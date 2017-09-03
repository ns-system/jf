<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'qualification_codes';
    protected $guarded    = ['id'];
    
}
