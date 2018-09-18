<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Futsu extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'futsu_account_ledgers';
    protected $guarded    = ['id'];

}
