<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personality extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'personality_codes';
    protected $guarded    = ['id'];
    
}
