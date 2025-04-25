<?php

namespace FriendsOfBotble\MultiInventory\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface InventoryProductInterface extends RepositoryInterface
{
    /**
     * Get product inventory by product ID and inventory ID
     *
     * @param int $productId
     * @param int $inventoryId
     * @return mixed
     */
    public function getByProductAndInventory($productId, $inventoryId);
    
    /**
     * Update stock for a product in an inventory
     *
     * @param int $productId
     * @param int $inventoryId
     * @param int $stock
     * @param float|null $price
     * @return mixed
     */
    public function updateStock($productId, $inventoryId, $stock, $price = null);
}