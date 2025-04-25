<?php

return [
    [
        'name' => 'Multi Inventory',
        'flag' => 'multi-inventory.index',
    ],
    [
        'name' => 'Inventories',
        'flag' => 'multi-inventory.inventories.index',
        'parent_flag' => 'multi-inventory.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'multi-inventory.inventories.create',
        'parent_flag' => 'multi-inventory.inventories.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'multi-inventory.inventories.edit',
        'parent_flag' => 'multi-inventory.inventories.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'multi-inventory.inventories.destroy',
        'parent_flag' => 'multi-inventory.inventories.index',
    ],
    [
        'name' => 'Settings',
        'flag' => 'multi-inventory.settings',
        'parent_flag' => 'multi-inventory.index',
    ],
];