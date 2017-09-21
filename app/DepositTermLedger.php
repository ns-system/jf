<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositTermLedger extends Model
{
    protected $connection = 'mysql_zenon';
    protected $table      = 'deposit_term_ledgers';
    protected $guarded    = ['id'];

//    public function Consignors() {
//        return $this->belongsTo('App\Consignor', 'consignor_group_id', 'id');
//    }

}
