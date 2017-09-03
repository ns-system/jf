<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SinrenDivision extends Model
{

    protected $connection = 'mysql_sinren';
    protected $table      = 'sinren_divisions';
    protected $guarded    = ['id'];

    public function SinrenUsers() {
//        return $this->hasMany('\App\RosterUser', 'division_id','division_id');
        return $this->hasMany('\App\SinrenUser', 'division_id', 'division_id');
    }

}
