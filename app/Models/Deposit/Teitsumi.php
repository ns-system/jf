<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Teitsumi extends Model
{

    protected $connection = 'mysql_zenon';
    protected $table      = 'teitsumi_account_ledgers';
    protected $guarded    = ['id'];

}
