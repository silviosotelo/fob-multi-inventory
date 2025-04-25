<?php

namespace FriendsOfBotble\MultiInventory\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use FriendsOfBotble\MultiInventory\Repositories\Interfaces\InventoryInterface;
use FriendsOfBotble\MultiInventory\Models\Inventory;

class InventoryRepository extends RepositoriesAbstract implements InventoryInterface
{
    /**
     * Get all active inventories
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActive()
    {
        $data = $this->model
            ->where('status', 'published')
            ->orderBy('order_priority', 'desc')
            ->get();

        return $data;
    }
    
    /**
     * Get default inventory
     *
     * @return \FriendsOfBotble\MultiInventory\Models\Inventory|null
     */
    public function getDefault()
    {
        return $this->model->where('is_default', true)->first();
    }
        
    /**
     * Get inventory by ID
     *
     * @param int $id
     * @param array $with
     * @return \FriendsOfBotble\MultiInventory\Models\Inventory|null
     */
    public function findById($id, array $with = [])
    {
        $query = $this->model->where('id', $id);
        
        if (!empty($with)) {
            $query = $query->with($with);
        }
        
        return $query->first();
    }
}