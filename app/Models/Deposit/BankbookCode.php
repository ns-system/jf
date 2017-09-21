<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class BankbookCode extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'deposit_bankbook_deed_codes';
    protected $guarded    = ['id'];

}
