<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositGist extends Model
{

    protected $connection = 'mysql_master';
    protected $table      = 'deposit_gist_codes';
    protected $guarded    = ['id'];

}
