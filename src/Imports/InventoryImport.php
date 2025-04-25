<?php

namespace FriendsOfBotble\MultiInventory\Imports;

use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Models\InventoryProduct;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InventoryImport implements ToCollection, WithHeadingRow
{
    protected $inventories;
    
    public function __construct()
    {
        $this->inventories = Inventory::where('status', 'published')->get()->keyBy('name');
    }
    
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $productId = $row['id'] ?? null;
            $sku = $row['sku'] ?? null;
            
            if (!$productId && !$sku) {
                continue;
            }
            
            $product = null;
            
            if ($productId) {
                $product = Product::find($productId);
            } elseif ($sku) {
                $product = Product::where('sku', $sku)->first();
            }
            
            if (!$product) {
                continue;
            }
            
            $totalStock = 0;
            
            foreach ($this->inventories as $inventoryName => $inventory) {
                $stockKey = strtolower(str_replace(' ', '_', $inventoryName)) . '_stock';
                $priceKey = strtolower(str_replace(' ', '_', $inventoryName)) . '_price';
                
                $stock = isset($row[$stockKey]) ? (int) $row[$stockKey] : 0;
                $price = isset($row[$priceKey]) && !empty($row[$priceKey]) ? (float) $row[$priceKey] : null;
                
                $totalStock += $stock;
                
                $inventoryProduct = InventoryProduct::firstOrNew([
                    'inventory_id' => $inventory->id,
                    'product_id' => $product->id,
                ]);
                
                $inventoryProduct->stock = $stock;
                
                if ($price !== null) {
                    $inventoryProduct->price = $price;
                }
                
                $inventoryProduct->save();
            }
            
            // Actualizar el stock total en la tabla de productos
            $product->stock = $totalStock;
            $product->save();
        }
    }
}