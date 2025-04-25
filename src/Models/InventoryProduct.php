<?php

namespace FriendsOfBotble\MultiInventory\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Product;

class InventoryProduct extends BaseModel
{
    protected $table = 'mi_inventory_products';

    protected $fillable = [
        'inventory_id',
        'product_id',
        'stock',
        'price',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}