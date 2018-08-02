<?php

use Illuminate\Http\Request;

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

    // @TODO: Add external service hooks for this group
    Route::group(['prefix' => '/account', 'middleware' => 'status:self,admin'], function() {
        Route::get('/{userid?}', 'AccountController@getIndex');
        Route::post('/{userid?}', 'AccountController@postIndex');
        Route::group(['middleware' => 'role:admin'], function() {
            Route::post('/new', 'AccountController@postNew');
            Route::delete('/{userid}', 'AccountController@deleteIndex');
            Route::post('/{userid}/roles', 'AccountController@postRole');
            Route::delete('/{userid}/roles', 'AccountController@deleteRole');
        });
        Route::get('/roles', 'AccountController@getRoles');
    });
});

Route::get("/", [
    'as' => 'l5-swagger.api',
    'middleware' => config('l5-swagger.routes.middleware.api', []),
    'uses' => '\L5Swagger\Http\Controllers\SwaggerController@api',
]);
