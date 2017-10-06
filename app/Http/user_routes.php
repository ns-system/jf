<?php

// ===========================================================================================================================
/**
 * Role       : auth
 * Middleware : auth
 * Prefix     : /app
 * As         : app::
 */
Route::group(['middleware' => 'auth', 'prefix' => '/app', 'as' => 'app::'], function() {
    /**
     * Role       : auth
     * Prefix     : /user
     * As         : user::
     */
    Route::group(['prefix' => '/user', 'as' => 'user::'], function() {
        Route::get('/{id}',           ['as' => 'show', 'uses' => 'UserController@show']);
        Route::post('/name/{id}',     ['as' => 'name', 'uses' => 'UserController@name']);
        Route::post('/icon/{id}',     ['as' => 'icon', 'uses' => 'UserController@userIcon']);
        Route::post('/division/{id}', ['as' => 'division', 'uses' => 'UserController@division']);
        Route::post('/password/{id}', ['as' => 'password', 'uses' => 'UserController@password']);
    });
});
