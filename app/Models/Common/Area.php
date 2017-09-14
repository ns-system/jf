<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'area_codes';
    protected $guarded    = ['id'];
    
//    public function Store(){
//        return $this->belongsTo('App\Store', 'store_number', 'store_number');
//    }
//    public function ControlStore(){
//        return $this->hasOne('App\ControlStore', 'control_store_code', 'control_store_code');
//    }
}
