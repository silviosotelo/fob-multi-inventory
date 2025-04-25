@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('plugins/multi-inventory::multi-inventory.create') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    {!! Form::open(['route' => 'multi-inventory.inventories.store', 'method' => 'POST', 'id' => 'inventory-form']) !!}
                        @include('plugins/multi-inventory::partials.inventory-form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('header')
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/multi-inventory/css/multi-inventory.css') }}">
@endpush

@push('footer')
    <script src="{{ asset('vendor/core/plugins/multi-inventory/js/multi-inventory-admin.js') }}"></script>
    
    @if(setting('multi_inventory_google_maps_api_key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ setting('multi_inventory_google_maps_api_key') }}&callback=initMap" async defer></script>
        <script>
            function initMap() {
                if (window.multiInventoryAdmin) {
                    window.multiInventoryAdmin.setupCoordinatePicker();
                }
            }
        </script>
    @endif
@endpush