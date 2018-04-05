<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositAmount extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'deposit_amounts';
    protected $guarded    = ['id'];

}
