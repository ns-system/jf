<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositBankbookCode extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'deposit_bankbook_deed_codes';
    protected $guarded    = ['id'];

}
