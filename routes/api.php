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

// Routes for all Tenant users
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('get.authenticated.user');

    Route::group(['prefix' => 'customers'], function () {
        Route::get('', 'CustomerController@index')->name('get.customers');
        Route::post('', 'CustomerController@store')->name('create.new.customer');
        Route::put('/{customer}', 'CustomerController@update')->name('update.customer');
        Route::get('/{customer}', 'CustomerController@show')->name('retrieve.specific.customer');
    });

    Route::group(['prefix' => 'deal'], function () {
        Route::get('', 'DealController@index')->name('all.deals');
        Route::post('', 'DealController@store')->name('store.deal');
        Route::put('/{deal}', 'DealController@update')->name('update.deal');
        Route::get('/{deal}', 'DealController@show')->name('show.deal');
        Route::delete('/{deal}', 'DealController@delete')->name('delete.deal');

        Route::get('options', 'DealOptionsController@index')->name('deal.options');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::put('/{user}', 'UserController@update')->name('update.user');
        Route::get('/{user}', 'UserController@show')->name('retrieve.specific.user');
    });

    Route::post('calculate-payments', 'CalculatePaymentsController@store')->name('calculate.payments');
});

//Routes for Tenant Admin users
Route::group(['middleware' => ['auth:api', 'auth.admin']], function () {
    Route::group(['prefix' => 'users'], function () {
        Route::get('', 'UserController@index')->name('get.users');
        Route::post('', 'UserController@store')->name('create.new.user');
    });
});


Route::get('health-check', function () {
    return response()->json([
        'message' => 'The application is really, really healthy... like Chuck Norris healthy.',
        'success' => true
    ], 200, []);
});
