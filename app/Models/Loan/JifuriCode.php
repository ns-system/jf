<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class JifuriCode extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_jifuri_codes';
    protected $guarded    = ['id'];

}
