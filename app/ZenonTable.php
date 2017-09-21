<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZenonTable extends Model
{
    protected $connection = 'mysql_suisin';
    protected $table = 'zenon_table_column_configs';
    protected $guarded    = ['id'];
    
}
