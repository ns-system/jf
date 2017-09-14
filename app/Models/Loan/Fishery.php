<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class Fishery extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_fishery_form_codes';
    protected $guarded    = ['id'];

}
