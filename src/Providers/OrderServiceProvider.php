<?php

namespace FriendsOfBotble\MultiInventory\Providers;

use Botble\Ecommerce\Cart\CartItem;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Services\HandleCheckoutOrderService;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->extend(HandleCheckoutOrderService::class, function ($service, $app) {
            return new class($service) extends HandleCheckoutOrderService {
                protected $originalService;
                
                public function __construct($originalService)
                {
                    $this->originalService = $originalService;
                }
                
                public function execute($data, $cart)
                {
                    $originalMethod = $this->originalService;
                    
                    $result = $originalMethod->execute($data, $cart);
                    
                    if ($result instanceof \Botble\Ecommerce\Models\Order) {
                        $this->updateInventoryStock($result, $cart);
                    }
                    
                    return $result;
                }
                
                protected function updateInventoryStock($order, $cart)
                {
                    $selectedInventoryId = session('selected_inventory_id', 
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
                            
                            // Guardar el inventario seleccionado con el producto del pedido
                            $orderProduct->update([
                                'options' => array_merge($orderProduct->options ?: [], 
                                    ['inventory_id' => $selectedInventoryId]
                                ),
                            ]);
                        }
                    }
                }
            };
        });
        
        // Adaptar el carrrito para incluir el inventario seleccionado
        $this->app->resolving(CartItem::class, function (CartItem $cartItem, $app) {
            $originalGetOptions = $cartItem->getOptions();
            
            if ($originalGetOptions && isset($originalGetOptions['inventory_id'])) {
                return;
            }
            
            $selectedInventoryId = session('selected_inventory_id', 
                Inventory::where('is_default', true)->first()?->id
            );
            
            if ($selectedInventoryId) {
                $cartItem->setOptions(array_merge($cartItem->getOptions() ?: [], [
                    'inventory_id' => $selectedInventoryId,
                ]));
            }
        });
    }
}