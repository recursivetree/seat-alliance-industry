<?php

Route::group([
    'namespace'  => 'RecursiveTree\Seat\AllianceIndustry\Http\Controllers',
    'middleware' => ['web', 'auth'],
    'prefix' => 'allianceindustry',
], function () {
    Route::get('/orders', [
        'as'   => 'allianceindustry.orders',
        'uses' => 'AllianceIndustryController@orders',
        'middleware' => 'can:allianceindustry.view_orders'
    ]);

    Route::get('/deliveries', [
        'as'   => 'allianceindustry.deliveries',
        'uses' => 'AllianceIndustryController@deliveries',
        'middleware' => 'can:allianceindustry.create_deliveries'
    ]);

    Route::get('/order/{id}/details', [
        'as'   => 'allianceindustry.orderDetails',
        'uses' => 'AllianceIndustryController@orderDetails',
        'middleware' => 'can:allianceindustry.view_orders'
    ]);

    Route::post('/order/{id}/deliveries/add', [
        'as'   => 'allianceindustry.addDelivery',
        'uses' => 'AllianceIndustryController@addDelivery',
        'middleware' => 'can:allianceindustry.create_deliveries'
    ]);

    Route::post('/order/{id}/deliveries/state', [
        'as'   => 'allianceindustry.setDeliveryState',
        'uses' => 'AllianceIndustryController@setDeliveryState',
        'middleware' => 'can:allianceindustry.create_deliveries'
    ]);

    Route::post('/order/delete', [
        'as'   => 'allianceindustry.deleteOrder',
        'uses' => 'AllianceIndustryController@deleteOrder',
        'middleware' => 'can:allianceindustry.create_orders'
    ]);

    Route::post('/order/{id}/deliveries/delete', [
        'as'   => 'allianceindustry.deleteDelivery',
        'uses' => 'AllianceIndustryController@deleteDelivery',
        'middleware' => 'can:allianceindustry.create_deliveries'
    ]);

    Route::get('/orders/create', [
        'as'   => 'allianceindustry.createOrder',
        'uses' => 'AllianceIndustryController@createOrder',
        'middleware' => 'can:allianceindustry.create_orders'
    ]);

    Route::post('/orders/submit', [
        'as'   => 'allianceindustry.submitOrder',
        'uses' => 'AllianceIndustryController@submitOrder',
        'middleware' => 'can:allianceindustry.create_orders'
    ]);
});