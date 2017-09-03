<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jifuri extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'jifuri_trading_files';

    public function Month() {
        return $this->belongsTo('App\Month', 'monthly_id', 'monthly_id');
    }

    public function Consignor() {
        return $this->hasOne('App\Consignor', 'consignor_code', 'consignor_code');
    }
    
    public function Customer(){
        return $this->hasOne('App\Customer','customer_number','customer_number');
    }

}
