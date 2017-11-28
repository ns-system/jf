<?php

/*
  |--------------------------------------------------------------------------
  | アプリケーションのルート
  |--------------------------------------------------------------------------
  |
  | ここでアプリケーションのルートを全て登録することが可能です。
  | 簡単です。ただ、Laravelへ対応するURIと、そのURIがリクエスト
  | されたときに呼び出されるコントローラを指定してください。
  |
 */

Route::Controller('/auth', 'Auth\AuthController');
Route::controller('/password', 'Auth\PasswordController');
Route::get('/permission_error', ['as' => 'permission_error', 'uses' => 'IndexController@permissionError']);
Route::get('/', ['as' => 'index', 'uses' => 'IndexController@show']);
Route::get('/home', function() {
    return redirect('/');
});

Route::get('/email', function() {
    return view('emails.sample_email_form');
});
Route::post('/email/send', function() {
    $addr = \Input::only('addr');
    Queue::push(new \App\Jobs\SampleMailJob($addr));
    echo 'At ' . date('Y-m-d H:i:s') . ', mail send.';
});

/**
 * Role       : auth user
 * Middleware : auth
 * Prefix     : /admin
 * As         : admin::
 */
Route::group(['middleware' => 'auth', 'prefix' => '/admin', 'as' => 'admin::'], function() {
    /**
     * Role       : super_user
     * Middleware : super_user
     * Prefix     : /super_user
     * As         : super::
     */
    Route::group(['middleware' => 'super_user', 'prefix' => '/super_user', 'as' => 'super::'], function() {
        /**
         * Prefix     : /user
         * As         : user::
         */
        Route::group(['prefix' => '/user', 'as' => 'user::'], function() {
            Route::get('/',           ['as' => 'show',   'uses' => 'SuperUserController@show']);
            Route::get('/search',     ['as' => 'search', 'uses' => 'SuperUserController@search']);
            Route::get('/{id}',       ['as' => 'detail', 'uses' => 'SuperUserController@user']);
            Route::post('/edit/{id}', ['as' => 'edit',   'uses' => 'SuperUserController@edit']);
        });
        /**
         * Prefix     : /month
         * As         : month::
         */
        Route::group(['prefix' => '/month', 'as' => 'month::'], function() {
            Route::get('/',              ['as' => 'show',    'uses' => 'ProcessStatusController@index']);
            Route::post('/publish/{id}', ['as' => 'publish', 'uses' => 'ProcessStatusController@publish']);
            Route::post('/create',       ['as' => 'create',  'uses' => 'ProcessStatusController@create']);
            Route::get('/status/{id}',   ['as' => 'status',  'uses' => 'ProcessStatusController@show']);
            Route::get('/search/{id}',   ['as' => 'search',  'uses' => 'ProcessStatusController@search']);
            Route::get('/failed/{id}',   ['as' => 'failed',  'uses' => 'ProcessStatusController@processFailed']);

            Route::get('/copy_confirm/{id}',  ['as' => 'copy_confirm',  'uses' => 'ProcessStatusController@copyConfirm']);
            Route::any('/copy_dispatch/{id}', ['as' => 'copy_dispatch', 'uses' => 'ProcessStatusController@dispatchCopyJob']);
            Route::get('/copy/{id}/{job_id}', ['as' => 'copy',          'uses' => 'ProcessStatusController@copy']);

            Route::get('/import_confirm/{id}/{job_id}',   ['as' => 'import_confirm', 'uses' => 'ProcessStatusController@importConfirm']);
            Route::post('/import_dispatch/{id}/{job_id}', ['as' => 'import_dispatch', 'uses' => 'ProcessStatusController@dispatchImportJob']);
            Route::get('/import/{id}/{job_id}',           ['as' => 'import', 'uses' => 'ProcessStatusController@import']);

            Route::any('/importing/{id}/{job_id}', ['as' => 'importing', 'uses' => 'ProcessStatusController@importAjax']);
            Route::any('/copying/{id}/{job_id}',   ['as' => 'copying',   'uses' => 'ProcessStatusController@copyAjax']);

            Route::get('/export/{id}',         ['as' => 'export',            'uses' => 'ProcessStatusController@exportProcessList']);
            Route::get('/export_nothing/{id}', ['as' => 'export_nothing',    'uses' => 'ProcessStatusController@exportNothingList']);
            Route::get('/consignor/show',      ['as' => 'consignor::show',   'uses' => 'ProcessStatusController@showConsignors']);
            Route::get('/consignor/create',    ['as' => 'consignor::create', 'uses' => 'ProcessStatusController@createConsignors']);
//            Route::get('/export_not_exist/{id}',          ['as' => 'export_not_exist', 'uses' => 'ProcessStatusController@exportNotExistList']);
        });
        /**
         * Prefix     : /config
         * As         : config::
         */
        Route::group(['prefix' => '/config', 'as' => 'config::'], function() {
//            Route::get('/{system}/',                   ['as' => 'home',    'uses'=>'SuisinAdminController@index']);
            Route::get('/{system}/{category}',         ['as' => 'index', 'uses' => 'SuisinAdminController@show']);
            Route::get('/{system}/{category}/export',  ['as' => 'export', 'uses' => 'SuisinAdminController@export']);
            Route::post('/{system}/{category}/import', ['as' => 'import', 'uses' => 'SuisinAdminController@import']);
            Route::post('/{system}/{category}/upload', ['as' => 'upload', 'uses' => 'SuisinAdminController@upload']);
        });
    });
    /**
     * Middleware : suisin_admin
     * Prefix     : /suisin
     * As         : suisin::
     */
    Route::group(['middleware' => 'suisin_admin', 'prefix' => '/suisin', 'as' => 'suisin::'], function() {
        Route::get('/config/{system}/',                   ['as' => 'home',   'uses' => 'SuisinAdminController@index']);
        Route::get('/config/{system}/{category}',         ['as' => 'index',  'uses' => 'SuisinAdminController@show']);
        Route::get('/config/{system}/{category}/export',  ['as' => 'export', 'uses' => 'SuisinAdminController@export']);
        Route::post('/config/{system}/{category}/import', ['as' => 'import', 'uses' => 'SuisinAdminController@import']);
        Route::post('/config/{system}/{category}/upload', ['as' => 'upload', 'uses' => 'SuisinAdminController@upload']);
    });
});
/**
 * Role       : auth
 * Middleware : auth
 * Prefix     : /app
 * As         : app::
 */
