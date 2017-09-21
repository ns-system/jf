<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'stores';
    protected $guarded    = ['id'];

    public function Prefecture(){
        return $this->belongsTo('App\Prefecture', 'prefecture_code', 'prefecture_code');
    }
    public function SmallStores(){
        return $this->hasMany('App\SmallStore', 'store_number', 'store_number');
    }
}
