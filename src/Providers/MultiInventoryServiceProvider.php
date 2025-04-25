<?php

namespace FriendsOfBotble\MultiInventory\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Traits\HasMultiInventory;
use FriendsOfBotble\MultiInventory\Repositories\Eloquent\InventoryRepository;
use FriendsOfBotble\MultiInventory\Repositories\Interfaces\InventoryInterface;
use FriendsOfBotble\MultiInventory\Repositories\Eloquent\InventoryProductRepository;
use FriendsOfBotble\MultiInventory\Repositories\Interfaces\InventoryProductInterface;
use Illuminate\Routing\Events\RouteMatched;

if (!defined('INVENTORY_MODULE_SCREEN_NAME')) {
    define('INVENTORY_MODULE_SCREEN_NAME', 'multi-inventory');
}

class MultiInventoryServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(MultiInventoryInterface::class, function () {
            return new MultiInventory();
        });
        // Registrar la interfaz y su implementación
        $this->app->bind(InventoryInterface::class, function () {
            return new InventoryRepository(new Inventory());
        });
        // Registrar la interfaz de InventoryProduct
        $this->app->bind(InventoryProductInterface::class, function () {
            return new InventoryProductRepository(new InventoryProduct());
        });
        //registrar el controlador del producto
        $this->app->bind('multi-inventory.product', function () {
            return new \FriendsOfBotble\MultiInventory\Http\Controllers\ProductController();
        });
    }



    public function boot(): void
    {
        $this
            ->setNamespace('plugins/multi-inventory')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web', 'api']);


        // Añadir el trait HasMultiInventory al modelo Product
        Product::resolveRelationUsing('inventories', function ($model) {
            return $model->belongsToMany(Inventory::class, 'mi_inventory_products', 'product_id', 'inventory_id')
                ->withPivot(['stock', 'price'])
                ->withTimestamps();
        });

        Product::macro('getStockByInventory', function ($inventoryId) {
            $inventoryProduct = $this->inventories()->where('inventory_id', $inventoryId)->first();
            return $inventoryProduct ? $inventoryProduct->pivot->stock : 0;
        });

        Product::macro('getPriceByInventory', function ($inventoryId) {
            $inventoryProduct = $this->inventories()->where('inventory_id', $inventoryId)->first();
            return $inventoryProduct && $inventoryProduct->pivot->price ? $inventoryProduct->pivot->price : $this->price;
        });

        Product::macro('updateInventoryStock', function ($inventoryId, $quantity, $operator = '-') {
            $inventoryProduct = $this->inventories()->where('inventory_id', $inventoryId)->first();

            if (!$inventoryProduct) {
                return false;
            }

            $stock = $inventoryProduct->pivot->stock;

            if ($operator === '-') {
                $newStock = max(0, $stock - $quantity);
            } else {
                $newStock = $stock + $quantity;
            }

            $this->inventories()->updateExistingPivot($inventoryId, ['stock' => $newStock]);

            // Actualizar el stock total
            $this->stock = $this->inventories()->sum('stock');
            $this->save();

            return true;
        });



        $this->app['events']->listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Inventory::class]);
            }

            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-plugins-multi-inventory',
                    'priority' => 5,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/multi-inventory::multi-inventory.name',
                    'icon' => 'fa fa-warehouse',
                    'url' => route('multi-inventory.index'),
                    'permissions' => ['multi-inventory.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-multi-inventory-list',
                    'priority' => 1,
                    'parent_id' => 'cms-plugins-multi-inventory',
                    'name' => 'plugins/multi-inventory::multi-inventory.inventories',
                    'icon' => null,
                    'url' => route('multi-inventory.inventories.index'),
                    'permissions' => ['multi-inventory.inventories.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-multi-inventory-settings',
                    'priority' => 2,
                    'parent_id' => 'cms-plugins-multi-inventory',
                    'name' => 'plugins/multi-inventory::multi-inventory.settings',
                    'icon' => null,
                    'url' => route('multi-inventory.settings'),
                    'permissions' => ['multi-inventory.settings'],
                ]);

            add_filter(BASE_FILTER_BEFORE_RENDER_FORM, function ($form, $data) {
                if (get_class($data) == Product::class && request()->segment(1) === BaseHelper::getAdminPrefix()) {
                    // Get product instance
                    $product = $data;

                    // Get all active inventories
                    $inventories = app(InventoryInterface::class)->getAllActive();

                    // Prepare inventory data for display
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

                    // Add HTML field with custom inventory table
                    $form->addAfter('images', 'inventories_management', 'html', [
                        'html' => view('plugins/multi-inventory::forms.inventory-product', [
                            'inventories' => $inventoryData,
                            'product' => $product,
                        ])->render(),
                    ]);
                }

                return $form;
            }, 124, 2);
        });
    }

    protected function getInventoriesForForm()
    {
        $inventories = app(InventoryInterface::class)->getAllActive();
        $choices = [];

        foreach ($inventories as $inventory) {
            $choices[$inventory->id] = $inventory->name;
        }

        return $choices;
    }
}
