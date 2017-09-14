<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class BankbookType extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'deposit_bankbook_deed_types';
    protected $guarded    = ['id'];

}
