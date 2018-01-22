<?php

namespace App\Services;

//use App\Http\Requests;

class DatabaseUsageShowService
{

    protected $database_dir = '';

    public function getMySqlConfig() {
        $res                = \DB::select("show variables where Variable_name = 'datadir'");
        $this->database_dir = $res[0]->Value;
        return $this;
    }

    public function getNowDirectoryUsage() {
        $command    = "df -BM {$this->database_dir}";
        $raw_res    = exec($command);
        $arr_res    = explode(" ", $raw_res);
        $unit       = 'M';
        $use_per    = (int) str_replace('%', '', $arr_res[11]);
        $full_size  = (int) str_replace($unit, '', $arr_res[4]);
        $usage_size = (int) str_replace($unit, '', $arr_res[5]);
        $avail_size = (int) str_replace($unit, '', $arr_res[9]);

        $usage = [
            'db_dir'           => $arr_res[0],
            'db_full_size'     => $full_size,
            'db_usage'         => $usage_size,
            'db_available'     => $avail_size,
            'db_usage_percent' => $use_per,
            'db_avail_percent' => 100 - $use_per,
            'size_unit'        => $unit
        ];
        return $usage;
    }

}
