<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkType extends Model
{
    protected $connection = 'mysql_roster';
    protected $table      = 'work_types';
    protected $guarded    = ['id'];

}
