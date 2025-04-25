{{-- Radio Button Style Inventory Selector --}}
<div class="inventory-selector">
    <label class="form-label">{{ trans('plugins/multi-inventory::multi-inventory.select_inventory') }}:</label>

    <div class="inventory-radio-selector">
        @foreach($inventories as $inventory)
            <div class="form-check">
                <input
                    class="form-check-input inventory-radio"
                    type="radio"
                    name="inventory_id"
                    id="inventory_{{ $inventory->id }}"
                    value="{{ $inventory->id }}"
                    data-stock="{{ $product->getStockByInventory($inventory->id) }}"
                    data-price="{{ $product->getPriceByInventory($inventory->id) }}"
                    {{ session('selected_inventory_id') == $inventory->id || ($loop->first && !session('selected_inventory_id')) ? 'checked' : '' }}>
                <label class="form-check-label" for="inventory_{{ $inventory->id }}">
                    {{ $inventory->name }}

                    @if(setting('multi_inventory_stock_display', 'count') != 'hidden')
                        @if($product->getStockByInventory($inventory->id) > 0)
                            @if(setting('multi_inventory_stock_display', 'count') == 'count')
                                <span class="badge bg-success">{{ setting('multi_inventory_text_in_stock', trans('plugins/multi-inventory::multi-inventory.in_stock')) }} ({{ $product->getStockByInventory($inventory->id) }})</span>
                            @else
                                <span class="badge bg-success">{{ setting('multi_inventory_text_in_stock', trans('plugins/multi-inventory::multi-inventory.in_stock')) }}</span>
                            @endif
                        @else
                            <span class="badge bg-danger">{{ setting('multi_inventory_text_out_of_stock', trans('plugins/multi-inventory::multi-inventory.out_of_stock')) }}</span>
                        @endif
                    @endif
                </label>

                <div class="inventory-info">
                    @if($product->getPriceByInventory($inventory->id) != $product->price)
                        <span class="inventory-specific-price">
                            {{ format_price($product->getPriceByInventory($inventory->id)) }}
                        </span>
                    @endif

                    @if($inventory->delivery_time)
                        <span class="delivery-time">
                            ({{ $inventory->delivery_time }})
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>