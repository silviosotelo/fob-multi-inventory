<?php

namespace FriendsOfBotble\MultiInventory\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class InventoryRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:400',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:60',
            'phone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
            'is_frontend' => 'boolean',
            'is_backend' => 'boolean',
            'delivery_time' => 'nullable|string|max:255',
            'order_priority' => 'nullable|integer',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}