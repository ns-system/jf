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
            Route::post('/create', ['as' => 'create', 'uses' => 'ProcessStatusController@create']);
            Route::get('/status/{id}', ['as' => 'status', 'uses' => 'ProcessStatusController@show']);
            Route::get('/search/{id}', ['as' => 'search', 'uses' => 'ProcessStatusController@search']);


//            Route::post('/sample',        ['as' => 'copy_comfirm',   'uses' => 'ProcessStatusController@sample']);


            Route::get('/failed/{id}', ['as' => 'failed', 'uses' => 'ProcessStatusController@processFailed']);
//            Route::get('/copy_confirm/export/{id}', ['as' => 'copy_export',   'uses' => 'ProcessStatusController@exportPreviousProcessCsvList']);


            Route::get('/copy_confirm/{id}', ['as' => 'copy_confirm', 'uses' => 'ProcessStatusController@copyConfirm']);
            Route::any('/copy_dispatch/{id}', ['as' => 'copy_dispatch', 'uses' => 'ProcessStatusController@dispatchCopyJob']);
            Route::get('/copy/{id}/{job_id}', ['as' => 'copy', 'uses' => 'ProcessStatusController@copy']);

            Route::get('/import_confirm/{id}/{job_id}', ['as' => 'import_confirm', 'uses' => 'ProcessStatusController@importConfirm']);
            Route::post('/import_dispatch/{id}/{job_id}', ['as' => 'import_dispatch', 'uses' => 'ProcessStatusController@dispatchImportJob']);
            Route::get('/import/{id}/{job_id}', ['as' => 'import', 'uses' => 'ProcessStatusController@import']);

            Route::any('/importing/{id}/{job_id}', ['as' => 'importing', 'uses' => 'ProcessStatusController@importAjax']);
            Route::any('/copying/{id}/{job_id}', ['as' => 'copying', 'uses' => 'ProcessStatusController@copyAjax']);

            Route::get('/export/{id}', ['as' => 'export', 'uses' => 'ProcessStatusController@exportProcessList']);
            Route::get('/export_nothing/{id}', ['as' => 'export_nothing', 'uses' => 'ProcessStatusController@exportNothingList']);
