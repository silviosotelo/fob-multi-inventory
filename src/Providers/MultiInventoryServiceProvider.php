<?php

namespace FriendsOfBotble\MultiInventory\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class MultiInventoryServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(MultiInventoryInterface::class, function () {
            return new MultiInventory();
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
                    $form->addAfter('images', 'inventories', 'inventorySelection', [
                        'label' => trans('plugins/multi-inventory::multi-inventory.inventories'),
                        'label_attr' => ['class' => 'control-label'],
                        'choices' => $this->getInventoriesForForm(),
                    ]);
                }
                
                return $form;
            }, 124, 2);
        });
    }

    protected function getInventoriesForForm()
    {
        $inventories = app(InventoryInterface::class)->all();
        $choices = [];
        
        foreach ($inventories as $inventory) {
            $choices[$inventory->id] = $inventory->name;
        }
        
        return $choices;
    }
}