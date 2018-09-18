<?php

namespace App\Services\Traits\Testing;

trait DbDisconnectable
{

    public function disconnect() {
        $msg = 'error.';
        try {
            \DB::disconnect();
        } catch (\Exception $exc) {
            echo $msg;
        }

        try {
            \DB::disconnect('laravel_db');
        } catch (\Exception $exc) {
            echo $msg;
        }

        try {
            \DB::disconnect('suisin_db');
        } catch (\Exception $exc) {
            echo $msg;
        }

        try {
            \DB::disconnect('roster_db');
        } catch (\Exception $exc) {
            echo $msg;
        }

        try {
            \DB::disconnect('zenon_db');
        } catch (\Exception $exc) {
            echo $msg;
        }

        try {
            \DB::disconnect('sinren_db');
        } catch (\Exception $exc) {
            echo $msg;
        }

        try {
            \DB::disconnect('master_db');
        } catch (\Exception $exc) {
            echo $msg;
        }
    }

}
