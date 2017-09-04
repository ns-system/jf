<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::Controller('/auth', 'Auth\AuthController');
Route::controller('/password', 'Auth\PasswordController');
Route::get('/permission_error', ['as' => 'permission_error', 'uses' => 'IndexController@permissionError']);
Route::get('/', ['as' => 'index', 'uses' => 'IndexController@show']);
Route::get('/home', function() {
    return redirect('/');
});

// ===========================================================================================================================
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
            Route::get('/', ['as' => 'show', 'uses' => 'SuperUserController@show']);
            Route::get('/search', ['as' => 'search', 'uses' => 'SuperUserController@search']);
            Route::get('/{id}', ['as' => 'detail', 'uses' => 'SuperUserController@user']);
            Route::post('/edit/{id}', ['as' => 'edit', 'uses' => 'SuperUserController@edit']);
        });
        /**
         * Prefix     : /month
         * As         : month::
         */
        Route::group(['prefix' => '/month', 'as' => 'month::'], function() {
            Route::get('/', ['as' => 'show', 'uses' => 'ProcessStatusController@index']);
            Route::post('/publish/{id}', ['as' => 'publish', 'uses' => 'ProcessStatusController@publish']);
            Route::get('/status/{id}', ['as' => 'status', 'uses' => 'ProcessStatusController@show']);
            Route::get('/search/{id}', ['as' => 'search', 'uses' => 'ProcessStatusController@search']);
        });
        /**
         * Prefix     : /config
         * As         : config::
         */
        Route::group(['prefix' => '/config', 'as' => 'config::'], function() {
//            Route::get('/{system}/',                   ['as' => 'home',    'uses'=>'SuisinAdminController@index']);
            Route::get('/{system}/{category}', ['as' => 'index', 'uses' => 'SuisinAdminController@show']);
            Route::get('/{system}/{category}/export', ['as' => 'export', 'uses' => 'SuisinAdminController@export']);
            Route::post('/{system}/{category}/import', ['as' => 'import', 'uses' => 'SuisinAdminController@import']);
            Route::post('/{system}/{category}/upload', ['as' => 'uppload', 'uses' => 'SuisinAdminController@upload']);
        });
    });
    /**
     * Middleware : suisin_admin
     * Prefix     : /suisin
     * As         : suisin::
     */
    Route::group(['middleware' => 'suisin_admin', 'prefix' => '/suisin', 'as' => 'suisin::'], function() {
        Route::get('/config/{system}/', ['as' => 'home', 'uses' => 'SuisinAdminController@index']);
        Route::get('/config/{system}/{category}', ['as' => 'index', 'uses' => 'SuisinAdminController@show']);
        Route::get('/config/{system}/{category}/export', ['as' => 'export', 'uses' => 'SuisinAdminController@export']);
        Route::post('/config/{system}/{category}/import', ['as' => 'import', 'uses' => 'SuisinAdminController@import']);
        Route::post('/config/{system}/{category}/upload', ['as' => 'upload', 'uses' => 'SuisinAdminController@upload']);
    });

    /**
     * Middleware : roster_admin
     * Prefix     : /roster
     * As         : roster::
     */
    Route::group(['middleware' => 'roster_admin', 'prefix' => '/roster', 'as' => 'roster::'], function() {
//            Route::get('/',                   ['as' => 'home',   'uses' => 'SuisinAdminController@index']);
        Route::get('/config/{system}/{category}', ['as' => 'index', 'uses' => 'SuisinAdminController@show']);
        Route::get('/config/{system}/{category}/export', ['as' => 'export', 'uses' => 'SuisinAdminController@export']);
        Route::post('/config/{system}/{category}/import', ['as' => 'import', 'uses' => 'SuisinAdminController@import']);
        Route::post('/config/{system}/{category}/upload', ['as' => 'upload', 'uses' => 'SuisinAdminController@upload']);
        Route::group(['as' => 'user::', 'prefix' => '/user'], function() {
            Route::get('/',              ['as'=>'index',  'uses'=>'RosterUserController@indexAdmin']);
            Route::get('/{id}',          ['as'=>'show',   'uses'=>'RosterUserController@showAdmin']);
            Route::post('/edit',         ['as'=>'edit',   'uses'=>'RosterUserController@editAdmin']);
            Route::get('/delete/{id}', ['as'=>'delete', 'uses'=>'RosterUserController@deleteAdmin']);
        });
    });
});
// ===========================================================================================================================

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
        Route::get('/{id}', ['as' => 'show', 'uses' => 'UserController@show']);
        Route::post('/name/{id}', ['as' => 'name', 'uses' => 'UserController@name']);
        Route::post('/icon/{id}', ['as' => 'icon', 'uses' => 'UserController@userIcon']);
        Route::post('/division/{id}', ['as' => 'division', 'uses' => 'UserController@division']);
        Route::post('/password/{id}', ['as' => 'password', 'uses' => 'UserController@password']);
    });

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
        /**
         * Role       : roster_user
         * Middleware : auth
         * Prefix     : /app
         * As         : app::
         */
        Route::group(['middleware' => 'roster_user'], function() {
            Route::get('/home', ['as' => 'home', 'uses' => 'RosterController@home']);

            Route::group(['as' => 'calendar::', 'prefix' => '/calendar'], function() {
                Route::get('/{ym}', ['as' => 'show', 'uses' => 'RosterController@show']);
                /**
                 * As         : form::
                 */
                Route::group(['as' => 'form::'], function() {
                    Route::get('/form/{ym}/{d}', ['as' => 'index', 'uses' => 'RosterController@form']);
                    Route::get('/delete/{id}', ['as' => 'delete', 'uses' => 'RosterController@delete']);
                    Route::post('/plan/edit', ['as' => 'plan_edit', 'uses' => 'RosterController@editPlan']);
                    Route::post('/actual/edit', ['as' => 'actual_edit', 'uses' => 'RosterController@editActual']);
                });
            });
            /**
             * As         : divisions::
             * Prefix     : /divisions
             */
            Route::group(['as' => 'division::', 'prefix' => '/division'], function() {
                Route::get('/check',           ['as' => 'check', 'uses' => 'RosterListController@check']);
                Route::get('/home/{div}',      ['as' => 'index', 'uses' => 'RosterListController@index']);
                Route::get('/list/{div}/{ym}', ['as' => 'show',  'uses' => 'RosterListController@show']);
            });
            /**
             * Middleware : roster_proxy
             * As         : accept::
             * Prefix     : /accept
             */
            Route::group(['middleware'=>'roster_proxy','as' => 'accept::', 'prefix' => '/accept'], function() {
                Route::get('/home',                   ['as' => 'index',    'uses' => 'RosterAcceptController@index']);
                Route::get('/list/{ym}/{div}',        ['as' => 'list',     'uses' => 'RosterAcceptController@show']);
                Route::post('/edit/{type}/part/{id}', ['as' => 'part',     'uses' => 'RosterAcceptController@part']);
                Route::post('/edit/{type}/all',       ['as' => 'all',      'uses' => 'RosterAcceptController@all']);
//                Route::post('/edit/actual/part//{id}', ['as' => 'actual_part', 'uses' => 'RosterAcceptController@actualPart']);
//                Route::post('/edit/actual/all',              ['as' => 'actual_all',  'uses' => 'RosterAcceptController@actualAll']);
            });
            /**
             * Middleware : roster_proxy
             * As         : accept::
             * Prefix     : /accept
             */
            Route::group(['middleware'=>'roster_chief','as' => 'chief::', 'prefix' => '/chief'], function() {
                Route::get('/home',    ['as' => 'index',    'uses' => 'RosterChiefController@index']);
                Route::post('/update', ['as' => 'update',   'uses' => 'RosterChiefController@update']);
            });
        });
    });
});
// ===========================================================================================================================


