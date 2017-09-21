<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class FundUsage extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_fund_usages';
    protected $guarded    = ['id'];

}
