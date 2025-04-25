{{-- Hidden Inventory Selector - Uses default inventory automatically --}}
@php
    $defaultInventory = \FriendsOfBotble\MultiInventory\Models\Inventory::where('is_default', true)->first();
    $inventoryId = $defaultInventory ? $defaultInventory->id : ($inventories->first()->id ?? null);
@endphp

<input type="hidden" name="inventory_id" value="{{ $inventoryId }}">

@if(setting('multi_inventory_stock_display', 'count') != 'hidden')
    <div class="inventory-info mb-3">
        @php
            $stockCount = $inventoryId ? $product->getStockByInventory($inventoryId) : 0;
        @endphp
        
        @if($stockCount > 0)
            @if(setting('multi_inventory_stock_display', 'count') == 'count')
                <span class="badge bg-success">{{ setting('multi_inventory_text_in_stock', trans('plugins/multi-inventory::multi-inventory.in_stock')) }} ({{ $stockCount }})</span>
            @else
                <span class="badge bg-success">{{ setting('multi_inventory_text_in_stock', trans('plugins/multi-inventory::multi-inventory.in_stock')) }}</span>
            @endif
        @else
            <span class="badge bg-danger">{{ setting('multi_inventory_text_out_of_stock', trans('plugins/multi-inventory::multi-inventory.out_of_stock')) }}</span>
        @endif
    </div>
@endif