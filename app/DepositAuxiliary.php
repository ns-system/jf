<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositAuxiliary extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'deposit_auxiliary_codes';
    protected $guarded    = ['id'];

}
