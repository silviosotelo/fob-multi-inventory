<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'FriendsOfBotble\MultiInventory\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'multi-inventory', 'as' => 'multi-inventory.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'InventoryController@index',
                'permission' => 'multi-inventory.index',
            ]);
            
            Route::group(['prefix' => 'inventories', 'as' => 'inventories.'], function () {
                Route::resource('', 'InventoryController')->parameters(['' => 'inventory']);
                
                Route::delete('items/destroy', [
                    'as' => 'deletes',
                    'uses' => 'InventoryController@deletes',
                    'permission' => 'multi-inventory.inventories.destroy',
                ]);
            });
            
            Route::get('settings', [
                'as' => 'settings',
                'uses' => 'SettingController@index',
                'permission' => 'multi-inventory.settings',
            ]);
            
            Route::post('settings', [
                'as' => 'settings.update',
                'uses' => 'SettingController@update',
                'permission' => 'multi-inventory.settings',
            ]);
            
            Route::post('product/{id}/update-inventory', [
                'as' => 'product.update-inventory',
                'uses' => 'ProductController@updateInventory',
                'permission' => 'products.edit',
            ]);
        });
    });
    
    Route::post('set-selected-inventory', [
        'as' => 'multi-inventory.set-selected-inventory',
        'uses' => 'SettingController@setSelectedInventory',
    ]);
});