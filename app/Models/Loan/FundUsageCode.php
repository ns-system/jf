<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class FundUsageCode extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_fund_usage_codes';
    protected $guarded    = ['id'];

}
