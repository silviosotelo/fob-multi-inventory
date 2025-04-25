{{-- resources/views/settings.blade.php --}}
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {!! Form::open(['route' => 'multi-inventory.settings.update', 'method' => 'POST']) !!}
    <div class="max-width-1200">
        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>{{ trans('plugins/multi-inventory::multi-inventory.settings') }}</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">{{ trans('plugins/multi-inventory::multi-inventory.settings_description') }}</p>
                </div>
            </div>

            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="form-group mb-3">
                        <label class="text-title-field" for="click_collect_enabled">
                            {{ trans('plugins/multi-inventory::multi-inventory.click_collect') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="click_collect_enabled" value="1" @if (setting('multi_inventory_click_collect_enabled', false)) checked @endif>
                            {{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label>
                            <input type="radio" name="click_collect_enabled" value="0" @if (!setting('multi_inventory_click_collect_enabled', false)) checked @endif>
                            {{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>

                    <div class="form-group mb-3 delivery-inventory-container @if (!setting('multi_inventory_click_collect_enabled', false)) d-none @endif">
                        <label class="text-title-field" for="delivery_inventory_id">
                            {{ trans('plugins/multi-inventory::multi-inventory.delivery') }} {{ trans('plugins/multi-inventory::multi-inventory.inventories') }}
                        </label>
                        {!! Form::customSelect('delivery_inventory_id', $inventories, setting('multi_inventory_delivery_inventory_id')) !!}
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-title-field" for="modify_stock_quantity">
                            {{ trans('plugins/multi-inventory::multi-inventory.modify_stock_quantity') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="modify_stock_quantity" value="1" @if (setting('multi_inventory_modify_stock_quantity', false)) checked @endif>
                            {{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label>
                            <input type="radio" name="modify_stock_quantity" value="0" @if (!setting('multi_inventory_modify_stock_quantity', false)) checked @endif>
                            {{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-title-field" for="reduce_stock_on_pending">
                            {{ trans('plugins/multi-inventory::multi-inventory.reduce_stock_on_pending') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="reduce_stock_on_pending" value="1" @if (setting('multi_inventory_reduce_stock_on_pending', false)) checked @endif>
                            {{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label>
                            <input type="radio" name="reduce_stock_on_pending" value="0" @if (!setting('multi_inventory_reduce_stock_on_pending', false)) checked @endif>
                            {{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-title-field" for="inventory_required">
                            {{ trans('plugins/multi-inventory::multi-inventory.inventory_required') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="inventory_required" value="1" @if (setting('multi_inventory_inventory_required', true)) checked @endif>
                            {{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label>
                            <input type="radio" name="inventory_required" value="0" @if (!setting('multi_inventory_inventory_required', true)) checked @endif>
                            {{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-title-field" for="display_type">
                            {{ trans('plugins/multi-inventory::multi-inventory.display_type') }}
                        </label>
                        {!! Form::customSelect('display_type', [
                            'radio' => 'Radio Buttons',
                            'select' => 'Dropdown Select',
                            'label' => 'Label',
                            'hidden' => 'Hidden',
                        ], setting('multi_inventory_display_type', 'radio')) !!}
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-title-field" for="order_flow">
                            {{ trans('plugins/multi-inventory::multi-inventory.order_flow') }}
                        </label>
                        {!! Form::customSelect('order_flow', [
                            'custom' => 'Custom Inventory',
                            'country' => 'By Country',
                            'most_stock' => 'Most Stock',
                            'lowest_stock' => 'Lowest Stock',
                            'name' => 'By Name',
                            'order' => 'By Order Priority',
                        ], setting('multi_inventory_order_flow', 'most_stock')) !!}
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-title-field" for="stock_display">
                            {{ trans('plugins/multi-inventory::multi-inventory.stock_display') }}
                        </label>
                        {!! Form::customSelect('stock_display', [
                            'count' => 'Show Count',
                            'inout' => 'In Stock/Out of Stock',
                            'hidden' => 'Hidden',
                        ], setting('multi_inventory_stock_display', 'count')) !!}
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-title-field" for="text_in_stock">
                            {{ trans('plugins/multi-inventory::multi-inventory.text_in_stock') }}
                        </label>
                        <input class="form-control" name="text_in_stock" type="text" value="{{ setting('multi_inventory_text_in_stock', 'In Stock') }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-title-field" for="text_out_of_stock">
                            {{ trans('plugins/multi-inventory::multi-inventory.text_out_of_stock') }}
                        </label>
                        <input class="form-control" name="text_out_of_stock" type="text" value="{{ setting('multi_inventory_text_out_of_stock', 'Out of Stock') }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-title-field" for="google_maps_api_key">
                            {{ trans('plugins/multi-inventory::multi-inventory.google_maps_api_key') }}
                        </label>
                        <input class="form-control" name="google_maps_api_key" type="text" value="{{ setting('multi_inventory_google_maps_api_key') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="flexbox-annotated-section" style="border: none">
            <div class="flexbox-annotated-section-annotation">
                &nbsp;
            </div>
            <div class="flexbox-annotated-section-content">
                <button class="btn btn-info" type="submit">{{ trans('core/setting::setting.save_settings') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@push('footer')
    <script>
        'use strict';
        $(document).ready(function() {
            $('input[name="click_collect_enabled"]').on('change', function() {
                if ($(this).val() === '1') {
                    $('.delivery-inventory-container').removeClass('d-none');
                } else {
                    $('.delivery-inventory-container').addClass('d-none');
                }
            });
        });
    </script>
@endpush