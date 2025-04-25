<?php

namespace FriendsOfBotble\MultiInventory\Exports;

use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Models\InventoryProduct;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with(['inventories'])->get();
    }
    
    public function headings(): array
    {
        $headings = [
            'ID',
            'SKU',
            'Name',
            'Total Stock',
        ];
        
        $inventories = Inventory::where('status', 'published')->get();
        
        foreach ($inventories as $inventory) {
            $headings[] = $inventory->name . ' (Stock)';
            
            if (setting('multi_inventory_inventoryPrices', false)) {
                $headings[] = $inventory->name . ' (Price)';
            }
        }
        
        return $headings;
    }
    
    public function map($product): array
    {
        $row = [
            'ID' => $product->id,
            'SKU' => $product->sku,
            'Name' => $product->name,
            'Total Stock' => $product->stock,
        ];
        
        $inventories = Inventory::where('status', 'published')->get();
        
        foreach ($inventories as $inventory) {
            $inventoryProduct = InventoryProduct::where('product_id', $product->id)
                ->where('inventory_id', $inventory->id)
                ->first();
                
            $row[$inventory->name . ' (Stock)'] = $inventoryProduct ? $inventoryProduct->stock : 0;
            
            if (setting('multi_inventory_inventoryPrices', false)) {
                $row[$inventory->name . ' (Price)'] = $inventoryProduct && $inventoryProduct->price ? $inventoryProduct->price : $product->price;
            }
        }
        
        return $row;
    }
}