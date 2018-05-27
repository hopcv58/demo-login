<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    // Define format id number.
    Route::pattern('id', '[0-9]{1,}');

    Route::name('user_info')->get('users/info', 'ApiUserController@info');

    Route::name('user_all')->get('users/all', 'ApiUserController@all');

    Route::name('user_update')->post('users/update', 'ApiUserController@update');

    Route::name('balance_deposit')->post('balance/deposit', 'ApiBalanceController@deposit');

    Route::name('balance_withdraw')->post('balance/withdraw', 'ApiBalanceController@withdraw');

    Route::name('balance_all')->get('balance/all', 'ApiBalanceController@all');

    Route::name('balance_address')->post('balance/address', 'ApiBalanceController@getAddress');

    Route::name('trades_all')->get('trades/all', 'ApiTradeController@all');

    Route::name('trades_pending')->get('trades/pending', 'ApiTradeController@pending');

    Route::name('order_create')->post('order/create', 'ApiTradeController@createOrder');

    Route::name('order_list')->get('order/list', 'ApiTradeController@listOrder');

});

Route::name('order_all')->get('order/all', 'ApiTradeController@allOrder');

Route::name('user_create')->post('users/create', 'ApiUserController@create');