<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositCategory extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'deposit_category_codes';
    protected $guarded    = ['id'];
    
}
