<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Taxation extends Model
{
    protected $connection = 'mysql_master';
    protected $table      = 'deposit_taxation_codes';
    protected $guarded    = ['id'];

}
