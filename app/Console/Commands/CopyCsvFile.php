<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CopyCsvFile extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copycsvfile {monthly_id : 月別IDを指定}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy CSV file from USB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $monthly_id                = $this->argument("monthly_id");
        $json_file_path            = config_path();
        $copy_csv_file_service     = new \App\Services\CopyCsvFileService();
        $import_zenon_data_service = new \App\Services\ImportZenonDataService();
        $json_file                 = $import_zenon_data_service->getJsonFile($json_file_path . "/import_config.json");
        $accumulation_dir_path     = $json_file["csv_folder_path"];
//        var_dump($accumulation_dir_path);
//        //引数がおかしくないかの確認
//        if (mb_strlen($monthly_id) !== 6 || substr($monthly_id, -2) <= 0 || substr($monthly_id, -2) > 12)
//        {
//            //おかしかったらエラー処理
//            echo 'ccc';
//        }
        if (!file_exists($accumulation_dir_path))
        {
            //おかしかったらエラー処理
            throw new Exception("累積先ディレクトリが存在しないようです。（想定：{$accumulation_dir_path}）");
        }
        if (!strptime($monthly_id, '%Y%m'))
        {
            //おかしかったらエラー処理
            throw new Exception("月別IDに誤りがあるようです。（投入された値：{$monthly_id}）");
        }


//        $last_day  = $copy_csv_file_service->accumulationFileCreation($monthly_id, $accumulation_dir_path);
//        $usb_path = "/mnt/win/csvfiles";
        $copy_csv_file_service->setMonthlyId($monthly_id)
                ->setDirectoryPath($accumulation_dir_path)
                ->accumulationFileCreation()
                ->copyCsvFile()
                ->tableTemplateCreation()
                ->registrationCsvFileToDatabase()
        ;
//        exit();
////        $copy_csv_file_service->copyCsvFile($monthly_id, $usb_path, $accumulation_dir_path);
//
//        $file_list = $copy_csv_file_service->getCsvFileList($usb_path);
//        $copy_csv_file_service->tableTemplateCreation($monthly_id);
//        $copy_csv_file_service->registrationCsvFileToDb($file_list, $monthly_id);
    }

}
