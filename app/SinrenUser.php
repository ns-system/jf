<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SinrenUser extends Model
{

    protected $connection = 'mysql_sinren';
    protected $table      = 'sinren_users';
    protected $guarded    = ['id'];

    public function scopeUser($query) {
        if (\Auth::Check())
        {
            $id = \Auth::user()->id;
        }
        return $query->where('user_id', '=', $id);
    }

    public function scopeDivision($query, $division) {
        return $query->where('division_id', $division);
    }

//    public function RosterUsers() {
////        echo "aa";
////        return $this;
//        return $this->hasMany('\App\SinrenUser', 'user_id', 'user_id');
//    }
    public function RosterUser() {
//        echo "aa";
//        return $this;
        return $this->hasOne('\App\RosterUser', 'user_id', 'user_id');
    }

    public function SinrenDivision() {
        return $this->belongsTo('\App\SinrenDivision', 'division_id', 'division_id');
    }

//    public function User() {
//        return $this->hasOne('\App\User', 'id', 'user_id');
//    }
//    public function SinrenDivision() {
//        return $this->hasOne('\App\SinrenDivision', 'division_id', 'division_id');
//    }
}
