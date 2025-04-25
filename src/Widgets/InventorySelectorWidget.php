<?php

namespace FriendsOfBotble\MultiInventory\Widgets;

use Botble\Dashboard\Supports\DashboardWidget;
use FriendsOfBotble\MultiInventory\Models\Inventory;

class InventorySelectorWidget extends DashboardWidget
{
    public function render()
    {
        $inventories = Inventory::where('status', 'published')
            ->where('is_backend', true)
            ->orderBy('order_priority', 'desc')
            ->get();

        return view('plugins/multi-inventory::widgets.inventory-selector', [
            'inventories' => $inventories,
            'selectedInventory' => session('selected_backend_inventory_id')
        ]);
    }
}