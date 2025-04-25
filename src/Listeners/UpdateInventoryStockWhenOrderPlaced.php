<?php

namespace FriendsOfBotble\MultiInventory\Listeners;

use Botble\Ecommerce\Events\OrderPlacedEvent;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use Illuminate\Support\Facades\Session;

class UpdateInventoryStockWhenOrderPlaced
{
    public function handle(OrderPlacedEvent $event): void
    {
        $order = $event->order;
        
        $selectedInventoryId = Session::get('selected_inventory_id', 
            Inventory::where('is_default', true)->first()?->id
        );
        
        if (!$selectedInventoryId) {
            return;
        }
        
        foreach ($order->products as $orderProduct) {
            $product = $orderProduct->product;
            
            if ($product) {
                $product->updateInventoryStock(
                    $selectedInventoryId, 
                    $orderProduct->qty
                );
            }
        }
    }
}