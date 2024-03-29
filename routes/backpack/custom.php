<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    //order admin panel----------------------------------------------------------------------------------------
    Route::crud('order/{reservationId}', 'OrderCrudController');
    Route::get('order/chef/{id}/finish', 'OrderCrudController@toggleReadyButton');
    Route::get('order/bartender/{id}/finish', 'OrderCrudController@orderDoneBartender');
    //--------------------------------------------------------------------------------------------------------
    Route::crud('customer', 'CustomerCrudController');
    Route::crud('food-category', 'FoodCategoryCrudController');

    //reservation admin panel---------------------------------------------------------------------------------
    Route::crud('reservation/show/{input}', 'ReservationCrudController');
    Route::get('reservation/{id}/receipt', 'ReservationCrudController@receipt');
    //-------------------------------------------------------------------------------------------------------

    Route::crud('dish-type', 'DishTypeCrudController');
    Route::crud('menu-item', 'MenuItemCrudController');
}); // this should be the absolute last line of this file
