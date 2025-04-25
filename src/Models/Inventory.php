<?php

namespace FriendsOfBotble\MultiInventory\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Product;

class Inventory extends BaseModel
{
    protected $table = 'mi_inventories';

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'phone',
        'email',
        'latitude',
        'longitude',
        'status',
        'is_default',
        'is_frontend',
        'is_backend',
        'delivery_time',
        'order_priority',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
        'address' => SafeContent::class,
        'city' => SafeContent::class,
        'state' => SafeContent::class,
        'country' => SafeContent::class,
        'zip_code' => SafeContent::class,
        'phone' => SafeContent::class,
        'email' => SafeContent::class,
        'is_default' => 'boolean',
        'is_frontend' => 'boolean',
        'is_backend' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'mi_inventory_products', 'inventory_id', 'product_id')
            ->withPivot(['stock', 'price'])
            ->withTimestamps();
    }
}