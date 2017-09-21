<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class Collateral extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_collateral_codes';
    protected $guarded    = ['id'];

}
