<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Zaikei extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'zaikei_account_ledgers';
    protected $guarded    = ['id'];

}
