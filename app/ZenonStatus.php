<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZenonStatus extends Model
{

    protected $connection = 'mysql_suisin';
    protected $table      = 'zenon_data_process_status';
    protected $guarded    = ['id'];

    public function ZenonCsv() {
        return $this->hasOne('\App\ZenonCsv', 'zenon_data_csv_file_id', 'id');
    }

    public function joinZenonCsv($monthly) {
        $table = \DB::connection('mysql_suisin')
                ->table('zenon_data_process_status')
                ->join('zenon_data_csv_files','zenon_data_csv_files.id','=','zenon_data_process_status.zenon_data_csv_file_id')
                ->where(['zenon_data_csv_files.is_process'=>(int) true, 'zenon_data_process_status.monthly_id'=>$monthly])
//                ->where(['zenon_data_process_status.monthly_id'=>$monthly])
        ;
        return $table;
    }

}
