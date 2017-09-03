<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{

    protected $connection = 'mysql_roster';
    protected $table      = 'rest_reasons';
    protected $guarded    = ['id'];
}
