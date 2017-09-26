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
            if (!file_exists($accumulation_dir_path))
            {
                //おかしかったらエラー処理
                throw new \Exception("累積先ディレクトリが存在しないようです。（想定：{$accumulation_dir_path}）");
            }
            if (!strptime($monthly_id, '%Y%m'))
            {
                //おかしかったらエラー処理
                throw new \Exception("月別IDに誤りがあるようです。（投入された値：{$monthly_id}）");
            }

            $copy_csv_file_service->setMonthlyId($monthly_id)
                    ->setDirectoryPath($accumulation_dir_path)
                    ->copyCsvFile()
                    ->tableTemplateCreation()
                    ->registrationCsvFileToDatabase()
//                ->tempFileErase()
            ;
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
