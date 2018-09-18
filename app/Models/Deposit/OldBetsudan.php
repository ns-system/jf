<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class OldBetsudan extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'old_betsudan_account_ledgers';
    protected $guarded    = ['id'];

}
