<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZenonType extends Model
{
    protected $connection = 'mysql_suisin';
    protected $table = 'zenon_data_types';
    protected $guarded    = ['id'];
    
}
