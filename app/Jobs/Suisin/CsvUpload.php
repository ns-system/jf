<?php

namespace App\Jobs\Suisin;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ImportZenonDataService;

class CsvUpload extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

    protected $processes;
    protected $ym;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ym, $processes) {
        $this->processes = $processes;
        $this->ym        = $ym;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        echo "[start : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;

        $ym        = $this->ym;
        $processes = $this->processes;

        $obj       = new ImportZenonDataService();
        $json      = $obj->getJsonFile(config_path() . '/import_config.json');
        $file_path = $json['csv_folder_path'] . "/monthly/{$ym}";
        if (!file_exists($file_path))
        {
            throw new \Exception("CSVファイル累積先が存在しないようです。（ファイルパス：{$file_path}）");
        }
        echo "  -- check : " . date('Y-m-d H:i:s') . PHP_EOL;

        // pre-check
        $rows = $obj->monthlyStatus($ym, $processes)->get();
        foreach ($rows as $r) {
            echo "  -----> {$r->csv_file_name}" . PHP_EOL;

            $csv_file          = $obj->setCsvFile($file_path . '/' . $r->csv_file_name)->getCsvFile();
            $r->is_pre_process = (int) true;
            $r->save();

            $this->csvCheck($csv_file, $r);

//            $r->is_execute = (int) true;
//            $r->process_started_at = date('Y-m-d H:i:s');
            $r->row_count    = $obj->getMaxRow();
//            $r->is_pre_process = (int) false;
            $r->save();
        }

        echo "  -- upload : " . date('Y-m-d H:i:s') . PHP_EOL;

        // upload-to-db
        \DB::connection('mysql_zenon')->transaction(function() use($rows, $ym, $file_path, $obj) {
            foreach ($rows as $r) {
                $r->is_post_process    = (int) true;
                $r->process_started_at = date('Y-m-d H:i:s');
                $r->save();
                echo "  -----> {$r->csv_file_name}" . PHP_EOL;

                $csv = $obj->setCsvFile($file_path . '/' . $r->csv_file_name)->getCsvFile();
                $obj->uploadToDatabase($r, $csv, $ym);

                // update
//                $cnt = \DB::connection('mysql_zenon')->table($r->table_name)->where('monthly_id', '=', $ym)->count();
//
//                $r->executed_row_count = $cnt;
                $r->is_import        = (int) true;
                $r->process_ended_at = date('Y-m-d H:i:s');
                $r->save();
            }
        });

        echo "  -- consignors : " . date('Y-m-d H:i:s') . PHP_EOL;

        // consignors
        $sql        = "consignor_code," .
                " consignor_name," .
                " COUNT(*) as total_count," .
                " MAX(scheduled_transfer_payment_on) as reference_last_traded_on," .
                " MAX(last_traded_on) as last_traded_on"
        ;
        $consignors = \App\Jifuri::where(['monthly_id' => $ym])
                ->select(\DB::raw($sql))
                ->groupBy('consignor_code')
                ->get()
        ;
        foreach ($consignors as $cns) {
            $keys      = ['consignor_code' => $cns->consignor_code];
            $table     = \App\Consignor::firstOrNew($keys);
            $last_date = $this->getLastTraded($cns);

            $table->consignor_code           = $cns->consignor_code;
            $table->consignor_name           = $cns->consignor_name;
            $table->total_count              = $cns->total_count;
            $table->reference_last_traded_on = $last_date;
            $table->save();
        }

        foreach ($rows as $r) {
            $r->is_process_end = (int) true;
            $r->save();
        }
        echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

    public function failed() {
        
    }

    private function csvCheck($csv_file, $csv_conf) {
//        echo "  ------ called " . date('Y-m-d H:i:s') . PHP_EOL;
        $cnt = 0;
        foreach ($csv_file as $row) {
            if ($row === [null])
            {
                continue;
            }else{$cnt++;}
            if (count($row) != $csv_conf->column_length)
            {

                throw new \Exception('カラム長が一致しませんでした。（' . $csv_conf->csv_file_name . ', ' . count($row) . ' <=>' . $csv_conf->column_length . '）');
            }
        }
//        echo "  ------ ended [{$cnt}] " . date('Y-m-d H:i:s') . PHP_EOL;
    }

}
