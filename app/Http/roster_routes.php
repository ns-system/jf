<?php

Route::group(['middleware' => 'auth', 'prefix' => '/admin', 'as' => 'admin::'], function() {
    /**
     * Middleware : roster_admin
     * Prefix     : /roster
     * As         : roster::
     */
    Route::group(['middleware' => 'roster_admin', 'prefix' => '/roster', 'as' => 'roster::'], function() {
        Route::get('/config/{system}/{category}',         ['as' => 'index',  'uses' => 'SuisinAdminController@show']);
        Route::get('/config/{system}/{category}/export',  ['as' => 'export', 'uses' => 'SuisinAdminController@export']);
        Route::post('/config/{system}/{category}/import', ['as' => 'import', 'uses' => 'SuisinAdminController@import']);
        Route::post('/config/{system}/{category}/upload', ['as' => 'upload', 'uses' => 'SuisinAdminController@upload']);
        Route::group(['as' => 'user::', 'prefix' => '/user'], function() {
            Route::get('/',            ['as' => 'index',  'uses' => 'RosterUserController@indexAdmin']);
            Route::get('/{id}',        ['as' => 'show',   'uses' => 'RosterUserController@showAdmin']);
            Route::post('/edit/{id}',  ['as' => 'edit',   'uses' => 'RosterUserController@editAdmin']);
            Route::get('/delete/{id}', ['as' => 'delete', 'uses' => 'RosterUserController@deleteAdmin']);
        });
        Route::group(['as' => 'csv::', 'prefix' => '/csv'], function() {
            Route::get('/',                   ['as' => 'index',  'uses' => 'RosterCsvExportController@index']);
            Route::get('/list/{ym}',          ['as' => 'show',   'uses' => 'RosterCsvExportController@show']);
            Route::get('/edit/{ym}/{id}',     ['as' => 'edit',   'uses' => 'RosterCsvExportController@edit']);
            Route::post('/update/{ym}',       ['as' => 'update', 'uses' => 'RosterCsvExportController@update']);
            Route::get('/search/{ym}',        ['as' => 'search', 'uses' => 'RosterCsvExportController@search']);
            Route::get('/export/{ym}/all',    ['as' => 'export', 'uses' => 'RosterCsvExportController@rawDataExport']);
            Route::get('/export/{ym}/{type}', ['as' => 'export', 'uses' => 'RosterCsvExportController@export']);
        });
    });
});

Route::group(['middleware' => 'auth', 'prefix' => '/app', 'as' => 'app::'], function() {
    Route::group(['prefix' => '/roster', 'as' => 'roster::'], function() {
        /**
         * Role       : auth
         * Prefix     : /user
         * As         : user::
         */
        Route::group(['prefix' => '/user', 'as' => 'user::'], function() {
            Route::get('/{id}',       ['as' => 'show', 'uses' => 'RosterUserController@index']);
            Route::post('/edit/{id}', ['as' => 'edit', 'uses' => 'RosterUserController@edit']);
        });
        /**
         * Role       : roster_user
         * Middleware : auth
         * Prefix     : /app
         * As         : app::
         */
        Route::group(['middleware' => 'roster_user'], function() {
            Route::get('/', ['as' => 'home', 'uses' => 'RosterController@home']);

            Route::group(['as' => 'calendar::', 'prefix' => '/calendar'], function() {
                Route::get('/{year_and_month}', ['as' => 'show', 'uses' => 'RosterController@show']);
                /**
                 * As         : form::
                 */
                Route::group(['as' => 'form::'], function() {
                    Route::get('/form/{year_and_month}/{day}',        ['as' => 'index', 'uses' => 'RosterController@form']);
                    Route::get('/delete/{id}',                        ['as' => 'delete', 'uses' => 'RosterController@delete']);
                    Route::post('/plan/edit/{year_and_month}/{id}',   ['as' => 'plan_edit', 'uses' => 'RosterController@editPlan']);
                    Route::post('/actual/edit/{year_and_month}/{id}', ['as' => 'actual_edit', 'uses' => 'RosterController@editActual']);
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
                    Route::get('/list',                                                                    ['as' => 'index',           'uses' => 'RosterAcceptController@index']);
                    Route::get('/list/{year_and_month}/divisions/{division_id}/users/{user_id?}{status?}', ['as' => 'calendar',        'uses' => 'RosterAcceptController@calendarIndex']);
                    Route::post('/list/{year_and_month}/divisions/{division_id}/users/{user_id}/edit',     ['as' => 'calendar_accept', 'uses' => 'RosterAcceptController@calendarAccept']);
//                    Route::post('/calendar/edit',                                 ['as' => 'calendar_accept', 'uses' => 'RosterAcceptController@calendarAccept']);
                });
                Route::group(['as' => 'work_plan::', 'prefix' => '/work_plan'], function() {
                    Route::get('/',                                      ['as' => 'index',    'uses' => 'RosterWorkPlanController@index']);
                    Route::get('/{year_and_month}',                      ['as' => 'division', 'uses' => 'RosterWorkPlanController@division']);
                    Route::get('/list/{year_and_month}/{user_id}',       ['as' => 'list',     'uses' => 'RosterWorkPlanController@userList']);
                    Route::post('/list/edit/{year_and_month}/{user_id}', ['as' => 'edit',     'uses' => 'RosterWorkPlanController@edit']);
                });
            });
            /**
             * Middleware : roster_chief
             * As         : accept::
             * Prefix     : /accept
             */
            Route::group(['middleware' => 'roster_chief', 'as' => 'chief::', 'prefix' => '/chief'], function() {
                Route::get('/',        ['as' => 'index',  'uses' => 'RosterChiefController@index']);
                Route::post('/update', ['as' => 'update', 'uses' => 'RosterChiefController@update']);
            });
        });
    });
});
