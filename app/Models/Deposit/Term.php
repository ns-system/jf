<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $connection = 'mysql_master';
    protected $table      = 'deposit_term_codes';
    protected $guarded    = ['id'];
}
