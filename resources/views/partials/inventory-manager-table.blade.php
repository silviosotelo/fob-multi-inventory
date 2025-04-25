{{-- resources/views/partials/inventory-manager-table.blade.php --}}

<div class="multi-inventory-manager-wrapper">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                {{ trans('plugins/multi-inventory::multi-inventory.batch_inventory_management') }}
            </h4>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="bulk-actions form-inline">
                            <div class="form-group mr-2">
                                <select id="bulk-action-select" class="form-control">
                                    <option value="set">{{ trans('plugins/multi-inventory::multi-inventory.set_value') }}</option>
                                    <option value="increase">{{ trans('plugins/multi-inventory::multi-inventory.increase_by') }}</option>
                                    <option value="decrease">{{ trans('plugins/multi-inventory::multi-inventory.decrease_by') }}</option>
                                    <option value="percentage_increase">{{ trans('plugins/multi-inventory::multi-inventory.percentage_increase') }}</option>
                                    <option value="percentage_decrease">{{ trans('plugins/multi-inventory::multi-inventory.percentage_decrease') }}</option>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <input type="number" id="bulk-action-value" class="form-control" placeholder="{{ trans('plugins/multi-inventory::multi-inventory.value') }}" min="0" step="1">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary bulk-update-btn" data-action="bulk" data-input="bulk-action-value">
                                    {{ trans('plugins/multi-inventory::multi-inventory.apply') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="form-group">
                            <input type="text" id="inventory-search" class="form-control" placeholder="{{ trans('plugins/multi-inventory::multi-inventory.search') }}">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <div class="multi-inventory-manager-table-spinner-overlay" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ trans('plugins/multi-inventory::multi-inventory.loading') }}</span>
                    </div>
                </div>
                
                <table class="table table-striped multi-inventory-manager-table">
                    <thead>
                        <tr>
                            <th>{{ trans('plugins/ecommerce::products.name') }}</th>
                            <th>{{ trans('plugins/ecommerce::products.sku') }}</th>
                            <th>{{ trans('plugins/ecommerce::products.price') }}</th>
                            <th>{{ trans('plugins/multi-inventory::multi-inventory.total_stock') }}</th>
                            
                            @foreach($inventories as $inventory)
                                <th>
                                    {{ $inventory->name }} 
                                    <div class="inventory-column-actions">
                                        <div class="form-group mb-1">
                                            <input type="number" id="inventory-{{ $inventory->id }}-value" class="form-control form-control-sm" placeholder="{{ trans('plugins/multi-inventory::multi-inventory.value') }}" min="0">
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary bulk-update-btn" data-action="set" data-input="inventory-{{ $inventory->id }}-value" data-inventory="{{ $inventory->id }}">
                                                <i class="fa fa-pencil-alt"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success bulk-update-btn" data-action="increase" data-input="inventory-{{ $inventory->id }}-value" data-inventory="{{ $inventory->id }}">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger bulk-update-btn" data-action="decrease" data-input="inventory-{{ $inventory->id }}-value" data-inventory="{{ $inventory->id }}">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </th>
                            @endforeach
                            
                            <th>{{ trans('core/base::tables.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($products) > 0)
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="product-image mr-2">
                                                <img src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" width="50" alt="{{ $product->name }}">
                                            </div>
                                            <div>
                                                <a href="{{ route('products.edit', $product->id) }}">{{ $product->name }}</a>
                                                @if($product->variations->count() > 0)
                                                    <span class="badge badge-info">{{ trans('plugins/ecommerce::products.variations') }}: {{ $product->variations->count() }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ format_price($product->price) }}</td>
                                    <td>
                                        <input type="number" class="form-control multi-inventory-manager-table-total-stock" value="{{ $product->stock }}" readonly>
                                    </td>
                                    
                                    @foreach($inventories as $inventory)
                                        @php
                                            $inventoryProduct = $product->inventories->where('id', $inventory->id)->first();
                                            $stock = $inventoryProduct ? $inventoryProduct->pivot->stock : 0;
                                        @endphp
                                        <td>
                                            <input 
                                                type="number" 
                                                class="form-control multi-inventory-manager-table-stock multi-inventory-manager-table-inventory-stock" 
                                                data-product-id="{{ $product->id }}" 
                                                data-inventory-id="{{ $inventory->id }}" 
                                                value="{{ $stock }}"
                                                min="0"
                                            >
                                        </td>
                                    @endforeach
                                    
                                    <td>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-icon btn-sm btn-primary" title="{{ trans('core/base::tables.edit') }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{ 4 + count($inventories) }}" class="text-center">{{ trans('core/base::tables.no_data') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            @if ($products->hasPages())
                <div class="mt-3 justify-content-center pagination-container">
                    {!! $products->links() !!}
                </div>
            @endif
        </div>
    </div>
</div>

@push('footer')
<script>
    $(document).ready(function() {
        // DataTable search
        $('#inventory-search').on('keyup', function() {
            $('.multi-inventory-manager-table').DataTable().search($(this).val()).draw();
        });
    });
</script>
@endpush