<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'customer_information_files';
    protected $guarded    = ['id'];

    public function JifuriDatas() {
        return $this->hasMany('\App\Jifuri', 'customer_number', 'customer_number');
    }

    public function scopePref($query, $pref) {
        return $query->where('prefecture_code', '=', $pref);
    }
    public function scopeStore($query, $store) {
        return $query->where('store_number', '=', $store);
    }
    public function scopeSmallStore($query, $small_store) {
        return $query->where('small_store_number', '=', $small_store);
    }
    public function scopeArea($query, $area) {
        return $query->where('area_code', '=', $area);
    }
    
}
