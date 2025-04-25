<?php

namespace FriendsOfBotble\MultiInventory\Traits;

use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Models\InventoryProduct;

trait HasMultiInventory
{
    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'mi_inventory_products', 'product_id', 'inventory_id')
            ->withPivot(['stock', 'price'])
            ->withTimestamps();
    }
    
    public function getTotalStockAttribute()
    {
        return $this->inventories()->sum('stock');
    }
    
    public function getStockByInventory($inventoryId)
    {
        $inventoryProduct = InventoryProduct::where('product_id', $this->id)
            ->where('inventory_id', $inventoryId)
            ->first();
            
        return $inventoryProduct ? $inventoryProduct->stock : 0;
    }
    
    public function getPriceByInventory($inventoryId)
    {
        $inventoryProduct = InventoryProduct::where('product_id', $this->id)
            ->where('inventory_id', $inventoryId)
            ->first();
            
        return $inventoryProduct && $inventoryProduct->price ? $inventoryProduct->price : $this->price;
    }
    
    public function updateInventoryStock($inventoryId, $quantity, $operator = '-')
    {
        $inventoryProduct = InventoryProduct::where('product_id', $this->id)
            ->where('inventory_id', $inventoryId)
            ->first();
            
        if (!$inventoryProduct) {
            return false;
        }
        
        if ($operator === '-') {
            $inventoryProduct->stock -= $quantity;
        } else {
            $inventoryProduct->stock += $quantity;
        }
        
        $inventoryProduct->save();
        
        // Actualizar el stock total en la tabla de productos
        $totalStock = $this->getTotalStockAttribute();
        $this->stock = $totalStock;
        $this->save();
        
        return true;
    }
}