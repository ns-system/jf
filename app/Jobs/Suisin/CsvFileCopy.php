<?php

namespace App\Jobs\Suisin;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class CsvFileCopy extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

    protected $ym;
    protected $job_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $job_id) {
        //
        $this->ym     = $id;
        $this->job_id = $job_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        echo '==== CsvFileCopy ====' . PHP_EOL;
        echo '[start : ' . date('Y-m-d H:i:s') . ']' . PHP_EOL;
        $monthly_id            = $this->ym;
        $json_file_path        = config_path();
        $copy_csv_file_service = new \App\Services\CopyCsvFileService();
        $json_file             = $copy_csv_file_service->getJsonFile($json_file_path, "/import_config.json");
        $accumulation_dir_path = $json_file["csv_folder_path"];
        $job                   = \App\JobStatus::find($this->job_id);

        try {
            $ignore_and_not_exist_file_lists = $copy_csv_file_service
                    ->setMonthlyId($monthly_id)
                    ->setDirectoryPath($accumulation_dir_path)
                    ->copyCsvFile()
                    ->tableTemplateCreation()
                    ->registrationCsvFileToDatabase()
            ;
            $copy_csv_file_service->tempFileErase();
            $copy_csv_file_service->outputForJsonFile($ignore_and_not_exist_file_lists['ignore'], storage_path() . '/jsonlogs', $this->ym . '_ignore_file_list.json');
            $copy_csv_file_service->outputForJsonFile($ignore_and_not_exist_file_lists['not_exist'], storage_path() . '/jsonlogs', $this->ym . '_not_exist_file_list.json');
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
            $job->is_copy_error = true;
            $job->save();
        } finally {
            echo '[end   : ' . date('Y-m-d H:i:s') . ']' . PHP_EOL;
            $job->is_copy_end = true;
            $job->save();
        }
    }

}
