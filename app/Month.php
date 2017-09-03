<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'monthly_master';
    
//    public function JifuriDatas(){
//        return $this->hasMany('App\Jifuri', 'monthly_id', 'monthly_id');
//    }
}
