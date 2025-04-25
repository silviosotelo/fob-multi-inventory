<div class="row">
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="name" class="control-label required">{{ trans('core/base::forms.name') }}</label>
                    {!! Form::text('name', old('name', $inventory->name ?? ''), ['class' => 'form-control', 'id' => 'name', 'placeholder' => trans('core/base::forms.name_placeholder'), 'data-counter' => 120]) !!}
                </div>

                <div class="form-group mb-3">
                    <label for="description" class="control-label">{{ trans('core/base::forms.description') }}</label>
                    {!! Form::textarea('description', old('description', $inventory->description ?? ''), ['class' => 'form-control', 'rows' => 4, 'id' => 'description', 'placeholder' => trans('core/base::forms.description_placeholder'), 'data-counter' => 400]) !!}
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="address" class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.address') }}</label>
                            {!! Form::text('address', old('address', $inventory->address ?? ''), ['class' => 'form-control', 'id' => 'address', 'placeholder' => trans('plugins/multi-inventory::multi-inventory.address_placeholder'), 'data-counter' => 255]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="city" class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.city') }}</label>
                            {!! Form::text('city', old('city', $inventory->city ?? ''), ['class' => 'form-control', 'id' => 'city', 'placeholder' => trans('plugins/multi-inventory::multi-inventory.city_placeholder'), 'data-counter' => 255]) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="state" class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.state') }}</label>
                            {!! Form::text('state', old('state', $inventory->state ?? ''), ['class' => 'form-control', 'id' => 'state', 'placeholder' => trans('plugins/multi-inventory::multi-inventory.state_placeholder'), 'data-counter' => 255]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="zip_code" class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.zip_code') }}</label>
                            {!! Form::text('zip_code', old('zip_code', $inventory->zip_code ?? ''), ['class' => 'form-control', 'id' => 'zip_code', 'placeholder' => trans('plugins/multi-inventory::multi-inventory.zip_code_placeholder'), 'data-counter' => 20]) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email" class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.email') }}</label>
                            {!! Form::email('email', old('email', $inventory->email ?? ''), ['class' => 'form-control', 'id' => 'email', 'placeholder' => trans('plugins/multi-inventory::multi-inventory.email_placeholder'), 'data-counter' => 60]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="phone" class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.phone') }}</label>
                            {!! Form::text('phone', old('phone', $inventory->phone ?? ''), ['class' => 'form-control', 'id' => 'phone', 'placeholder' => trans('plugins/multi-inventory::multi-inventory.phone_placeholder'), 'data-counter' => 20]) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="country" class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.country') }}</label>
                            {!! Form::customSelect('country', get_countries_list(), old('country', $inventory->country ?? '')) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="delivery_time" class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.delivery_time') }}</label>
                            {!! Form::text('delivery_time', old('delivery_time', $inventory->delivery_time ?? ''), ['class' => 'form-control', 'id' => 'delivery_time', 'placeholder' => trans('plugins/multi-inventory::multi-inventory.delivery_time_placeholder'), 'data-counter' => 255]) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="latitude" class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.latitude') }}</label>
                            {!! Form::text('latitude', old('latitude', $inventory->latitude ?? ''), ['class' => 'form-control', 'id' => 'latitude', 'placeholder' => '40.7128', 'data-counter' => 20]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="longitude" class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.longitude') }}</label>
                            {!! Form::text('longitude', old('longitude', $inventory->longitude ?? ''), ['class' => 'form-control', 'id' => 'longitude', 'placeholder' => '-74.0060', 'data-counter' => 20]) !!}
                        </div>
                    </div>
                </div>

                @if(setting('multi_inventory_google_maps_api_key'))
                <div class="form-group mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.map_location') }}</label>
                        <button id="geocode-address-btn" class="btn btn-info btn-sm">
                            <i class="fa fa-map-marker"></i> {{ trans('plugins/multi-inventory::multi-inventory.geocode_address') }}
                        </button>
                    </div>
                    <div id="coordinate-picker-map" style="height: 300px;"></div>
                    <small class="text-muted">{{ trans('plugins/multi-inventory::multi-inventory.map_help') }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 right-sidebar">
        <div class="widget meta-boxes">
            <div class="widget-title">
                <h4><span>{{ trans('core/base::forms.publish') }}</span></h4>
            </div>
            <div class="widget-body">
                <div class="form-group mb-3">
                    <label for="status" class="control-label required">{{ trans('core/base::tables.status') }}</label>
                    {!! Form::customSelect('status', \Botble\Base\Enums\BaseStatusEnum::labels(), old('status', $inventory->status ?? \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)) !!}
                </div>

                <div class="form-group mb-3">
                    <label class="control-label">{{ trans('plugins/multi-inventory::multi-inventory.order_priority') }}</label>
                    {!! Form::number('order_priority', old('order_priority', $inventory->order_priority ?? 0), ['class' => 'form-control', 'placeholder' => '0', 'min' => 0]) !!}
                    <small class="text-muted">{{ trans('plugins/multi-inventory::multi-inventory.order_priority_help') }}</small>
                </div>

                <div class="form-group mb-3">
                    <div class="onoffswitch">
                        <input type="hidden" name="is_default" value="0">
                        <input type="checkbox" name="is_default" class="onoffswitch-checkbox" id="is_default" value="1" @if(old('is_default', $inventory->is_default ?? false)) checked @endif>
                        <label class="onoffswitch-label" for="is_default">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                    <label for="is_default">{{ trans('plugins/multi-inventory::multi-inventory.is_default') }}</label>
                </div>

                <div class="form-group mb-3">
                    <div class="onoffswitch">
                        <input type="hidden" name="is_frontend" value="0">
                        <input type="checkbox" name="is_frontend" class="onoffswitch-checkbox" id="is_frontend" value="1" @if(old('is_frontend', $inventory->is_frontend ?? true)) checked @endif>
                        <label class="onoffswitch-label" for="is_frontend">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                    <label for="is_frontend">{{ trans('plugins/multi-inventory::multi-inventory.is_frontend') }}</label>
                    <small class="text-muted d-block">{{ trans('plugins/multi-inventory::multi-inventory.is_frontend_help') }}</small>
                </div>

                <div class="form-group mb-3">
                    <div class="onoffswitch">
                        <input type="hidden" name="is_backend" value="0">
                        <input type="checkbox" name="is_backend" class="onoffswitch-checkbox" id="is_backend" value="1" @if(old('is_backend', $inventory->is_backend ?? true)) checked @endif>
                        <label class="onoffswitch-label" for="is_backend">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                    <label for="is_backend">{{ trans('plugins/multi-inventory::multi-inventory.is_backend') }}</label>
                    <small class="text-muted d-block">{{ trans('plugins/multi-inventory::multi-inventory.is_backend_help') }}</small>
                </div>

                <div class="btn-list">
                    <button type="submit" name="submit" value="save" class="btn btn-info">
                        <i class="fa fa-save"></i> {{ trans('core/base::forms.save') }}
                    </button>
                    <button type="submit" name="submit" value="apply" class="btn btn-success">
                        <i class="fa fa-check-circle"></i> {{ trans('core/base::forms.save_and_continue') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>