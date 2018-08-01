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

Route::group(['prefix' => '/auth'], function() {
        Route::post('/', 'AuthController@postIndex');
        Route::get('/logout', 'AuthController@getLogout');
    }
);

Route::group(['middleware'=>'apiauth'], function() {
    Route::get('/servers', 'DataController@getServers');
    Route::group(['middleware' => 'role:admin'], function() {
        Route::post("/servers/{id?}", "DataController@postServers");
        Route::delete("/servers/{id}", "DataController@deleteServer");
    });
    Route::get('/users/{id?}', 'DataController@getUser');

    Route::group(['prefix' => '/cad'], function() {
        Route::group(['middleware' => 'status:dispatch,self,admin'], function () {
            Route::post('/status/{userid?}', 'DataController@postUserStatus');
        });
    });

    Route::group(['prefix' => '/account', 'middleware' => 'status:self,admin'], function() {
        Route::post('/{id}', 'AccountController@postIndex');
    });
});

Route::get("/", [
    'as' => 'l5-swagger.api',
    'middleware' => config('l5-swagger.routes.middleware.api', []),
    'uses' => '\L5Swagger\Http\Controllers\SwaggerController@api',
]);
