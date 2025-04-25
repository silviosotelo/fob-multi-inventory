<?php

namespace FriendsOfBotble\MultiInventory\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\MultiInventory\Models\Inventory;

class InventoryProductForm extends FormAbstract
{
    protected $template = 'plugins/multi-inventory::forms.inventory-product';

    public function buildForm(): void
    {
        $inventories = Inventory::where('status', 'published')->get();
        $product = $this->getModel();
        
        $this->setFormOptions([
            'url' => route('multi-inventory.product.update-inventory', $product->id),
        ]);
        
        $this->setupModel(new Product);
        
        $inventoryData = [];
        
        foreach ($inventories as $inventory) {
            $inventoryProduct = $product->inventories()->where('inventory_id', $inventory->id)->first();
            
            $inventoryData[] = [
                'id' => $inventory->id,
                'name' => $inventory->name,
                'stock' => $inventoryProduct ? $inventoryProduct->pivot->stock : 0,
                'price' => $inventoryProduct ? $inventoryProduct->pivot->price : null,
            ];
        }
        
        $this->add('product_id', 'hidden', [
            'value' => $product->id,
        ])
        ->add('inventories', 'html', [
            'html' => view('plugins/multi-inventory::forms.inventory-product', [
                'inventories' => $inventoryData,
                'product' => $product,
            ])->render(),
        ]);
    }
}