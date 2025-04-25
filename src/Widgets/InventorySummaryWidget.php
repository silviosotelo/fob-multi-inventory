<?php

namespace FriendsOfBotble\MultiInventory\Widgets;

use Botble\Base\Widgets\Card;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Models\InventoryProduct;

class InventorySummaryWidget extends Card
{
    public function getOptions(): array
    {
        $inventories = Inventory::where('status', 'published')->count();
        
        $lowStockProducts = InventoryProduct::where('stock', '<', 5)->where('stock', '>', 0)->count();
        
        return [
            'color' => 'info',
            'title' => trans('plugins/multi-inventory::multi-inventory.widget_title'),
            'value' => $inventories,
            'label' => trans('plugins/multi-inventory::multi-inventory.total_inventories'),
            'icon' => 'fas fa-warehouse',
            'route' => route('multi-inventory.inventories.index'),
            'extra' => [
                [
                    'label' => trans('plugins/multi-inventory::multi-inventory.products_low_stock'),
                    'value' => $lowStockProducts,
                ],
            ],
        ];
    }
}