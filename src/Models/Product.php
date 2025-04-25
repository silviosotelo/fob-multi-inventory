<?php
namespace Botble\Ecommerce\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    // Método existente o nuevo para relación con inventarios
    public function inventoryProducts(): HasMany
    {
        return $this->hasMany(InventoryProduct::class, 'product_id');
    }

    // Método para obtener stock total
    public function getTotalStockAttribute()
    {
        return $this->inventoryProducts()->sum('stock');
    }
}