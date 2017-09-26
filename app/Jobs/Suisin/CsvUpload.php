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

            $import_zenon_data_service = new ImportZenonDataService();
            $json                      = $import_zenon_data_service->setFilePath(config_path(), 'import_config.json')->getJsonFile();
            $file_path                 = $json['csv_folder_path'] . "/monthly/{$ym}";

            echo "  -- check : " . date('Y-m-d H:i:s') . PHP_EOL;

            // pre-check
            $rows = $import_zenon_data_service->monthlyStatus($ym, $processes)->get();
            foreach ($rows as $r) {
                echo "  -----> {$r->csv_file_name}" . PHP_EOL;
                $import_zenon_data_service->setPreProcessStartToMonthlyStatus($r);

                $csv_file = $import_zenon_data_service->setCsvFileObject($file_path . '/' . $r->csv_file_name)->checkCsvFileLength($r->column_length);

                $import_zenon_data_service->setPreProcessEndToMonthlyStatus($r, $csv_file->getCsvLines());
            }

            echo "  -- upload : " . date('Y-m-d H:i:s') . PHP_EOL;

            // upload-to-db
            \DB::connection('mysql_zenon')->transaction(function() use($rows, $ym, $file_path, $import_zenon_data_service) {
                foreach ($rows as $r) {

                    $import_zenon_data_service->setPostProcessStartToMonthlyStatus($r);
                    echo "  -----> {$r->csv_file_name}" . PHP_EOL;

                    $csv_file_object = $import_zenon_data_service->setCsvFileObject($file_path . '/' . $r->csv_file_name)->getCsvFileObject();
                    $import_zenon_data_service->uploadToDatabase($r, $csv_file_object, $ym);

                    $import_zenon_data_service->setPostProcessEndToMonthlyStatus($r);
                }
            });

            echo "  -- consignors : " . date('Y-m-d H:i:s') . PHP_EOL;

            // consignors
            $sql        = "consignor_code, consignor_name, COUNT(*) as total_count, MAX(scheduled_transfer_payment_on) as reference_last_traded_on, MAX(last_traded_on) as last_traded_on";
            $consignors = \App\Jifuri::where(['monthly_id' => $ym])->select(\DB::raw($sql))->groupBy('consignor_code')->get();
            foreach ($consignors as $cns) {
                $keys      = ['consignor_code' => $cns->consignor_code];
                $table     = \App\Consignor::firstOrNew($keys);
                $last_date = $import_zenon_data_service->getLastTraded($cns->reference_last_traded_on, $cns->last_traded_on);

                $table->consignor_code           = $cns->consignor_code;
                $table->consignor_name           = $cns->consignor_name;
                $table->total_count              = $cns->total_count;
                $table->reference_last_traded_on = $last_date;
                $table->save();
            }
            $job->is_import_end = true;
            $job->save();
        } catch (\Exception $exc) {
            echo $exc->getMessage() . PHP_EOL;
            echo $exc->getTraceAsString();
            $job->is_import_error = true;
            $job->save();
        }

        echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

}
