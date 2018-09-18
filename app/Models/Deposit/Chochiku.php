<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Chochiku extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'chochiku_account_ledgers';
    protected $guarded    = ['id'];

}
