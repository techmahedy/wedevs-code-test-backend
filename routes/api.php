<?php

use Illuminate\Support\Facades\Route;

Route::post('signup', 'AuthController@register');
Route::post('signin', 'AuthController@login');

Route::group(['middleware' => 'auth:api'], function () {
    
    Route::post('signout', 'AuthController@logout');
    Route::get('user', 'AuthController@getAuthenticatedUser');
    
    //product
    Route::get('/products', 'ProductController@index');
    Route::get('/product/{id}', 'ProductController@show');
    Route::post('/product', 'ProductController@store');
    Route::post('/product/{id}', 'ProductController@update');
    Route::delete('/product/{id}', 'ProductController@destroy');

    //order
    Route::post('/order', 'OrderController@order');
    Route::get('/order/list', 'OrderController@orderList');
    Route::post('/order/edit/{id}', 'OrderController@editOrder');
    Route::post('/order/status', 'OrderController@orderStatusUpdate');
    Route::get('/order/deliver/list', 'OrderController@deliverOrderList');
    Route::get('/order/{id}', 'OrderController@orderDetails');

    //notification
    Route::get('/notifications', 'NotificationController@getNotification');
    Route::get('/activities', 'NotificationController@getOrderActivity');

});
