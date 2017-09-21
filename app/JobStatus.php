<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{

    protected $connection = 'mysql_suisin';
    protected $table      = 'job_status';
    protected $guarded    = ['id'];

}
