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
        $tmp_1      = explode(" ", $raw_res);
        $unit       = 'M';
        $tmp_2      = array_diff($tmp_1, ['', ' ']);
        $arr_res    = array_values($tmp_2);
        $use_per    = (int) str_replace('%', '', $arr_res[4]);
        $full_size  = (int) str_replace($unit, '', $arr_res[1]);
        $usage_size = (int) str_replace($unit, '', $arr_res[2]);
        $avail_size = (int) str_replace($unit, '', $arr_res[3]);

        $usage = [
            'db_dir'           => /* $arr_res[0] */$this->database_dir,
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
