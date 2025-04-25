<?php

namespace FriendsOfBotble\MultiInventory\Listeners;

use Botble\Ecommerce\Events\OrderStatusChanged;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use Illuminate\Support\Facades\Log;

class UpdateInventoryStockWhenOrderStatusChanged
{
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        
        $reducingStatuses = ['processing', 'completed'];
        $increasingStatuses = ['cancelled'];
        
        if (in_array($order->status, $reducingStatuses) && !$order->stock_reduced) {
            foreach ($order->products as $orderProduct) {
                $product = $orderProduct->product;
                
                if (!$product) {
                    continue;
                }
                
                $options = $orderProduct->options ? $orderProduct->options : [];
                $inventoryId = $options['inventory_id'] ?? null;
                
                if (!$inventoryId) {
                    // Utilizar el inventario predeterminado
                    $inventoryId = Inventory::where('is_default', true)->first()?->id;
                }
                
                if ($inventoryId) {
                    $product->updateInventoryStock(
                        $inventoryId, 
                        $orderProduct->qty
                    );
                }
            }
            
            $order->stock_reduced = true;
            $order->save();
            
        } elseif (in_array($order->status, $increasingStatuses) && $order->stock_reduced) {
            foreach ($order->products as $orderProduct) {
                $product = $orderProduct->product;
                
                if (!$product) {
                    continue;
                }
                
                $options = $orderProduct->options ? $orderProduct->options : [];
                $inventoryId = $options['inventory_id'] ?? null;
                
                if (!$inventoryId) {
                    // Utilizar el inventario predeterminado
                    $inventoryId = Inventory::where('is_default', true)->first()?->id;
                }
                
                if ($inventoryId) {
                    $product->updateInventoryStock(
                        $inventoryId, 
                        $orderProduct->qty,
                        '+'
                    );
                }
            }
            
            $order->stock_reduced = false;
            $order->save();
        }
    }
}