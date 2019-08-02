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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'Api', 'prefix' => 'v1', 'as' => 'api.v1.'], function () {
    Route::group(['prefix' => 'list'], function () {
        Route::apiResource('flights', 'FlightListController', ['only' => ['index', 'show']]);
    });

    Route::group(['prefix' => 'detail'], function () {
        Route::apiResource('flights', 'FlightDetailController', ['only' => ['index', 'show']]);
    });

    Route::apiResource('flightdata', 'FlightDataController', ['only' => ['store']]);
});
