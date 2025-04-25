<?php

namespace FriendsOfBotble\MultiInventory\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Models\InventoryProduct;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function updateInventory($id, Request $request, BaseHttpResponse $response)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.not_found'));
        }
        
        $inventories = $request->input('inventories', []);
        
        foreach ($inventories as $inventoryId => $data) {
            $inventory = Inventory::find($inventoryId);
            
            if (!$inventory) {
                continue;
            }
            
            $stock = isset($data['stock']) ? (int) $data['stock'] : 0;
            $price = isset($data['price']) && !empty($data['price']) ? (float) $data['price'] : null;
            
            $inventoryProduct = InventoryProduct::firstOrNew([
                'inventory_id' => $inventoryId,
                'product_id' => $product->id,
            ]);
            
            $inventoryProduct->stock = $stock;
            $inventoryProduct->price = $price;
            $inventoryProduct->save();
        }
        
        // Actualizar el stock total en la tabla de productos
        $totalStock = $product->inventories()->sum('stock');
        $product->stock = $totalStock;
        $product->save();
        
        return $response
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}