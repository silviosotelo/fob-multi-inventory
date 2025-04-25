{{-- Sobrescribir la vista: /platform/plugins/ecommerce/resources/views/orders/checkout/product.blade.php --}}

<div class="row cart-item">
    <div class="col-3">
        <div class="checkout-product-img-wrapper">
            <img src="{{ Botble\RvMedia\Facades\RvMedia::getImageUrl($product->product_image, 'thumb', false, RvMedia::getDefaultImage()) }}" class="img-fluid" alt="{{ $product->product_name }}">
        </div>
    </div>
    <div class="col-5">
        <p>{{ $product->product_name }}</p>
        
        @if (!empty($product->product_options))
            <p>
                <small>{{ $product->options_txt }}</small>
            </p>
        @endif

        @if (!empty($product->options) && isset($product->options['inventory_id']))
            @php
                $inventoryId = $product->options['inventory_id'];
                $inventory = \FriendsOfBotble\MultiInventory\Models\Inventory::find($inventoryId);
            @endphp
            @if ($inventory)
                <p class="mb-0">
                    <small>{{ trans('plugins/multi-inventory::multi-inventory.inventories') }}: {{ $inventory->name }}</small>
                </p>
            @endif
        @endif
        
        <p class="mb-0">
            <small>{{ format_price($product->price_per_item) }}</small>
        </p>
    </div>
    <div class="col-4 text-end">
        <span>{{ format_price($product->price_per_item * $product->qty) }}</span>
    </div>
</div>