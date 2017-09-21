<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsignorGroup extends Model
{

    protected $connection = 'mysql_suisin';
    protected $table      = 'consignor_groups';
    protected $guarded    = ['id'];

    public function Consignors() {
        return $this->hasMany('App\Consignor', 'consignor_group_id', 'id');
    }

    public function CreateUser() {
        return $this->hasOne('App\User', 'id', 'create_user_id');
    }

    public function ModifyUser() {
        return $this->hasOne('App\User', 'id', 'modify_user_id');
    }

}
