@extends(Theme::getThemeNamespace() . '::layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-title my-4">{{ __('Our Locations') }}</h1>
                
                <div class="row mb-4">
                    @foreach($inventories as $inventory)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $inventory->name }}</h5>
                                    <address class="mb-3">
                                        {{ $inventory->address }}<br>
                                        {{ $inventory->city }}, {{ $inventory->state }} {{ $inventory->zip_code }}<br>
                                        {{ $inventory->country }}
                                    </address>
                                    
                                    @if($inventory->phone)
                                        <p><i class="icon icon-phone"></i> {{ $inventory->phone }}</p>
                                    @endif
                                    
                                    @if($inventory->email)
                                        <p><i class="icon icon-envelope"></i> {{ $inventory->email }}</p>
                                    @endif
                                    
                                    @if($inventory->delivery_time)
                                        <p><i class="icon icon-truck"></i> {{ __('Delivery Time') }}: {{ $inventory->delivery_time }}</p>
                                    @endif
                                </div>
                                <div class="card-footer bg-white">
                                    @if($inventory->latitude && $inventory->longitude)
                                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $inventory->latitude }},{{ $inventory->longitude }}" 
                                           class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="icon icon-map-marker"></i> {{ __('Get Directions') }}
                                        </a>
                                    @endif
                                    
                                    @if(get_ecommerce_setting('is_enabled_store_pickup', 0))
                                        <a href="{{ route('public.cart') }}?pickup={{ $inventory->id }}" 
                                           class="btn btn-sm btn-outline-success">
                                            <i class="icon icon-shopping-basket"></i> {{ __('Pick Up Here') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if(setting('multi_inventory_google_maps_api_key') && $inventories->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">{{ __('Find Us On Map') }}</h4>
                        </div>
                        <div class="card-body p-0">
                            <div id="inventory-map" style="height: 500px;" data-inventories="{{ json_encode($inventories) }}"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('header')
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/multi-inventory/css/multi-inventory.css') }}">
@endpush

@push('footer')
    <script src="{{ asset('vendor/core/plugins/multi-inventory/js/multi-inventory.js') }}"></script>
    
    @if(setting('multi_inventory_google_maps_api_key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ setting('multi_inventory_google_maps_api_key') }}&callback=initInventoryMap" async defer></script>
        <script>
            function initInventoryMap() {
                if (window.multiInventory) {
                    window.multiInventory.setupMaps();
                }
            }
        </script>
    @endif
@endpush