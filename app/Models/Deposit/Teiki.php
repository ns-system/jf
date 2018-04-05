<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Teiki extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'teiki_account_ledgers';
    protected $guarded    = ['id'];

}
