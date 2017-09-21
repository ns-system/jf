<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sample extends Model
{
    protected $connection = 'mysql_zenon';
    protected $table = 'sample';
    
}
