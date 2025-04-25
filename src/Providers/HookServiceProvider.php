<?php

namespace FriendsOfBotble\MultiInventory\Providers;

use Botble\Theme\Facades\Theme;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action(RENDERING_THEME_OPTIONS_PAGE, [$this, 'addThemeOptions'], 55);
        
        if (setting('multi_inventory_display_type', 'radio') !== 'hidden') {
            add_action('theme.ecommerce.product.single.before-add-to-cart-button', [$this, 'registerInventorySelector'], 15);
        }
        
        add_filter('ecommerce_product_price_html', [$this, 'modifyPriceBasedOnInventory'], 15, 2);
        
        add_filter('ecommerce_before_product_add_to_cart', [$this, 'validateInventoryBeforeAddToCart'], 15, 3);
    }
    
    public function addThemeOptions(): void
    {
        // Agregar opciones del tema si es necesario
    }
    
    public function registerInventorySelector(): void
    {
        $product = Theme::getData('product');
        
        if (!$product) {
            return;
        }
        
        // Solo para productos con stock gestionado
        if (!$product->isStockManaged()) {
            return;
        }
        
        $inventories = Inventory::where('status', 'published')
            ->where('is_frontend', true)
            ->orderBy('order_priority', 'desc')
            ->get();
            
        if ($inventories->isEmpty()) {
            return;
        }
        
        echo view('plugins/multi-inventory::partials.select-inventory', compact('product', 'inventories'))->render();
    }
    
    public function modifyPriceBasedOnInventory($html, $product)
    {
        if (!setting('multi_inventory_modify_stock_quantity', false)) {
            return $html;
        }
        
        $selectedInventoryId = session('selected_inventory_id');
        
        if (!$selectedInventoryId) {
            return $html;
        }
        
        $inventoryPrice = $product->getPriceByInventory($selectedInventoryId);
        
        if ($inventoryPrice && $inventoryPrice != $product->price) {
            return format_price($inventoryPrice);
        }
        
        return $html;
    }
    
    public function validateInventoryBeforeAddToCart($error, $product, $qty)
    {
        if (!setting('multi_inventory_inventory_required', true)) {
            return $error;
        }
        
        $selectedInventoryId = request()->input('inventory_id') ?: session('selected_inventory_id');
        
        if (!$selectedInventoryId) {
            return trans('plugins/multi-inventory::multi-inventory.no_inventory_selected');
        }
        
        $stock = $product->getStockByInventory($selectedInventoryId);
        
        if ($stock < $qty && !$product->isBackordered()) {
            return trans('plugins/multi-inventory::multi-inventory.not_enough_stock', [
                'quantity' => $stock,
                'inventory' => Inventory::find($selectedInventoryId)->name,
            ]);
        }
        
        return $error;
    }
}