<?php

namespace FriendsOfBotble\MultiInventory\Http\Requests;

use Botble\Base\Forms\FormRequest;
if (!defined('INVENTORY_MODULE_SCREEN_NAME')) {
    define('INVENTORY_MODULE_SCREEN_NAME', 'multi-inventory');
}
class MultiInventorySettingRequest extends FormRequest
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
}