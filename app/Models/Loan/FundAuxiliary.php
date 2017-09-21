<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class FundAuxiliary extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_fund_auxiliary_codes';
    protected $guarded    = ['id'];

}
