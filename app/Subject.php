<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'subject_codes';
    protected $guarded    = ['id'];
    
}
