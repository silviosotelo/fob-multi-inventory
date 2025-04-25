<div class="alert alert-warning">
    <p>{{ trans('plugins/multi-inventory::multi-inventory.no_inventories_message') }}</p>
    <a href="{{ route('multi-inventory.inventories.create') }}" class="btn btn-info btn-sm">
        <i class="fa fa-plus"></i> {{ trans('plugins/multi-inventory::multi-inventory.create_inventory') }}
    </a>
</div>