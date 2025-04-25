<div class="inventory-management-wrapper">
    <h4>{{ trans('plugins/multi-inventory::multi-inventory.inventory_management') }}</h4>
    
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>{{ trans('plugins/multi-inventory::multi-inventory.inventories') }}</th>
                    <th>{{ trans('plugins/multi-inventory::multi-inventory.stock') }}</th>
                    <th>{{ trans('plugins/multi-inventory::multi-inventory.price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventories as $inventory)
                    <tr>
                        <td>{{ $inventory['name'] }}</td>
                        <td>
                            <input 
                                type="number" 
                                name="inventories[{{ $inventory['id'] }}][stock]" 
                                class="form-control" 
                                value="{{ $inventory['stock'] }}"
                                min="0"
                            >
                        </td>
                        <td>
                            <input 
                                type="number" 
                                name="inventories[{{ $inventory['id'] }}][price]" 
                                class="form-control" 
                                value="{{ $inventory['price'] }}"
                                min="0"
                                step="0.01"
                                placeholder="{{ $product->price }}"
                            >
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>