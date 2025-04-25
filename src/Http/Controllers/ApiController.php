<?php

namespace FriendsOfBotble\MultiInventory\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Models\InventoryProduct;
use Illuminate\Http\Request;

class ApiController extends BaseController
{
    public function getInventories(Request $request, BaseHttpResponse $response)
    {
        $inventories = Inventory::where('status', 'published');
        
        if ($request->input('frontend_only')) {
            $inventories->where('is_frontend', true);
        }
        
        if ($request->input('backend_only')) {
            $inventories->where('is_backend', true);
        }
        
        return $response->setData($inventories->get());
    }
    
    public function getInventory($id, BaseHttpResponse $response)
    {
        $inventory = Inventory::where('status', 'published')->find($id);
        
        if (!$inventory) {
            return $response
                ->setError()
                ->setCode(404)
                ->setMessage('Inventory not found');
        }
        
        return $response->setData($inventory);
    }
    
    public function getProductInventory($id, $inventoryId, BaseHttpResponse $response)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return $response
                ->setError()
                ->setCode(404)
                ->setMessage('Product not found');
        }
        
        $inventory = Inventory::where('status', 'published')->find($inventoryId);
        
        if (!$inventory) {
            return $response
                ->setError()
                ->setCode(404)
                ->setMessage('Inventory not found');
        }
        
        $inventoryProduct = InventoryProduct::where('product_id', $id)
            ->where('inventory_id', $inventoryId)
            ->first();
            
        if (!$inventoryProduct) {
            return $response
                ->setError()
                ->setCode(404)
                ->setMessage('Product not found in this inventory');
        }
        
        return $response->setData([
            'inventory' => $inventory,
            'product' => $product,
            'stock' => $inventoryProduct->stock,
            'price' => $inventoryProduct->price ?: $product->price,
        ]);
    }
    
    public function getProductInventories($id, BaseHttpResponse $response)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return $response
                ->setError()
                ->setCode(404)
                ->setMessage('Product not found');
        }
        
        $inventories = $product->inventories()->where('status', 'published')->get();
        
        return $response->setData($inventories);
    }
}