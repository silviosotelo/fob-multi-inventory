{{-- resources/views/partials/text-display.blade.php --}}

<div class="multi-inventory-text">
    @if($onlySelected)
        @php
            $selectedInventoryId = session('selected_inventory_id', $defaultInventoryId);
            $selectedInventory = $inventories->firstWhere('id', $selectedInventoryId);
            $stock = isset($inventoriesStock[$selectedInventoryId]) ? $inventoriesStock[$selectedInventoryId] : 0;
        @endphp
        
        @if($selectedInventory)
            <div class="multi-inventory-text-inventory">
                <div class="multi-inventory-text-inventory-name">
                    {{ $selectedInventory->name }}
                </div>
                
                <div class="multi-inventory-text-inventory-stock {{ $stock > 0 ? 'multi-inventory-text-inventory-stock-in' : 'multi-inventory-text-inventory-stock-out' }}">
                    @if($stock > 0)
                        @if(setting('multi_inventory_stock_display', 'count') == 'count')
                            {{ trans('plugins/multi-inventory::multi-inventory.in_stock') }} ({{ $stock }})
                        @else
                            {{ trans('plugins/multi-inventory::multi-inventory.in_stock') }}
                        @endif
                    @else
                        {{ trans('plugins/multi-inventory::multi-inventory.out_of_stock') }}
                    @endif
                </div>
                
                @if($selectedInventory->delivery_time)
                    <div class="multi-inventory-text-inventory-delivery">
                        {{ $selectedInventory->delivery_time }}
                    </div>
                @endif
            </div>
            
            <div class="text-center mt-2">
                <button type="button" class="btn btn-sm btn-outline-secondary multi-inventory-open-popup" data-product-id="{{ $product->id }}">
                    {{ trans('plugins/multi-inventory::multi-inventory.change_inventory') }}
                </button>
            </div>
        @endif
    @else
        @foreach($inventories as $inventory)
            @php
                $stock = isset($inventoriesStock[$inventory->id]) ? $inventoriesStock[$inventory->id] : 0;
            @endphp
            
            <div class="multi-inventory-text-inventory">
                <div class="multi-inventory-text-inventory-name">
                    {{ $inventory->name }}
                </div>
                
                <div class="multi-inventory-text-inventory-stock {{ $stock > 0 ? 'multi-inventory-text-inventory-stock-in' : 'multi-inventory-text-inventory-stock-out' }}">
                    @if($stock > 0)
                        @if(setting('multi_inventory_stock_display', 'count') == 'count')
                            {{ trans('plugins/multi-inventory::multi-inventory.in_stock') }} ({{ $stock }})
                        @else
                            {{ trans('plugins/multi-inventory::multi-inventory.in_stock') }}
                        @endif
                    @else
                        {{ trans('plugins/multi-inventory::multi-inventory.out_of_stock') }}
                    @endif
                </div>
                
                @if($inventory->delivery_time)
                    <div class="multi-inventory-text-inventory-delivery">
                        {{ $inventory->delivery_time }}
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>