//            Route::get('/export_not_exist/{id}',          ['as' => 'export_not_exist', 'uses' => 'ProcessStatusController@exportNotExistList']);
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
            Route::post('/{system}/{category}/upload', ['as' => 'upload', 'uses' => 'SuisinAdminController@upload']);
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
            Route::get('/', ['as' => 'index', 'uses' => 'RosterUserController@indexAdmin']);
            Route::get('/{id}', ['as' => 'show', 'uses' => 'RosterUserController@showAdmin']);
            Route::post('/edit', ['as' => 'edit', 'uses' => 'RosterUserController@editAdmin']);
            Route::get('/delete/{id}', ['as' => 'delete', 'uses' => 'RosterUserController@deleteAdmin']);
        });
        Route::group(['as' => 'csv::', 'prefix' => '/csv'], function() {
            Route::get('/', ['as' => 'index', 'uses' => 'RosterCsvExportController@index']);
            Route::get('/list/{ym}', ['as' => 'show', 'uses' => 'RosterCsvExportController@show']);
            Route::get('/edit/{ym}/{id}', ['as' => 'edit', 'uses' => 'RosterCsvExportController@edit']);
            Route::post('/update/{ym}', ['as' => 'update', 'uses' => 'RosterCsvExportController@update']);
            Route::get('/search/{ym}', ['as' => 'search', 'uses' => 'RosterCsvExportController@search']);
            Route::get('/export/{ym}/{type}', ['as' => 'export', 'uses' => 'RosterCsvExportController@export']);
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
//    /**
//     * Role       : auth
//     * Prefix     : /user
//     * As         : user::
//     */
//    Route::group(['prefix' => '/user', 'as' => 'user::'], function() {
//        Route::get('/{id}', ['as' => 'show', 'uses' => 'UserController@show']);
//        Route::post('/name/{id}', ['as' => 'name', 'uses' => 'UserController@name']);
//        Route::post('/icon/{id}', ['as' => 'icon', 'uses' => 'UserController@userIcon']);
//        Route::post('/division/{id}', ['as' => 'division', 'uses' => 'UserController@division']);
//        Route::post('/password/{id}', ['as' => 'password', 'uses' => 'UserController@password']);
//    });

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
                    Route::post('/plan/edit/{ym}/{id}', ['as' => 'plan_edit', 'uses' => 'RosterController@editPlan']);
                    Route::post('/actual/edit/{ym}/{id}', ['as' => 'actual_edit', 'uses' => 'RosterController@editActual']);
                });
            });
            /**
             * As         : divisions::
             * Prefix     : /divisions
             */
            Route::group(['as' => 'division::', 'prefix' => '/division'], function() {
                Route::get('/check', ['as' => 'check', 'uses' => 'RosterListController@check']);
                Route::get('/home/{div}', ['as' => 'index', 'uses' => 'RosterListController@index']);
                Route::get('/list/{div}/{ym}', ['as' => 'show', 'uses' => 'RosterListController@show']);
            });
            /**
             * Middleware : roster_proxy
             * As         : accept::
             * Prefix     : /accept
             */
            Route::group(['middleware' => 'roster_proxy'], function() {
                Route::group(['as' => 'accept::', 'prefix' => '/accept'], function() {
                    Route::get('/home', ['as' => 'index', 'uses' => 'RosterAcceptController@index']);
                    Route::get('/list/{ym}/{div}', ['as' => 'list', 'uses' => 'RosterAcceptController@show']);
                    Route::get('/calendar/{ym}/{div}', ['as' => 'calendar', 'uses' => 'RosterAcceptController@calendar']);
                    Route::post('/calendar/edit', ['as' => 'calendar_accept', 'uses' => 'RosterAcceptController@calendarAccept']);

                    Route::post('/edit/{type}/part/{id}', ['as' => 'part', 'uses' => 'RosterAcceptController@part']);
                    Route::post('/edit/{type}/all', ['as' => 'all', 'uses' => 'RosterAcceptController@all']);
                });
                Route::group(['as' => 'work_plan::', 'prefix' => '/work_plan'], function() {
                    Route::get('', ['as' => 'index', 'uses' => 'RosterWorkPlanController@index']);
                    Route::get('/{month}', ['as' => 'division', 'uses' => 'RosterWorkPlanController@division']);
                    Route::get('/list/{month}/{id}', ['as' => 'list', 'uses' => 'RosterWorkPlanController@userList']);
                    Route::post('/list/edit/{month}/{id}', ['as' => 'edit', 'uses' => 'RosterWorkPlanController@edit']);
                });
            });
            /**
             * Middleware : roster_chief
             * As         : accept::
             * Prefix     : /accept
             */
            Route::group(['middleware' => 'roster_chief', 'as' => 'chief::', 'prefix' => '/chief'], function() {
                Route::get('/home', ['as' => 'index', 'uses' => 'RosterChiefController@index']);
                Route::post('/update', ['as' => 'update', 'uses' => 'RosterChiefController@update']);
            });
        });
    });

    Route::group(['as' => 'nikocale::', 'prefix' => '/nikocale', 'middleware' => 'nikocale'], function() {
        Route::get('/index/{monthly_id?}', ['as' => 'index', 'uses' => 'NikocaleController@index']);
        Route::post('/store/{user_id}/{entered_on}', ['as' => 'store', 'uses' => 'NikocaleController@store']);
//        Route::post('/update/{id}',                   ['as' => 'update',  'uses' => 'NikocaleController@update']);
        Route::get('/destroy/{id}', ['as' => 'destroy', 'uses' => 'NikocaleController@destroy']);
    });
});
// ===========================================================================================================================
//
//Route::get('/roster/add/user', 'SinrenUserController@showRosterRegisterUser');
//Route::post('/roster/add/user/add', 'SinrenUserController@createRosterRegisterUser');
//Route::get('/roster/app/home', 'RosterController@showIndex');
//Route::get('/roster/app/calendar/{id?}', 'RosterController@showCalendar');
//
//Route::post('/roster/app/calendar/form', 'RosterController@showForm');
//Route::post('/roster/app/calendar/form/plan/edit', 'RosterController@editPlan');
//Route::post('/roster/app/calendar/form/actual/edit', 'RosterController@editActual');
//
//Route::get('/roster/app/list/{id?}', 'RosterController@showList');
//
//Route::get('/roster/chief/accept/{id?}', 'AcceptController@show');
//Route::post('/roster/chief/accept/edit/all', 'AcceptController@editAll');
//Route::post('/roster/chief/accept/edit/unit/{id?}', 'AcceptController@editUnit');
//
//Route::get('/roster/chief/proxy', 'ChiefController@show');
//Route::post('/roster/chief/proxy/edit{id?}', 'ChiefController@edit');
//Route::get('/model', function() {
//    $t = '\App\Masters\Common\Prefecture';
//    $m = \App\Masters\Common\Prefecture::find(1);
//    $m = new \App\Masters\Common\Prefecture();
//    $m = new $t;
//});

Route::get('/strlen/{str?}', function() {
    var_dump(\Input::all());
    $input = \Input::all();
    $tmp   = $input['str'];
    $buf   = substr($tmp, 0, -3);
    var_dump($buf);
    echo $buf;
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
//Route::get('/admin/roster/home', function() {
//    return view('roster.admin.home');
//});
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

Route::any('/test/file_upload', function() {
    $file = \Request::file('file');
    var_dump($file->getClientOriginalExtension());
    return $file;
//    return \Requrest::input();
//    return Response::make();
});
