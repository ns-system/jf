<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControlDivision extends Model
{

    protected $connection = 'mysql_sinren';
    protected $table      = 'control_divisions';
    protected $guarded    = ['id'];

    public function scopeUser($query, $user_id) {
        return $query->where('user_id', $user_id);
    }
    public function scopeJoinUsers($query, $user_id) {
        return $query->leftJoin('sinren_db.sinren_users', 'control_divisions.division_id', '=', 'sinren_users.division_id')
                        ->leftJoin('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                        ->where('control_divisions.user_id', '=', $user_id)
        ;
    }

    public function scopeJoinDivisions($query) {
        return $query->leftJoin('sinren_db.sinren_divisions', 'control_divisions.division_id', '=', 'sinren_divisions.division_id');
    }

    public function Division() {
        return $this->hasOne('\App\Division', 'division_id', 'division_id');
    }

}
