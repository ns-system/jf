<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZenonCsv extends Model
{

    protected $connection = 'mysql_suisin';
    protected $table      = 'zenon_data_csv_files';
    protected $guarded    = ['id'];

    public function scopeActive($query) {
        return $query->where('is_process', '=', 1);
    }

}
