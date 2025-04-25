{{-- Dropdown Style Inventory Selector --}}
<div class="inventory-selector">
    <label class="form-label">{{ trans('plugins/multi-inventory::multi-inventory.select_inventory') }}:</label>

    <div class="inventory-dropdown-selector">
        <select name="inventory_id" class="inventory-dropdown">
            @foreach($inventories as $inventory)
                <option 
                    value="{{ $inventory->id }}" 
                    data-stock="{{ $product->getStockByInventory($inventory->id) }}"
                    data-price="{{ $product->getPriceByInventory($inventory->id) }}"
                    data-delivery-time="{{ $inventory->delivery_time }}"
                    {{ session('selected_inventory_id') == $inventory->id || ($loop->first && !session('selected_inventory_id')) ? 'selected' : '' }}
                    {{ $product->getStockByInventory($inventory->id) <= 0 ? 'class=out-of-stock' : '' }}
                >
                    {{ $inventory->name }}
                    
                    @if(setting('multi_inventory_stock_display', 'count') != 'hidden')
                        @if($product->getStockByInventory($inventory->id) > 0)
                            @if(setting('multi_inventory_stock_display', 'count') == 'count')
                                - {{ setting('multi_inventory_text_in_stock', trans('plugins/multi-inventory::multi-inventory.in_stock')) }} ({{ $product->getStockByInventory($inventory->id) }})
                            @else
                                - {{ setting('multi_inventory_text_in_stock', trans('plugins/multi-inventory::multi-inventory.in_stock')) }}
                            @endif
                        @else
                            - {{ setting('multi_inventory_text_out_of_stock', trans('plugins/multi-inventory::multi-inventory.out_of_stock')) }}
                        @endif
                    @endif
                </option>
            @endforeach
        </select>
        
        <div class="inventory-dropdown-info mt-2">
            {{-- This will be populated by JavaScript --}}
        </div>
    </div>
</div>