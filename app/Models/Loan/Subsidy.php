<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class Subsidy extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_subsidy_codes';
    protected $guarded    = ['id'];

}
