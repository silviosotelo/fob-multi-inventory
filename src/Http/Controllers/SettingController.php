<?php

namespace FriendsOfBotble\MultiInventory\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Supports\SettingStore;
use FriendsOfBotble\MultiInventory\Http\Requests\MultiInventorySettingRequest;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use Illuminate\Http\Request;

class SettingController extends BaseController
{
    public function index()
    {
        page_title()->setTitle(trans('plugins/multi-inventory::multi-inventory.settings'));

        // Obtener la lista de inventarios para el select
        $inventories = Inventory::where('status', 'published')
            ->pluck('name', 'id')
            ->toArray();

        return view('plugins/multi-inventory::settings', compact('inventories'));
    }

    public function update(MultiInventorySettingRequest $request, BaseHttpResponse $response, SettingStore $settingStore)
    {
        $settingStore
            ->set('multi_inventory_click_collect_enabled', $request->input('click_collect_enabled', false))
            ->set('multi_inventory_delivery_inventory_id', $request->input('delivery_inventory_id'))
            ->set('multi_inventory_modify_stock_quantity', $request->input('modify_stock_quantity', false))
            ->set('multi_inventory_reduce_stock_on_pending', $request->input('reduce_stock_on_pending', false))
            ->set('multi_inventory_inventory_required', $request->input('inventory_required', true))
            ->set('multi_inventory_display_type', $request->input('display_type', 'radio'))
            ->set('multi_inventory_order_flow', $request->input('order_flow', 'most_stock'))
            ->set('multi_inventory_stock_display', $request->input('stock_display', 'count'))
            ->set('multi_inventory_text_in_stock', $request->input('text_in_stock', 'In Stock'))
            ->set('multi_inventory_text_out_of_stock', $request->input('text_out_of_stock', 'Out of Stock'))
            ->set('multi_inventory_google_maps_api_key', $request->input('google_maps_api_key'));
            
        $settingStore->save();
        
        return $response
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
    
    public function setSelectedInventory(Request $request, BaseHttpResponse $response)
    {
        $inventoryId = $request->input('inventory_id');
        
        if ($inventoryId) {
            session(['selected_inventory_id' => $inventoryId]);
        }
        
        return $response->setMessage('Inventory selected successfully');
    }
}