Route::group(['middleware' => 'auth', 'prefix' => '/app', 'as' => 'app::'], function() {
    /**
     * Role       : auth
     * Prefix     : /roster
     * As         : roster::
     */
    Route::group(['prefix' => '/roster', 'as' => 'roster::'], function() {
        /**
         * Role       : auth
         * Prefix     : /user
         * As         : user::
         */
        Route::group(['prefix' => '/user', 'as' => 'user::'], function() {
            Route::get('/', ['as' => 'show', 'uses' => 'RosterUserController@index']);
            Route::post('/edit/{id}', ['as' => 'edit', 'uses' => 'RosterUserController@edit']);
        });
    });

    Route::group(['as' => 'nikocale::', 'prefix' => '/nikocale', 'middleware' => 'nikocale'], function() {
        Route::get('/index/{monthly_id?}',           ['as' => 'index',   'uses' => 'NikocaleController@index']);
        Route::post('/store/{user_id}/{entered_on}', ['as' => 'store',   'uses' => 'NikocaleController@store']);
//        Route::post('/update/{id}',                   ['as' => 'update',  'uses' => 'NikocaleController@update']);
        Route::get('/destroy/{id}',                  ['as' => 'destroy', 'uses' => 'NikocaleController@destroy']);
    });
});

Route::get('/strlen/{str?}', function() {
    var_dump(\Input::all());
    $input = \Input::all();
    $tmp   = $input['str'];
    $buf   = substr($tmp, 0, -3);
    var_dump($buf);
    echo $buf;
});

Route::get('/info', function() {
    phpinfo();
});
Route::get('/phpmyadmin', function() {
    return Redirect::to('http://cvs.phpmyadmin');
});

Route::any('/test/file_upload', function() {
    $file = \Request::file('file');
    var_dump($file->getClientOriginalExtension());
    return $file;
//    return \Requrest::input();
//    return Response::make();
});
