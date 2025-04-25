<?php
namespace FriendsOfBotble\MultiInventory\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\InventoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\InventoryProductInterface;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Models\InventoryProduct;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    protected $inventoryRepository;
    protected $inventoryProductRepository;

    public function __construct(
        InventoryInterface $inventoryRepository, 
        InventoryProductInterface $inventoryProductRepository
    ) {
        $this->inventoryRepository = $inventoryRepository;
        $this->inventoryProductRepository = $inventoryProductRepository;
    }

    public function updateInventory($id, Request $request, BaseHttpResponse $response)
    {
        $product = Product::findOrFail($id);

        $inventories = $request->input('inventories', []);

        try {
            foreach ($inventories as $inventoryId => $data) {
                $inventory = $this->inventoryRepository->findById($inventoryId);
                
                if (!$inventory) {
                    continue;
                }

                $stock = isset($data['stock']) ? (int) $data['stock'] : 0;
                $price = isset($data['price']) && !empty($data['price']) 
                    ? (float) $data['price'] 
                    : null;

                $this->inventoryProductRepository->updateStock(
                    $product->id, 
                    $inventoryId, 
                    $stock, 
                    $price
                );
            }

            // Actualizar el stock total en la tabla de productos
            $totalStock = $this->inventoryProductRepository->getTotalStock($product->id);
            $product->stock = $totalStock;
            $product->save();

            return $response
                ->setMessage(trans('core/base::notices.update_success_message'))
                ->setData(['total_stock' => $totalStock]);
        } catch (\Exception $e) {
            return $response
                ->setError()
                ->setMessage($e->getMessage());
        }
    }
}