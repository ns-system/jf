<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Toza extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'toza_account_ledgers';
    protected $guarded    = ['id'];

}
