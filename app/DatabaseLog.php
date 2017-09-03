<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DatabaseLog extends Model
{

    protected $connection = 'mysql_laravel';
    protected $table      = 'database_logs';
    protected $guarded    = ['id'];

    
    public function User(){
        return $this->hasOne('\App\User','id','user_id');
    }
}
