<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AcceptProvider extends ServiceProvider
{

    protected $rows;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    public function __construct() {
        
    }

    public function accepts() {
        $records    = \DB::connection('mysql_sinren')
                ->table('control_divisions')
                ->leftJoin('sinren_data_db.sinren_users', 'sinren_users.division_id', '=', 'control_divisions.division_id')
                ->leftJoin('laravel_db.users', 'users.id', '=', 'sinren_users.user_id')
                ->leftJoin('roster_data_db.roster_users', 'roster_users.user_id', '=', 'sinren_users.user_id')
                ->leftJoin('roster_data_db.rosters', 'rosters.user_id', '=', 'roster_users.user_id')
                ->leftJoin('sinren_data_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
        ;
        $this->rows = $records;
        return $this;
//        return $records;
    }

    public function accepts_where($month_id, $divisions) {
        $table = $this->rows;
        $table->where(['rosters.month_id' => $month_id]);
        $table->where('sinren_users.user_id', '<>', \Auth::user()->id);
        $table->where(function($query) {
            $query->orWhere('is_plan_accept', '=', 0);
            $query->orWhere('is_actual_accept', '=', 0);
        });
//            $div_id = $div->division_id;
//            var_dump($div_id);
        $table->where(function($query) use ($divisions) {
            foreach ($divisions as $div) {
                $query->orWhere(['sinren_users.division_id' => $div->division_id]);
            }
        });
        return $table;
    }

}
