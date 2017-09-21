<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class SubsidyInstitution extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_subsidy_institution_codes';
    protected $guarded    = ['id'];

}
