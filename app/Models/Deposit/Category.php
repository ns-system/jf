<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'deposit_category_codes';
    protected $guarded    = ['id'];
    
}
