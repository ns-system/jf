<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Tsumitei extends Model
{
    protected $connection = 'mysql_zenon';
    protected $table      = 'tsumitei_account_ledgers';
    protected $guarded    = ['id'];
}
