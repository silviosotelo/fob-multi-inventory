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
        
        // Hook para la página de edición de productos
        add_action(BASE_ACTION_META_BOXES, [$this, 'registerProductInventoryBox'], 50, 3);
        
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
        
        $displayType = setting('multi_inventory_display_type', 'radio');
        
        echo view('plugins/multi-inventory::partials.selectors.' . $displayType, compact('product', 'inventories'))->render();
    }
    
    public function registerProductInventoryBox($priority, $data, $screen)
    {
        if ($data instanceof Product && in_array($screen, ['products.create', 'products.edit'])) {
            add_meta_box(
                'product_inventory_wrap',
                trans('plugins/multi-inventory::multi-inventory.inventory_management'),
                [$this, 'productInventoryMetaField'],
                $screen,
                'advanced',
                'default'
            );
        }
    }

    public function productInventoryMetaField($context)
    {
        $product = $context->getModel();
        
        if (!$product) {
            return '';
        }
        
        $inventories = Inventory::where('status', 'published')->get();
        
        if ($inventories->isEmpty()) {
            return view('plugins/multi-inventory::admin.no-inventories')->render();
        }
        
        $inventoryData = [];
        
        foreach ($inventories as $inventory) {
            $inventoryProduct = $product->inventories()->where('inventory_id', $inventory->id)->first();
            
            $inventoryData[] = [
                'id' => $inventory->id,
                'name' => $inventory->name,
                'stock' => $inventoryProduct ? $inventoryProduct->pivot->stock : 0,
                'price' => $inventoryProduct ? $inventoryProduct->pivot->price : null,
            ];
        }
        
        return view('plugins/multi-inventory::forms.inventory-product', [
            'inventories' => $inventoryData,
            'product' => $product,
        ])->render();
    }
}