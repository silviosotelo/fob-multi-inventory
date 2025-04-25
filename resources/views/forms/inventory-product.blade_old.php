<div class="inventory-management-wrapper">
    <h4>{{ trans('plugins/multi-inventory::multi-inventory.inventory_management') }}</h4>
    <p class="text-muted">{{ trans('plugins/multi-inventory::multi-inventory.inventory_management_description') }}</p>
    
    <form id="inventory-form" action="{{ route('multi-inventory.product.update-inventory', $product->id) }}" method="POST">
        @csrf
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
                                <small class="text-muted">{{ trans('plugins/multi-inventory::multi-inventory.price_help') }}</small>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!--div class="form-group">
            <button type="button" id="save-inventory-btn" class="btn btn-primary">
                <i class="fa fa-save"></i> {{ trans('core/base::forms.save') }}
            </button>
        </div-->
<div class="form-group">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> {{ trans('core/base::forms.save') }}
    </button>
</div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#save-inventory-btn').on('click', function() {
        var form = $('#inventory-form');
        if (form.length === 0) {
            Botble.showError('Form element not found');
            return;
        }
        
        var formData = new FormData(form[0]);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Botble.showLoading();
            },
            success: function(data) {
                Botble.hideLoading();
                if (data.error) {
                    Botble.showError(data.message);
                } else {
                    Botble.showSuccess(data.message);
                }
            },
            error: function(error) {
                Botble.hideLoading();
                Botble.showError('Error saving inventory data');
                console.error('Error:', error);
            }
        });
    });
});

</script>