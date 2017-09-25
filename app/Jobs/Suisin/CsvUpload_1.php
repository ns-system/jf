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
    protected $job_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ym, $processes, $job_id) {
        $this->processes = $processes;
        $this->ym        = $ym;
        $this->job_id    = $job_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        echo "==== CsvFileUpload ====" . PHP_EOL;
        echo "[start : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;

        $ym        = $this->ym;
        $processes = $this->processes;
        $job       = \App\JobStatus::find($this->job_id);

        try {
            $job->is_import_start = true;
            $job->save();

            $obj       = new ImportZenonDataService();
            $json      = $obj->getJsonFile(config_path(), 'import_config.json');
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
                $r->is_pre_process_start = true;
                $r->save();

                $csv_file              = $obj->setCsvFileObject($file_path . '/' . $r->csv_file_name)->checkCsvFileLength($r->column_length);
                $r->row_count          = $csv_file->getCsvLines();
                $r->is_pre_process_end = true;
                $r->save();
            }

            echo "  -- upload : " . date('Y-m-d H:i:s') . PHP_EOL;

            // upload-to-db
            \DB::connection('mysql_zenon')->transaction(function() use($rows, $ym, $file_path, $obj) {
                foreach ($rows as $r) {
                    $r->is_post_process_start = true;
                    $r->process_started_at    = date('Y-m-d H:i:s');
                    $r->save();
                    echo "  -----> {$r->csv_file_name}" . PHP_EOL;

                    $csv_file_object = $obj->setCsvFileObject($file_path . '/' . $r->csv_file_name)->getCsvFileObject();
                    $obj->uploadToDatabase($r, $csv_file_object, $ym);

                    $r->is_import           = true;
                    $r->is_post_process_end = true;
//                    $r->is_process_end      = true;
                    $r->process_ended_at    = date('Y-m-d H:i:s');
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
                $last_date = $obj->getLastTraded($cns);

                $table->consignor_code           = $cns->consignor_code;
                $table->consignor_name           = $cns->consignor_name;
                $table->total_count              = $cns->total_count;
                $table->reference_last_traded_on = $last_date;
                $table->save();
            }
            $job->is_import_end = true;
            $job->save();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $job->is_import_error = true;
            $job->save();
        }

//        foreach ($rows as $r) {
//            $r->is_process_end = (int) true;
//            $r->save();
//        }
        echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

    public function failed() {
        
    }

//    private function csvCheck($csv_file, $csv_conf) {
////        echo "  ------ called " . date('Y-m-d H:i:s') . PHP_EOL;
//        $cnt = 0;
//        foreach ($csv_file as $row) {
//            if ($row === [null])
//            {
//                continue;
//            }
//            else
//            {
//                $cnt++;
//            }
//            if (count($row) != $csv_conf->column_length)
//            {
//
//                throw new \Exception('カラム長が一致しませんでした。（' . $csv_conf->csv_file_name . ', ' . count($row) . ' <=>' . $csv_conf->column_length . '）');
//            }
//        }
////        echo "  ------ ended [{$cnt}] " . date('Y-m-d H:i:s') . PHP_EOL;
//    }

}
