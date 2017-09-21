<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consignor extends Model
{

    protected $connection = 'mysql_suisin';
    protected $table      = 'consignors';
    protected $guarded    = ['id'];

    public function JifuriDatas() {
        return $this->hasMany('App\Jifuri', 'consignor_code', 'consignor_code');
    }

    public function Group() {
        return $this->hasOne('App\ConsignorGroup', 'id', 'consignor_group_id');
    }

}
