<?php

namespace FriendsOfBotble\MultiInventory\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface InventoryInterface extends RepositoryInterface
{
    /**
     * Get all active inventories
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActive();
    
    /**
     * Get default inventory
     *
     * @return \FriendsOfBotble\MultiInventory\Models\Inventory|null
     */
    public function getDefault();
    
    /**
     * Get inventory by ID
     *
     * @param int $id
     * @param array $with
     * @return \FriendsOfBotble\MultiInventory\Models\Inventory|null
     */
    public function findById($id, array $with = []);
}