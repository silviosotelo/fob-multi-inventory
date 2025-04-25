<?php

namespace FriendsOfBotble\MultiInventory\Tables;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\LinkableColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Columns\YesNoColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use Illuminate\Database\Eloquent\Builder;

class InventoryTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Inventory::class)
            ->addHeaderAction(
                CreateHeaderAction::make()->route('multi-inventory.inventories.create')
            )
            ->addActions([
                EditAction::make()->route('multi-inventory.inventories.edit'),
                DeleteAction::make()->route('multi-inventory.inventories.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                LinkableColumn::make('name')
                    ->route('multi-inventory.inventories.edit'),
                YesNoColumn::make('is_default')
                    ->title(trans('plugins/multi-inventory::multi-inventory.is_default')),
                YesNoColumn::make('is_frontend')
                    ->title(trans('plugins/multi-inventory::multi-inventory.is_frontend')),
                YesNoColumn::make('is_backend')
                    ->title(trans('plugins/multi-inventory::multi-inventory.is_backend')),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->addBulkAction(DeleteBulkAction::make())
            ->queryUsing(fn (Builder $query) => $query->select([
                'id',
                'name',
                'is_default',
                'is_frontend',
                'is_backend',
                'created_at',
                'status',
            ]));
    }
}