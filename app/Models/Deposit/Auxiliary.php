<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Auxiliary extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'deposit_auxiliary_codes';
    protected $guarded    = ['id'];

}
