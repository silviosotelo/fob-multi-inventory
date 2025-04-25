@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('plugins/multi-inventory::multi-inventory.inventories') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="box-header with-border">
                        <div class="btn-group">
                            <a class="btn btn-primary btn-sm" href="{{ route('multi-inventory.inventories.create') }}">
                                <i class="fa fa-plus"></i> {{ trans('plugins/multi-inventory::multi-inventory.create') }}
                            </a>
                            <a class="btn btn-success btn-sm" href="{{ route('multi-inventory.export') }}">
                                <i class="fa fa-download"></i> {{ trans('plugins/multi-inventory::multi-inventory.export') }}
                            </a>
                            <a class="btn btn-info btn-sm" href="{{ route('multi-inventory.import.form') }}">
                                <i class="fa fa-upload"></i> {{ trans('plugins/multi-inventory::multi-inventory.import') }}
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        {!! $dataTable->renderTable() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(config('multi-inventory.features.map_view', true))
    <div class="row mt-3">
        <div class="col-12">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('plugins/multi-inventory::multi-inventory.inventory_map') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    <div id="inventory-map" style="height: 500px;" data-inventories="{{ json_encode($inventories) }}"></div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('header')
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/multi-inventory/css/multi-inventory.css') }}">
@endpush

@push('footer')
    <script src="{{ asset('vendor/core/plugins/multi-inventory/js/multi-inventory-admin.js') }}"></script>
    
    @if(config('multi-inventory.features.map_view', true))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ setting('multi_inventory_google_maps_api_key') }}&callback=initInventoryMap" async defer></script>
        <script>
            function initInventoryMap() {
                if (window.multiInventoryAdmin) {
                    window.multiInventoryAdmin.setupMaps();
                }
            }
        </script>
    @endif
@endpush