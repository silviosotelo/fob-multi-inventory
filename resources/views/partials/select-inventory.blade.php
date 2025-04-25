{{-- resources/views/partials/select-inventory.blade.php --}}
<div class="inventory-selector mb-3">
    <label class="form-label">{{ trans('plugins/multi-inventory::multi-inventory.select_inventory') }}:</label>

    <div class="inventories-wrapper">
        @foreach($inventories as $inventory)
        <div class="form-check">
            <input
                class="form-check-input inventory-radio"
                type="radio"
                name="inventory_id"
                id="inventory_{{ $inventory->id }}"
                value="{{ $inventory->id }}"
                data-stock="{{ $product->getStockByInventory($inventory->id) }}"
                data-price="{{ $product->getPriceByInventory($inventory->id) }}"
                {{ $loop->first ? 'checked' : '' }}>
            <label class="form-check-label" for="inventory_{{ $inventory->id }}">
                {{ $inventory->name }}

                @if($product->getStockByInventory($inventory->id) > 0)
                <span class="badge bg-success">{{ trans('plugins/multi-inventory::multi-inventory.in_stock') }}</span>
                @else
                <span class="badge bg-danger">{{ trans('plugins/multi-inventory::multi-inventory.out_of_stock') }}</span>
                @endif

                @if($product->getPriceByInventory($inventory->id) != $product->price)
                <span class="inventory-specific-price">
                    {{ format_price($product->getPriceByInventory($inventory->id)) }}
                </span>
                @endif

                @if($inventory->delivery_time)
                <span class="delivery-time">
                    ({{ $inventory->delivery_time }})
                </span>
                @endif
            </label>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inventoryRadios = document.querySelectorAll('.inventory-radio');
        const addToCartBtn = document.querySelector('.add-to-cart-button');
        const productPrice = document.querySelector('.product-price');
        const originalPrice = {
            {
                $product - > price
            }
        };

        inventoryRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Actualizar precio si es diferente
                const inventoryPrice = parseFloat(this.dataset.price);
                if (inventoryPrice && inventoryPrice != originalPrice) {
                    productPrice.innerHTML = formatPrice(inventoryPrice);
                } else {
                    productPrice.innerHTML = formatPrice(originalPrice);
                }

                // Verificar stock
                const stock = parseInt(this.dataset.stock);
                if (stock <= 0) {
                    addToCartBtn.disabled = true;
                    addToCartBtn.classList.add('disabled');
                } else {
                    addToCartBtn.disabled = false;
                    addToCartBtn.classList.remove('disabled');
                }

                // Guardar en sesión
                fetch('/set-selected-inventory', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        inventory_id: this.value
                    })
                });
            });
        });

        // Trigger change en el inventario seleccionado por defecto
        document.querySelector('.inventory-radio:checked').dispatchEvent(new Event('change'));

        function formatPrice(price) {
            return price.toLocaleString('{{ app()->getLocale() }}', {
                style: 'currency',
                currency: '{{ get_application_currency()->title }}'
            });
        }
    });
</script>