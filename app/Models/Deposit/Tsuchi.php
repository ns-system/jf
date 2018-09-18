<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Tsuchi extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'tsuchi_account_ledgers';
    protected $guarded    = ['id'];

}
