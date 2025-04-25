{{-- resources/views/partials/inventory-popup.blade.php --}}

<div id="multi-inventory-overlay" class="multi-inventory-overlay"></div>

<div id="multi-inventory-popup-container" class="multi-inventory-popup-container">
    <div class="multi-inventory-popup">
        <div class="multi-inventory-popup-header">
            <h3>{{ trans('plugins/multi-inventory::multi-inventory.select_inventory') }}</h3>
            <div class="multi-inventory-popup-close-container">
                <span class="multi-inventory-popup-close">&times;</span>
            </div>
        </div>

        <div class="multi-inventory-popup-content">
            @if(setting('multi_inventory_popup_search_enabled', true))
            <div class="multi-inventory-popup-search-container">
                <div class="multi-inventory-popup-search-input-container">
                    <input type="text" class="multi-inventory-popup-address form-control" placeholder="{{ trans('plugins/multi-inventory::multi-inventory.enter_address') }}">
                    <button type="button" class="multi-inventory-popup-address-button btn btn-primary">
                        <i class="fa fa-search"></i> {{ trans('plugins/multi-inventory::multi-inventory.search') }}
                    </button>
                </div>
            </div>
            @endif

            <div class="multi-inventory-popup-locations-container">
                <div class="multi-inventory-popup-locations-nearest-location-container">
                    <h4>{{ trans('plugins/multi-inventory::multi-inventory.nearest_location') }}</h4>
                    <div class="multi-inventory-popup-locations-nearest-location-loader">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ trans('plugins/multi-inventory::multi-inventory.loading') }}</span>
                        </div>
                    </div>
                    <div class="multi-inventory-popup-locations-nearest-location-error alert alert-warning">
                        {{ trans('plugins/multi-inventory::multi-inventory.location_error') }}
                    </div>
                    <div class="multi-inventory-popup-locations-nearest-location">
                        {{-- Will be populated via JavaScript --}}
                    </div>
                </div>

                <div class="multi-inventory-popup-all-locations-container">
                    <h4>{{ trans('plugins/multi-inventory::multi-inventory.all_locations') }}</h4>
                    <div class="multi-inventory-popup-locations">
                        {{-- Will be populated via JavaScript or AJAX --}}
                        @if(!empty($inventories))
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
                                    
                                    <div class="multi-inventory-popup-locations-location-distance">
                                        <i class="fa fa-map-marker"></i> 
                                        <span class="multi-inventory-popup-locations-location-distance-value"></span> 
                                        {{ setting('multi_inventory_popup_miles', false) ? 'miles' : 'km' }}
                                    </div>
                                    
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
                        @endif
                    </div>
                </div>
                
                @if(setting('multi_inventory_delivery_enabled', true))
                <div class="multi-inventory-popup-locations-delivery-location-container">
                    <h4>{{ trans('plugins/multi-inventory::multi-inventory.delivery') }}</h4>
                    <div class="multi-inventory-popup-locations-location multi-inventory-popup-locations-delivery-location">
                        <div class="multi-inventory-popup-locations-location-name">
                            <h5>{{ trans('plugins/multi-inventory::multi-inventory.delivery_to_address') }}</h5>
                        </div>
                        
                        <div class="multi-inventory-popup-locations-location-description">
                            {{ trans('plugins/multi-inventory::multi-inventory.delivery_description') }}
                        </div>
                        
                        @php
                            $defaultInventory = \FriendsOfBotble\MultiInventory\Models\Inventory::where('is_default', true)->first();
                            $deliveryInventoryId = setting('multi_inventory_delivery_inventory_id', $defaultInventory ? $defaultInventory->id : null);
                        @endphp
                        
                        @if($deliveryInventoryId)
                            <button type="button" class="btn btn-sm btn-primary multi-inventory-choose-location" 
                                data-id="{{ $deliveryInventoryId }}" 
                                data-name="{{ trans('plugins/multi-inventory::multi-inventory.delivery') }}">
                                {{ trans('plugins/multi-inventory::multi-inventory.select') }}
                            </button>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>