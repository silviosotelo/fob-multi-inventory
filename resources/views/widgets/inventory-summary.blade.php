{{-- resources/views/widgets/inventory-summary.blade.php --}}

<div class="row multi-inventory-dashboard-widget">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ trans('plugins/multi-inventory::multi-inventory.inventory_status') }}</h4>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ trans('plugins/multi-inventory::multi-inventory.inventory') }}</th>
                            <th>{{ trans('plugins/multi-inventory::multi-inventory.total_products') }}</th>
                            <th>{{ trans('plugins/multi-inventory::multi-inventory.total_stock') }}</th>
                            <th>{{ trans('plugins/multi-inventory::multi-inventory.stock_value') }}</th>
                            <th>{{ trans('plugins/multi-inventory::multi-inventory.low_stock') }}</th>
                            <th>{{ trans('plugins/multi-inventory::multi-inventory.out_of_stock') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventories as $inventory)
                        <tr>
                            <td>
                                <a href="{{ route('multi-inventory.inventories.edit', $inventory->id) }}">
                                    {{ $inventory->name }}
                                </a>
                                @if($inventory->is_default)
                                    <span class="badge bg-info">{{ trans('plugins/multi-inventory::multi-inventory.default') }}</span>
                                @endif
                            </td>
                            <td>{{ $inventory->products_count }}</td>
                            <td>{{ $inventory->total_stock }}</td>
                            <td>{{ format_price($inventory->stock_value) }}</td>
                            <td>
                                @if($inventory->low_stock_count > 0)
                                    <span class="text-warning">{{ $inventory->low_stock_count }}</span>
                                @else
                                    <span class="text-success">0</span>
                                @endif
                            </td>
                            <td>
                                @if($inventory->out_of_stock_count > 0)
                                    <span class="text-danger">{{ $inventory->out_of_stock_count }}</span>
                                @else
                                    <span class="text-success">0</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-light font-weight-bold">
                            <td>{{ trans('plugins/multi-inventory::multi-inventory.total') }}</td>
                            <td>{{ $totalProducts }}</td>
                            <td>{{ $totalStock }}</td>
                            <td>{{ format_price($totalStockValue) }}</td>
                            <td>{{ $totalLowStock }}</td>
                            <td>{{ $totalOutOfStock }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ trans('plugins/multi-inventory::multi-inventory.inventory_distribution') }}</h4>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="inventory-distribution-chart" style="height: 250px;">
                    {!! $inventoryDistributionChart !!}
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title">{{ trans('plugins/multi-inventory::multi-inventory.quick_actions') }}</h4>
            </div>
            <div class="card-body">
                <div class="btn-group w-100 mb-2">
                    <a href="{{ route('multi-inventory.inventories.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> {{ trans('plugins/multi-inventory::multi-inventory.create_inventory') }}
                    </a>
                </div>
                <div class="btn-group w-100 mb-2">
                    <a href="{{ route('multi-inventory.import.form') }}" class="btn btn-success">
                        <i class="fa fa-upload"></i> {{ trans('plugins/multi-inventory::multi-inventory.import') }}
                    </a>
                </div>
                <div class="btn-group w-100 mb-2">
                    <a href="{{ route('multi-inventory.export') }}" class="btn btn-info">
                        <i class="fa fa-download"></i> {{ trans('plugins/multi-inventory::multi-inventory.export') }}
                    </a>
                </div>
                <div class="btn-group w-100">
                    <a href="{{ route('multi-inventory.settings') }}" class="btn btn-secondary">
                        <i class="fa fa-cog"></i> {{ trans('plugins/multi-inventory::multi-inventory.settings') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(count($lowStockProducts) > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ trans('plugins/multi-inventory::multi-inventory.low_stock_products') }}</h4>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ trans('plugins/ecommerce::products.name') }}</th>
                            <th>{{ trans('plugins/ecommerce::products.sku') }}</th>
                            <th>{{ trans('plugins/multi-inventory::multi-inventory.inventory') }}</th>
                            <th>{{ trans('plugins/multi-inventory::multi-inventory.stock') }}</th>
                            <th>{{ trans('plugins/multi-inventory::multi-inventory.threshold') }}</th>
                            <th>{{ trans('core/base::tables.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                        <tr>
                            <td>
                                <a href="{{ route('products.edit', $product->product_id) }}">
                                    {{ $product->product_name }}
                                </a>
                            </td>
                            <td>{{ $product->product_sku }}</td>
                            <td>{{ $product->inventory_name }}</td>
                            <td><span class="text-warning">{{ $product->stock }}</span></td>
                            <td>{{ $product->low_stock_threshold }}</td>
                            <td>
                                <a href="{{ route('products.edit', $product->product_id) }}" class="btn btn-icon btn-sm btn-primary">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif