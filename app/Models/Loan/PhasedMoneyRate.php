<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class PhasedMoneyRate extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_phased_money_rate_codes';
    protected $guarded    = ['id'];

}