Route::get('/roster/add/user', 'SinrenUserController@showRosterRegisterUser');
Route::post('/roster/add/user/add', 'SinrenUserController@createRosterRegisterUser');
Route::get('/roster/app/home', 'RosterController@showIndex');
Route::get('/roster/app/calendar/{id?}', 'RosterController@showCalendar');

Route::post('/roster/app/calendar/form', 'RosterController@showForm');
Route::post('/roster/app/calendar/form/plan/edit', 'RosterController@editPlan');
Route::post('/roster/app/calendar/form/actual/edit', 'RosterController@editActual');

Route::get('/roster/app/list/{id?}', 'RosterController@showList');

Route::get('/roster/chief/accept/{id?}', 'AcceptController@show');
Route::post('/roster/chief/accept/edit/all', 'AcceptController@editAll');
Route::post('/roster/chief/accept/edit/unit/{id?}', 'AcceptController@editUnit');

Route::get('/roster/chief/proxy', 'ChiefController@show');
Route::post('/roster/chief/proxy/edit{id?}', 'ChiefController@edit');

Route::get('/strlen/{str?}', function() {
    var_dump(\Input::all());
    $input = \Input::all();
    $tmp   = $input['str'];
    $buf   = substr($tmp, 0, -3);
    var_dump($buf);
});

//Route::get('/mysample',function(\App\Services\UserSample $test){
//Route::get('/mysample',function(){
//    echo "Main";
//    return \Payment::pay(200);
//});
//Route::get('/roster/chief/accept/', 'AcceptController@show');
//Route::post('/roster/app/calendar/plan/edit', 'RosterController@planEdit');
//Route::post('/roster/app/calendar/plan/delete', 'RosterController@planDelete');
//Route::post('/roster/app/calendar/actual/edit', 'RosterController@actualEdit');
//Route::post('/roster/app/calendar/actual/delete', 'RosterController@actualDelete');
//Route::get('/home', function() {
//    return redirect('/');
//});

Route::get('/info', function() {
    phpinfo();
});
Route::get('/phpmyadmin', function() {
    return Redirect::to('http://cvs.phpmyadmin');
});

//Route::get('/admin/suisin/home', 'SuisinAdminController@index');

Route::get('/admin/roster/home', function() {
    return view('roster.admin.home');
});

Route::get('/isok', ['middleware' => 'auth', function() {
        echo "ok";
    }]);

//Route::get('/my_error', function() {
//    echo "err";
//    Log::info('実はエラー', ['id' => 1]);
//});


//Route::get('/admin/roster/division', 'DivisionController@showDivision');
//Route::get('/admin/roster/division/export', 'DivisionController@exportDivision');
//Route::post('/admin/roster/division/import', 'DivisionController@importDivision');
//Route::post('/admin/roster/division/import/upload', 'DivisionController@uploadDivision');
//
//Route::get('/admin/roster/work_type', 'WorkTypeController@showWorkType');
//Route::get('/admin/roster/work_type/export', 'WorkTypeController@exportWorkType');
//Route::post('/admin/roster/work_type/import', 'WorkTypeController@importWorkType');
//Route::post('/admin/roster/work_type/import/upload', 'WorkTypeController@uploadWorkType');
//
//Route::get('/admin/roster/rest', 'RestController@show');
//Route::get('/admin/roster/rest/export', 'RestController@export');
//Route::post('/admin/roster/rest/import', 'RestController@import');
//Route::post('/admin/roster/rest/import/upload', 'RestController@upload');
//
//Route::get('/admin/roster/holiday', 'HolidayController@show');
//Route::get('/admin/roster/holiday/export', 'HolidayController@export');
//Route::post('/admin/roster/holiday/import', 'HolidayController@import');
//Route::post('/admin/roster/holiday/import/upload', 'HolidayController@upload');
//
//Route::post('/sample/search', 'SampleController@searchCustomer');
//Route::any('/sample/export', 'SampleController@exportSample');
//Route::any('/sample', 'SampleController@showSample');