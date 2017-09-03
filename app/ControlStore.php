<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControlStore extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'control_stores';
    protected $guarded    = ['id'];
    
    public function SmallStores(){
        return $this->belongsTo('App\SmallStore', 'control_store_code', 'control_store_code');
    }
    
    public function scopeOrderAsc($query){
        return $query->orderBy('control_store_code', 'asc');
    }
}
