<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_fund_codes';
    protected $guarded    = ['id'];

}
