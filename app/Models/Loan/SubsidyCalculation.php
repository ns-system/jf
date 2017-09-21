<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class SubsidyCalculation extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_subsidy_calculation_codes';
    protected $guarded    = ['id'];

}
