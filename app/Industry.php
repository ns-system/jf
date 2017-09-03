<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'industry_codes';
    protected $guarded    = ['id'];
    
}
