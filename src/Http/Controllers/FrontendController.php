<?php

namespace FriendsOfBotble\MultiInventory\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Theme;

class FrontendController extends BaseController
{
    /**
     * Display inventory locations
     */
    public function index()
    {
        page_title()->setTitle(__('Our Locations'));

        $inventories = Inventory::where('status', 'published')
            ->where('is_frontend', true)
            ->orderBy('order_priority', 'desc')
            ->get();

        return Theme::scope('multi-inventory.inventories', compact('inventories'))->render();
    }

    /**
     * Set the selected inventory in session
     */
    public function setSelectedInventory(Request $request, BaseHttpResponse $response)
    {
        $inventoryId = $request->input('inventory_id');
        $context = $request->input('context', 'frontend');
        
        if ($inventoryId) {
            $sessionKey = $context === 'backend' 
                ? 'selected_backend_inventory_id' 
                : 'selected_inventory_id';
            
            Session::put($sessionKey, $inventoryId);
        }
        
        return $response
            ->setData(['success' => true])
            ->setMessage(__('Inventory selected successfully'));
    }

    /**
     * Get inventory information for a product
     */
    public function getProductInventory($productId, $inventoryId, BaseHttpResponse $response)
    {
        $inventory = Inventory::where('status', 'published')
            ->where('is_frontend', true)
            ->find($inventoryId);
            
        if (!$inventory) {
            return $response
                ->setError()
                ->setMessage(__('Inventory not found'))
                ->setCode(404);
        }
        
        $product = app(\Botble\Ecommerce\Repositories\Interfaces\ProductInterface::class)->findById($productId);
        
        if (!$product) {
            return $response
                ->setError()
                ->setMessage(__('Product not found'))
                ->setCode(404);
        }
        
        // Get stock and price
        $stock = $product->getStockByInventory($inventoryId);
        $price = $product->getPriceByInventory($inventoryId);
        
        if ($price === null || $price == 0) {
            $price = $product->price;
        }
        
        return $response->setData([
            'inventory' => $inventory,
            'stock' => $stock,
            'price' => $price,
            'price_formatted' => format_price($price),
            'in_stock' => $stock > 0,
            'stock_label' => $stock > 0 
                ? setting('multi_inventory_text_in_stock', __('In Stock')) 
                : setting('multi_inventory_text_out_of_stock', __('Out of Stock')),
            'stock_display' => setting('multi_inventory_stock_display', 'count')
        ]);
    }
}