<?php

namespace FriendsOfBotble\MultiInventory\Tables;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\DataTables;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class InventoryTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Inventory::class)
            ->addActions([
                'edit' => [
                    'permission' => 'multi-inventory.inventories.edit',
                    'route' => 'multi-inventory.inventories.edit',
                    'icon' => 'fa fa-edit',
                    'label' => trans('core/base::tables.edit'),
                ],
                'delete' => [
                    'permission' => 'multi-inventory.inventories.destroy',
                    'route' => 'multi-inventory.inventories.destroy',
                    'icon' => 'fa fa-trash',
                    'label' => trans('core/base::tables.delete'),
                    'class' => 'btn-danger',
                ],
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Inventory $item) {
                return Html::link(route('multi-inventory.inventories.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('is_default', function (Inventory $item) {
                return $item->is_default ? trans('core/base::base.yes') : trans('core/base::base.no');
            })
            ->editColumn('is_frontend', function (Inventory $item) {
                return $item->is_frontend ? trans('core/base::base.yes') : trans('core/base::base.no');
            })
            ->editColumn('is_backend', function (Inventory $item) {
                return $item->is_backend ? trans('core/base::base.yes') : trans('core/base::base.no');
            })
            ->editColumn('checkbox', function (Inventory $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (Inventory $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Inventory $item) {
                return $item->status->toHtml();
            });

        return $this->toJson($data);
    }

    public function query(): Builder
    {
        $query = $this->getModel()->query()->select([
            'id',
            'name',
            'is_default',
            'is_frontend',
            'is_backend',
            'created_at',
            'status',
        ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'is_default' => [
                'title' => trans('plugins/multi-inventory::multi-inventory.is_default'),
                'width' => '100px',
            ],
            'is_frontend' => [
                'title' => trans('plugins/multi-inventory::multi-inventory.is_frontend'),
                'width' => '100px',
            ],
            'is_backend' => [
                'title' => trans('plugins/multi-inventory::multi-inventory.is_backend'),
                'width' => '100px',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('multi-inventory.inventories.create'), 'multi-inventory.inventories.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(
            route('multi-inventory.inventories.deletes'),
            'multi-inventory.inventories.destroy',
            parent::bulkActions()
        );
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}