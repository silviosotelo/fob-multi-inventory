{{-- resources/views/partials/popup-locations.blade.php --}}

@foreach($inventories as $inventory)
    <div class="multi-inventory-popup-locations-location multi-inventory-popup-all-locations-location multi-inventory-popup-all-locations-location-{{ $inventory->id }}" 
        data-id="{{ $inventory->id }}" 
        data-name="{{ $inventory->name }}"
        data-lat="{{ $inventory->latitude }}" 
        data-lng="{{ $inventory->longitude }}">
        
        <div class="multi-inventory-popup-locations-location-name">
            <h5>{{ $inventory->name }}</h5>
        </div>
        
        <div class="multi-inventory-popup-locations-location-address">
            {{ $inventory->address }}<br>
            {{ $inventory->city }}, {{ $inventory->state }} {{ $inventory->zip_code }}<br>
            {{ $inventory->country }}
        </div>
        
        @if($inventory->phone || $inventory->email)
        <div class="multi-inventory-popup-locations-location-contact">
            @if($inventory->phone)
            <div><i class="fa fa-phone"></i> {{ $inventory->phone }}</div>
            @endif
            @if($inventory->email)
            <div><i class="fa fa-envelope"></i> {{ $inventory->email }}</div>
            @endif
        </div>
        @endif
        
        @if($inventory->delivery_time)
        <div class="multi-inventory-popup-locations-location-delivery-time">
            <i class="fa fa-truck"></i> {{ $inventory->delivery_time }}
        </div>
        @endif
        
        @if(isset($inventory->distance))
        <div class="multi-inventory-popup-locations-location-distance" style="display: block;">
            <i class="fa fa-map-marker"></i> 
            <span class="multi-inventory-popup-locations-location-distance-value">{{ $inventory->distance }}</span> 
            {{ setting('multi_inventory_popup_miles', false) ? 'miles' : 'km' }}
        </div>
        @endif
        
        @if(isset($product) && $product)
            <div class="multi-inventory-popup-locations-location-stock">
                @php
                    $stock = $product->getStockByInventory($inventory->id);
                @endphp
                
                @if($stock > 0)
                    <span class="badge bg-success">
                        {{ setting('multi_inventory_stock_display', 'count') == 'count' ? 
                            trans('plugins/multi-inventory::multi-inventory.in_stock') . ' (' . $stock . ')' : 
                            trans('plugins/multi-inventory::multi-inventory.in_stock') }}
                    </span>
                @else
                    <span class="badge bg-danger">
                        {{ trans('plugins/multi-inventory::multi-inventory.out_of_stock') }}
                    </span>
                @endif
            </div>
        @endif
        
        <button type="button" class="btn btn-sm btn-primary multi-inventory-choose-location" data-id="{{ $inventory->id }}" data-name="{{ $inventory->name }}">
            {{ trans('plugins/multi-inventory::multi-inventory.select') }}
        </button>
    </div>
@endforeach