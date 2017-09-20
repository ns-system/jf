<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitCopyCsvFileServiceTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFalseWhenInputEmptyDirPath() {
        $service        = new \App\Services\CopyCsvFileService;
        $empty_dir_path = "emptydirpath";
        //$empty_dir_path ="/home/vagrant";
        try {
            $service->inputCheck(2017, $empty_dir_path);
            $this->fail('例外発生なし');
        } catch (Exception $e) {

            $this->assertEquals("累積先ディレクトリが存在しないようです。（想定：{$empty_dir_path}）", $e->getMessage());
        }
    }

    public function testFalseWhenInputWrongMonthlyId() {
        $service          = new \App\Services\CopyCsvFileService;
        $wrong_monthly_id = 201459;
        $empty_dir_path   = "/home/vagrant";
        try {
            $service->inputCheck($wrong_monthly_id, $empty_dir_path);
            $this->fail('例外発生なし');
        } catch (Exception $e) {
            $this->assertEquals("月別IDに誤りがあるようです。（投入された値：{$wrong_monthly_id}）", $e->getMessage());
        }
    }

    public function testTrueCopySuccessful() {
        $monthly_id            = 201708;
        $csv_files_path ="/home/vagrant/cvs/storage/csv_files/*";
        $test_accumulation_dir_path = "/home/vagrant/cvs/storage/test"; 
        $service               = new \App\Services\CopyCsvFileService;
        $temp_file_path        = $test_accumulation_dir_path . "/temp";
        exec("sudo mkdir -p {$temp_file_path}");
        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
        $file_list             = $service->getCsvFileList($temp_file_path);
        $is_exist = TRUE;
        $count =0;
        $service->setMonthlyId($monthly_id)
                ->setDirectoryPath($test_accumulation_dir_path)
                ->accumulationFileCreation();
        foreach ($file_list as $f) {
           exec("sudo rm -rf exile {$f['destination']}");
        }
      $service->setMonthlyId($monthly_id)
                ->setDirectoryPath($test_accumulation_dir_path)
                ->accumulationFileCreation()
                ->copyCsvFile();
      foreach ($file_list as $f) {
         
           if(!(file_exists($test_accumulation_dir_path."/".$f['destination']."/".$f["csv_file_name"]))){
               $is_exist=FALSE; 
           }
       }
        $this->assertTrue($is_exist);
    }
    public function testOutputForJsonFile(){
        
    }
}
