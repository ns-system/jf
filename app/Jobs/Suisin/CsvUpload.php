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

    public function failed() {
        // ジョブが失敗した時に呼び出される…
        echo "[failed : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

    public function handle() {
        echo "==== CsvFileUpload ====" . PHP_EOL;
        echo "[start : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;

        $ym          = $this->ym;
        $process_ids = $this->process_ids;

        echo "  monthly_id = {$ym}, job_id = {$this->job_id}" . PHP_EOL;

        $import_zenon_data_service = new ImportZenonDataService();
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
                    ->addSelect(\DB::raw('zenon_data_monthly_process_status.id as id'))
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
            echo '[ ' . date('Y-m-d H:i:s') . ' ]' . PHP_EOL;
            echo $e->getMessage();
            echo $e->getTraceAsString() . PHP_EOL;
            $import_zenon_data_service->setImportErrorToJobStatus($this->job_id, mb_substr($e->getMessage(), 0, 250));
            exit();
        }

        // アップロード処理
        echo "  -- upload : " . date('Y-m-d H:i:s') . PHP_EOL;

        // 全オンデータ反映
        $database_setting_not_exist_list = [];
        foreach ($rows as $r) {
            echo "     --> {$r->csv_file_name}" . PHP_EOL;
            $import_zenon_data_service->setPostStartToMonthlyStatus($r->id);
            $csv_file_object = $import_zenon_data_service->setCsvFileObject($file_path . '/' . $r->csv_file_name)->getCsvFileObject();

            // データベース反映処理
            try {
                $error_array = \DB::connection('mysql_zenon')->transaction(function() use ($import_zenon_data_service, $r, $csv_file_object, $ym) {
                    return $import_zenon_data_service->uploadToDatabase($r, $csv_file_object, $ym);
                });
            } catch (\Exception $e) {
                echo '[ ERROR : ' . date('Y-m-d H:i:s') . ' ]' . PHP_EOL;
                echo $e->getMessage() . PHP_EOL;
                echo $e->getTraceAsString() . PHP_EOL;
                $import_zenon_data_service->setImportErrorToJobStatus($this->job_id, $e->getMessage());
                $r->is_post_process_error = true;
                $r->save();
            }

            // エラーメッセージ処理
            try {
                if (!empty($error_array))
                {
                    $database_setting_not_exist_list[] = $error_array;
                    $import_zenon_data_service->setPostErrorToMonthlyStatus($r->id, $error_array['reason']);
                }
                else
                {
                    $import_zenon_data_service->setPostEndToMonthlyStatus($r->id);
                }
            } catch (\Exception $exc) {
                echo '[ ' . date('Y-m-d H:i:s') . ' ]' . PHP_EOL;
                echo $e->getMessage() . PHP_EOL;
                echo $e->getTraceAsString() . PHP_EOL;
                $import_zenon_data_service->setImportErrorToJobStatus($this->job_id, $e->getMessage());
            }
        }
        if (!empty($database_setting_not_exist_list))
        {
            $import_zenon_data_service->outputForJsonFile($database_setting_not_exist_list, storage_path() . '/jsonlogs', date('Ymd_His') . '_database_setting_not_exist_files.json');
        }
        $import_zenon_data_service->setImportEndToJobStatus($this->job_id);

        echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

}
