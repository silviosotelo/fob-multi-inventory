<?php
namespace FriendsOfBotble\MultiInventory\Widgets;

use Botble\Base\Widgets\Card;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Models\InventoryProduct;
use Botble\Ecommerce\Models\Product;

class InventorySummaryWidget extends Card
{
    public function getOptions(): array
    {
        // Total de inventarios publicados
        $totalInventories = Inventory::where('status', 'published')->count();
        
        // Productos con stock bajo
        $lowStockProducts = InventoryProduct::where('stock', '<', 5)
            ->where('stock', '>', 0)
            ->groupBy('inventory_id')
            ->select('inventory_id')
            ->get()
            ->count();
        
        // Valor total del inventario
        $totalInventoryValue = InventoryProduct::join('ec_products', 'mi_inventory_products.product_id', '=', 'ec_products.id')
            ->select(\DB::raw('SUM(mi_inventory_products.stock * ec_products.price) as total_value'))
            ->first()
            ->total_value ?? 0;
        
        // Inventarios con stock crítico
        $criticalInventories = Inventory::whereHas('products', function($query) {
            $query->where('stock', '<', 5);
        })->count();

        return [
            'color' => 'info',
            'title' => trans('plugins/multi-inventory::multi-inventory.widget_title'),
            'value' => $totalInventories,
            'label' => trans('plugins/multi-inventory::multi-inventory.total_inventories'),
            'icon' => 'fas fa-warehouse',
            'route' => route('multi-inventory.inventories.index'),
            'extra' => [
                [
                    'label' => trans('plugins/multi-inventory::multi-inventory.products_low_stock'),
                    'value' => $lowStockProducts,
                    'color' => $lowStockProducts > 0 ? 'warning' : 'success',
                ],
                [
                    'label' => trans('plugins/multi-inventory::multi-inventory.inventory_value'),
                    'value' => format_price($totalInventoryValue),
                ],
                [
                    'label' => trans('plugins/multi-inventory::multi-inventory.critical_inventories'),
                    'value' => $criticalInventories,
                    'color' => $criticalInventories > 0 ? 'danger' : 'success',
                ],
            ],
        ];
    }
}