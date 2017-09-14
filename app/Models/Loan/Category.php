<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'loan_category_codes';
    protected $guarded    = ['id'];

}
