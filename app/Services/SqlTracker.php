<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//なぜサービスでテストケースを利用しようと思ったのか。セキュリティ的にあうあう。
//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

namespace App\Services;

/**
 * Description of ImportConfigService
 *
 * @author r-kawanishi
 */
//use DatabaseMigrations;

class SqlTracker
{

    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  illuminate.query  $event
     * @return void
     */
    public function handle($query, $bindings) {
        //
        if (env('APP_DEBUG'))
        {
            \Log::debug('EXECUTE SQL:[' . $query . ']', ['BINDINGS' => json_encode($bindings)]);
        }
    }

}
