{{-- resources/views/widgets/inventory-selector.blade.php --}}
<div class="card inventory-selector-widget">
    <div class="card-header">
        <h4 class="card-title">{{ trans('plugins/multi-inventory::multi-inventory.select_inventory') }}</h4>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form id="backend-inventory-selector-form">
            <div class="form-group">
                <select 
                    name="backend_inventory_id" 
                    class="form-control" 
                    id="backend-inventory-select"
                >
                    @foreach($inventories as $inventory)
                        <option 
                            value="{{ $inventory->id }}" 
                            {{ session('selected_backend_inventory_id') == $inventory->id ? 'selected' : '' }}
                        >
                            {{ $inventory->name }}
                            @if($inventory->is_default)
                                ({{ trans('plugins/multi-inventory::multi-inventory.default') }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inventorySelect = document.getElementById('backend-inventory-select');
    
    inventorySelect.addEventListener('change', function() {
        const inventoryId = this.value;
        
        fetch('{{ route("multi-inventory.set-selected-inventory") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                inventory_id: inventoryId,
                context: 'backend'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Opcional: mostrar una notificación
                if (typeof Botble !== 'undefined' && Botble.showSuccess) {
                    Botble.showSuccess(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
</script>