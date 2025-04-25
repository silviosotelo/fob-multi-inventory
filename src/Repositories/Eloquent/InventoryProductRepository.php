<?php

namespace FriendsOfBotble\MultiInventory\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use FriendsOfBotble\MultiInventory\Repositories\Interfaces\InventoryProductInterface;
use FriendsOfBotble\MultiInventory\Models\InventoryProduct;

class InventoryProductRepository extends RepositoriesAbstract implements InventoryProductInterface
{
    /**
     * Get product inventory by product ID and inventory ID
     *
     * @param int $productId
     * @param int $inventoryId
     * @return mixed
     */
    public function getByProductAndInventory($productId, $inventoryId)
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('inventory_id', $inventoryId)
            ->first();
    }
    
    /**
     * Update stock for a product in an inventory
     *
     * @param int $productId
     * @param int $inventoryId
     * @param int $stock
     * @param float|null $price
     * @return mixed
     */
    public function updateStock($productId, $inventoryId, $stock, $price = null)
    {
        $inventoryProduct = $this->getByProductAndInventory($productId, $inventoryId);
        
        if (!$inventoryProduct) {
            $inventoryProduct = $this->model->newInstance();
            $inventoryProduct->product_id = $productId;
            $inventoryProduct->inventory_id = $inventoryId;
        }
        
        $inventoryProduct->stock = $stock;
        
        if ($price !== null) {
            $inventoryProduct->price = $price;
        }
        
        $inventoryProduct->save();
        
        return $inventoryProduct;
    }
}