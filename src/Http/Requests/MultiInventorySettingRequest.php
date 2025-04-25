<?php

namespace FriendsOfBotble\MultiInventory\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Support\Http\Requests\Request;

class MultiInventorySettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'click_collect_enabled' => 'nullable|boolean',
            'delivery_inventory_id' => 'nullable|exists:mi_inventories,id',
            'modify_stock_quantity' => 'nullable|boolean',
            'reduce_stock_on_pending' => 'nullable|boolean',
            'inventory_required' => 'nullable|boolean',
            'display_type' => 'required|in:radio,select,label,hidden',
            'order_flow' => 'required|in:custom,country,most_stock,lowest_stock,name,order',
            'stock_display' => 'required|in:count,inout,hidden',
            'text_in_stock' => 'required|string|max:255',
            'text_out_of_stock' => 'required|string|max:255',
            'google_maps_api_key' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [
            'click_collect_enabled' => trans('plugins/multi-inventory::multi-inventory.click_collect'),
            'delivery_inventory_id' => trans('plugins/multi-inventory::multi-inventory.delivery') . ' ' . trans('plugins/multi-inventory::multi-inventory.inventories'),
            'modify_stock_quantity' => trans('plugins/multi-inventory::multi-inventory.modify_stock_quantity'),
            'reduce_stock_on_pending' => trans('plugins/multi-inventory::multi-inventory.reduce_stock_on_pending'),
            'inventory_required' => trans('plugins/multi-inventory::multi-inventory.inventory_required'),
            'display_type' => trans('plugins/multi-inventory::multi-inventory.display_type'),
            'order_flow' => trans('plugins/multi-inventory::multi-inventory.order_flow'),
            'stock_display' => trans('plugins/multi-inventory::multi-inventory.stock_display'),
            'text_in_stock' => trans('plugins/multi-inventory::multi-inventory.text_in_stock'),
            'text_out_of_stock' => trans('plugins/multi-inventory::multi-inventory.text_out_of_stock'),
            'google_maps_api_key' => trans('plugins/multi-inventory::multi-inventory.google_maps_api_key'),
        ];
    }
}