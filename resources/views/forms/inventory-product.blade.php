{{-- plugins/multi-inventory/resources/views/inventory-product.blade.php --}}
<div class="multi-inventory-section">
    <div class="flexbox-grid no-bg">
        <div class="flexbox-content">
            <div class="form-group">
                <h4>{{ trans('plugins/multi-inventory::multi-inventory.inventory_management') }}</h4>
                <p class="text-muted">{{ trans('plugins/multi-inventory::multi-inventory.inventory_management_description') }}</p>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>{{ trans('plugins/multi-inventory::multi-inventory.inventories') }}</th>
                    <th style="width: 200px;">{{ trans('plugins/multi-inventory::multi-inventory.stock') }}</th>
                    <th style="width: 200px;">{{ trans('plugins/multi-inventory::multi-inventory.price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventories as $inventory)
                    <tr>
                        <td>
                            <strong>{{ $inventory['name'] }}</strong>
                            @if(isset($inventory['is_default']) && $inventory['is_default'])
                                <span class="badge badge-info">{{ trans('plugins/multi-inventory::multi-inventory.default') }}</span>
                            @endif
                        </td>
                        <td>
                            <input 
                                type="number" 
                                name="multi_inventories[{{ $inventory['id'] }}][stock]" 
                                class="form-control" 
                                value="{{ $inventory['stock'] }}"
                                min="0"
                            >
                        </td>
                        <td>
                            <input 
                                type="number" 
                                name="multi_inventories[{{ $inventory['id'] }}][price]" 
                                class="form-control" 
                                value="{{ $inventory['price'] }}"
                                min="0"
                                step="0.01"
                                placeholder="{{ $product->price }}"
                            >
                            <small class="text-muted">{{ trans('plugins/multi-inventory::multi-inventory.price_help') }}</small>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>