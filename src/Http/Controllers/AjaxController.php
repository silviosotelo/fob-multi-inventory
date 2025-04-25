<?php

namespace FriendsOfBotble\MultiInventory\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Models\InventoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Theme;

class AjaxController extends BaseController
{
    /**
     * Update stock for a product in an inventory
     */
    public function updateStock(Request $request, BaseHttpResponse $response)
    {
        $productId = $request->input('product_id');
        $inventoryId = $request->input('inventory_id');
        $stock = (int) $request->input('stock', 0);
        
        if (!$productId || !$inventoryId) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/multi-inventory::multi-inventory.missing_product_or_inventory'))
                ->setCode(400);
        }
        
        $product = Product::find($productId);
        $inventory = Inventory::find($inventoryId);
        
        if (!$product || !$inventory) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/multi-inventory::multi-inventory.product_or_inventory_not_found'))
                ->setCode(404);
        }
        
        // Update or create inventory product relation
        $inventoryProduct = InventoryProduct::updateOrCreate(
            ['product_id' => $productId, 'inventory_id' => $inventoryId],
            ['stock' => max(0, $stock)]
        );
        
        // Update product total stock
        $totalStock = InventoryProduct::where('product_id', $productId)->sum('stock');
        $product->stock = $totalStock;
        $product->save();
        
        return $response
            ->setMessage(trans('plugins/multi-inventory::multi-inventory.stock_updated_successfully'));
    }
    
    /**
     * Get inventories for a product with location data
     */
    public function getInventories(Request $request, BaseHttpResponse $response)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $productId = $request->input('product_id');
        
        $inventoriesQuery = Inventory::where('status', 'published')
            ->where('is_frontend', true);
        
        // If product ID is provided, filter inventories that have stock
        if ($productId) {
            $product = Product::find($productId);
            
            if ($product) {
                $inventoriesQuery->whereHas('products', function ($query) use ($productId) {
                    $query->where('product_id', $productId)
                        ->where('stock', '>', 0);
                });
            }
        }
        
        $inventories = $inventoriesQuery->get();
        
        if ($inventories->isEmpty()) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/multi-inventory::multi-inventory.no_inventories_found'))
                ->setCode(404);
        }
        
        // Calculate distances if coordinates provided
        if ($lat && $lng) {
            foreach ($inventories as $inventory) {
                if ($inventory->latitude && $inventory->longitude) {
                    $inventory->distance = $this->calculateDistance(
                        $lat, 
                        $lng, 
                        $inventory->latitude, 
                        $inventory->longitude
                    );
                }
            }
            
            // Sort by distance
            $inventories = $inventories->sortBy('distance');
        } else {
            // Sort by priority
            $inventories = $inventories->sortByDesc('order_priority');
        }
        
        // Get first inventory ID for highlighting
        $firstInventoryId = $inventories->first() ? $inventories->first()->id : null;
        
        // Render inventories view
        $inventoriesHtml = view('plugins/multi-inventory::partials.popup-locations', [
            'inventories' => $inventories,
            'product' => $productId ? Product::find($productId) : null,
        ])->render();
        
        return $response->setData([
            'status' => true,
            'inventories_html' => $inventoriesHtml,
            'first_inventory' => $firstInventoryId,
        ]);
    }
    
    /**
     * Get stock information for a variation
     */
    public function getVariationStock(Request $request, BaseHttpResponse $response)
    {
        $variationId = $request->input('variation_id');
        
        if (!$variationId) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/multi-inventory::multi-inventory.missing_variation'))
                ->setCode(400);
        }
        
        $variation = Product::find($variationId);
        
        if (!$variation) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/multi-inventory::multi-inventory.variation_not_found'))
                ->setCode(404);
        }
        
        $inventories = Inventory::where('status', 'published')
            ->where('is_frontend', true)
            ->orderByDesc('order_priority')
            ->get();
            
        $inventoriesStock = [];
        $textHtml = '';
        
        foreach ($inventories as $inventory) {
            $stock = $variation->getStockByInventory($inventory->id);
            $inventoriesStock[$inventory->id] = $stock;
        }
        
        // Default inventory ID
        $defaultInventoryId = Inventory::where('is_default', true)->first()?->id;
        
        // Render text view if needed
        if (in_array(setting('multi_inventory_product_page_display', 'radio'), ['text', 'textOnlySelected'])) {
            $textHtml = view('plugins/multi-inventory::partials.text-display', [
                'inventories' => $inventories,
                'product' => $variation,
                'inventoriesStock' => $inventoriesStock,
                'defaultInventoryId' => $defaultInventoryId,
                'onlySelected' => setting('multi_inventory_product_page_display', 'radio') === 'textOnlySelected',
            ])->render();
        }
        
        return $response->setData([
            'status' => true,
            'inventories_stock' => $inventoriesStock,
            'text' => $textHtml,
        ]);
    }
    
    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = setting('multi_inventory_popup_miles', false) ? 3959 : 6371; // miles or km
        
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return round($angle * $earthRadius, 1);
    }
}