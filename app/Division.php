<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $connection = 'mysql_sinren';
    protected $table      = 'sinren_divisions';
    protected $guarded    = ['id'];

}
