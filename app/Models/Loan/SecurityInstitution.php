<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class SecurityInstitution extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_security_institution_codes';
    protected $guarded    = ['id'];

}
