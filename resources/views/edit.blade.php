@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('plugins/multi-inventory::multi-inventory.edit') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    {!! Form::model($inventory, ['route' => ['multi-inventory.inventories.update', $inventory->id], 'method' => 'PUT', 'id' => 'inventory-form']) !!}
                        @include('plugins/multi-inventory::partials.inventory-form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        
        @if($inventory->exists && isset($inventory->id))
        <div class="col-md-12 mt-3">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('plugins/multi-inventory::multi-inventory.inventory_products') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('plugins/ecommerce::products.name') }}</th>
                                    <th>{{ trans('plugins/ecommerce::products.sku') }}</th>
                                    <th>{{ trans('plugins/multi-inventory::multi-inventory.stock') }}</th>
                                    <th>{{ trans('plugins/multi-inventory::multi-inventory.price') }}</th>
                                    <th>{{ trans('core/base::tables.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($inventory->products->count() > 0)
                                    @foreach($inventory->products as $product)
                                        <tr>
                                            <td>
                                                <a href="{{ route('products.edit', $product->id) }}" target="_blank">
                                                    {{ $product->name }}
                                                </a>
                                            </td>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->pivot->stock }}</td>
                                            <td>{{ $product->pivot->price ? format_price($product->pivot->price) : format_price($product->price) }}</td>
                                            <td>
                                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-icon btn-sm btn-info" target="_blank">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">{{ trans('plugins/multi-inventory::multi-inventory.no_products') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
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