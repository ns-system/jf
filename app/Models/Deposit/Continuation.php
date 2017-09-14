<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Continuation extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'deposit_continuation_codes';
    protected $guarded    = ['id'];

}
