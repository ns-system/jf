<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositBankbookType extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'deposit_bankbook_deed_types';
    protected $guarded    = ['id'];

}
