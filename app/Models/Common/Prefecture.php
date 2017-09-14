<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'prefecture_codes';
    protected $guarded    = ['id'];

    public function Stores(){
        return $this->hasMany('App\Store', 'prefecture_code', 'prefecture_code');
    }

}
