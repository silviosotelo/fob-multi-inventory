<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'FriendsOfBotble\MultiInventory\Http\Controllers', 'middleware' => ['api']], function () {
    Route::prefix('multi-inventory')->group(function () {
        Route::get('inventories', 'ApiController@getInventories');
        Route::get('inventories/{id}', 'ApiController@getInventory');
        Route::get('product/{id}/inventory/{inventoryId}', 'ApiController@getProductInventory');
        Route::get('product/{id}/inventories', 'ApiController@getProductInventories');
    });
});