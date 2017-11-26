<?php

namespace App\Jobs\Suisin;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ImportZenonDataService;
use \App\Services\Traits\MemoryCheckable;

class CsvUpload extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        MemoryCheckable
    ;

    protected $process_ids;
    protected $ym;
    protected $job_id;

    public function __construct($ym, $process_ids, $job_id) {
        $this->process_ids = $process_ids;
        $this->ym          = $ym;
        $this->job_id      = $job_id;
    }

//    public function failed() {
//        echo "error!";
//        \DB::connection('mysql_zenon')->rollback();
//    }

    public function handle() {
        echo "==== CsvFileUpload ====" . PHP_EOL;
        echo "[start : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;

        $ym          = $this->ym;
        $process_ids = $this->process_ids;

        echo "monthly_id = {$ym}, job_id = {$this->job_id}" . PHP_EOL;

        $import_zenon_data_service = new ImportZenonDataService();
        \DB::connection('mysql_zenon')->beginTransaction();

        // 事前チェック
        try {
            echo "  -- check : " . date('Y-m-d H:i:s') . PHP_EOL;
            // JobStatusの変更
            $import_zenon_data_service->setImportStartToJobStatus($this->job_id);

            // Configファイル読み取り
            $json      = $import_zenon_data_service->setFilePath(config_path(), 'import_config.json')->getJsonFile();
            $file_path = $json['csv_folder_path'] . "/monthly/{$ym}";

            // 事前チェック実施
            $rows = $import_zenon_data_service
                    ->monthlyStatus($ym, $process_ids)
                    ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                    ->orderBy('key_id', 'asc')
                    ->get()
            ;

            foreach ($rows as $r) {
                echo "     --> {$r->csv_file_name}" . PHP_EOL;
                $import_zenon_data_service->setPreStartToMonthlyStatus($r->id);
                $csv_file = $import_zenon_data_service->setCsvFileObject($file_path . '/' . $r->csv_file_name)->checkCsvFileLength($r->column_length);
                $import_zenon_data_service->setPreEndAndRowCountToMonthlyStatus($r->id, $csv_file->getCsvLines());
            }
        } catch (\Exception $e) {
            $import_zenon_data_service->setImportErrorToJobStatus($this->job_id, mb_substr($e->getMessage(), 0, 250));
            echo $e->getMessage();
            \DB::connection('mysql_zenon')->rollback();
            exit();
        }

        // アップロード処理
        try {
            echo "  -- upload : " . date('Y-m-d H:i:s') . PHP_EOL;

            // 全オンデータ反映
            $database_setting_not_exist_list = [];
            foreach ($rows as $r) {
                echo "     --> {$r->csv_file_name}" . PHP_EOL;
//                $this->debugMemory('phase1');
                $import_zenon_data_service->setPostStartToMonthlyStatus($r->id);
                $csv_file_object = $import_zenon_data_service->setCsvFileObject($file_path . '/' . $r->csv_file_name)->getCsvFileObject();

                // データベース反映処理
                $error_array = $import_zenon_data_service->uploadToDatabase($r, $csv_file_object, $ym);

                // エラーメッセージ処理
                if (!empty($error_array))
                {
                    $database_setting_not_exist_list[] = $error_array;
                    $import_zenon_data_service->setPostErrorToMonthlyStatus($r->id, $error_array['reason']);
                }
                else
                {
                    $import_zenon_data_service->setPostEndToMonthlyStatus($r->id);
                }
//                $this->debugMemory('phase2');
            }
            if (!empty($database_setting_not_exist_list))
            {
                $import_zenon_data_service->outputForJsonFile($database_setting_not_exist_list, storage_path() . '/jsonlogs', date('Ymd_His') . '_database_setting_not_exist_files.json');
            }

            // 委託者マスタ創生
            echo "  -- consignors : " . date('Y-m-d H:i:s') . PHP_EOL;
            $sql        = "consignor_code, COUNT(*) as total_count, MAX(scheduled_transfer_payment_on) as reference_last_traded_on, MAX(last_traded_on) as last_traded_on";
            $consignors = \App\Jifuri::where(['monthly_id' => $ym])->select(\DB::raw($sql))->groupBy('consignor_code')->get();
            foreach ($consignors as $cns) {
                $tmp_cns        = \App\Jifuri::where(['consignor_code' => $cns->consignor_code, 'monthly_id' => $ym,])->orderBy('last_traded_on', 'desc')->first();
                $consignor_name = (!empty($tmp_cns)) ? $tmp_cns->consignor_name : '';

                $keys      = ['consignor_code' => $cns->consignor_code];
                $table     = \App\Consignor::firstOrNew($keys);
                $last_date = (empty($cns->reference_last_traded_on) || $cns->reference_last_traded_on === '0000-00-00' || $cns->reference_last_traded_on === '00000000') ?
                        $cns->last_traded_on :
                        $cns->reference_last_traded_on
                ;

                $table->consignor_code           = $cns->consignor_code;
                $table->consignor_name           = $consignor_name;
                $table->total_count              = $cns->total_count;
                $table->reference_last_traded_on = $last_date;
                $table->save();
            }
            $import_zenon_data_service->setImportEndToJobStatus($this->job_id);
        } catch (\Exception $e) {
            // エラー発生時、フラグをリセット
            $import_zenon_data_service->resetJobStatus($rows);
//            echo $e->getMessage();
            echo '[ ' . date('Y-m-d H:i:s') . ' ]' . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
            $import_zenon_data_service->setImportErrorToJobStatus($this->job_id, $e->getMessage());
            \DB::connection('mysql_zenon')->rollback();
            exit();
        }

        \DB::connection('mysql_zenon')->commit();

        echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

}
