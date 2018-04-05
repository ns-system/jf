<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class NewBetsudan extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'new_betsudan_account_ledgers';
    protected $guarded    = ['id'];

}
