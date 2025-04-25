<?php

namespace FriendsOfBotble\MultiInventory\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use FriendsOfBotble\MultiInventory\Forms\InventoryForm;
use FriendsOfBotble\MultiInventory\Http\Requests\InventoryRequest;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Tables\InventoryTable;
use FriendsOfBotble\MultiInventory\Repositories\Interfaces\InventoryInterface;
use Exception;
use Illuminate\Http\Request;

class InventoryController extends BaseController
{
    protected $inventoryRepository;
    
    public function __construct(InventoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }
    
    public function index(InventoryTable $table)
    {
        page_title()->setTitle(trans('plugins/multi-inventory::multi-inventory.inventories'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/multi-inventory::multi-inventory.create'));

        return $formBuilder->create(InventoryForm::class)->renderForm();
    }

    public function store(InventoryRequest $request, BaseHttpResponse $response)
    {
        $inventory = Inventory::query()->create($request->input());

        event(new CreatedContentEvent(INVENTORY_MODULE_SCREEN_NAME, $request, $inventory));

        return $response
            ->setPreviousUrl(route('multi-inventory.inventories.index'))
            ->setNextUrl(route('multi-inventory.inventories.edit', $inventory->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Inventory $inventory, FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/multi-inventory::multi-inventory.edit') . ' "' . $inventory->name . '"');

        return $formBuilder->create(InventoryForm::class, ['model' => $inventory])->renderForm();
    }

    public function update(Inventory $inventory, InventoryRequest $request, BaseHttpResponse $response)
    {
        $inventory->fill($request->input());
        $inventory->save();

        event(new UpdatedContentEvent(INVENTORY_MODULE_SCREEN_NAME, $request, $inventory));

        return $response
            ->setPreviousUrl(route('multi-inventory.inventories.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Inventory $inventory, Request $request, BaseHttpResponse $response)
    {
        try {
            $inventory->delete();

            event(new DeletedContentEvent(INVENTORY_MODULE_SCREEN_NAME, $request, $inventory));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}