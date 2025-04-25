{{-- Label Style Inventory Selector --}}
<div class="inventory-selector">
    <label class="form-label">{{ trans('plugins/multi-inventory::multi-inventory.select_inventory') }}:</label>

    <div class="inventory-label-selector">
        @foreach($inventories as $inventory)
            <div 
                class="inventory-label {{ session('selected_inventory_id') == $inventory->id || ($loop->first && !session('selected_inventory_id')) ? 'selected' : '' }}"
                data-inventory="{{ $inventory->id }}"
                data-stock="{{ $product->getStockByInventory($inventory->id) }}"
                data-price="{{ $product->getPriceByInventory($inventory->id) }}"
            >
                <div class="inventory-name">{{ $inventory->name }}</div>
                
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
                
                @if($product->getPriceByInventory($inventory->id) != $product->price)
                    <span class="price">{{ format_price($product->getPriceByInventory($inventory->id)) }}</span>
                @endif
                
                @if($inventory->delivery_time)
                    <span class="delivery-time">{{ $inventory->delivery_time }}</span>
                @endif
            </div>
        @endforeach
    </div>
    
    <input type="hidden" name="inventory_id" id="inventory_id_input" value="{{ session('selected_inventory_id') ?: ($inventories->first()->id ?? '') }}">
</div>