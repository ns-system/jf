<?php

namespace App\Models\Deposit;

use Illuminate\Database\Eloquent\Model;

class Gist extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'deposit_gist_codes';
    protected $guarded    = ['id'];

}
