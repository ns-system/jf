<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitCopyCsvFileServiceTest extends TestCase
{

    use DatabaseTransactions;

    private $directorys = [
        'temp'    => 'temp',
        'monthly' => 'monthly',
        'daily'   => 'daily',
        'ignore'  => 'ignore',
    ];

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

//時間かかるのでコメントアウト
    public function testTrueCopySuccessful() {
        $monthly_id                 = 201708;
        $csv_files_path             = "/home/vagrant/cvs/storage/csv_files/*";
        $test_accumulation_dir_path = "/home/vagrant/cvs/storage/test";
        $service                    = new \App\Services\CopyCsvFileService;
        $temp_file_path             = $test_accumulation_dir_path . "/temp";
        exec("sudo mkdir -p {$temp_file_path}");
        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
        $file_list                  = $service->getCsvFileList($temp_file_path);
        $is_exist                   = TRUE;
        $count                      = 0;
        $service->setMonthlyId($monthly_id)
                ->setDirectoryPath($test_accumulation_dir_path);
        foreach ($file_list as $f) {
            exec("sudo rm -rf exile {$f['destination']}");
        }
        $service->setMonthlyId($monthly_id)
                ->setDirectoryPath($test_accumulation_dir_path)
                ->copyCsvFile();
        foreach ($file_list as $f) {

            if (!(file_exists($test_accumulation_dir_path . "/" . $f['destination'] . "/" . $f["csv_file_name"])))
            {
                $is_exist = FALSE;
            }
        }
        $this->assertTrue($is_exist);
    }

    public function testTrueTempFileErase() {
        $test_accumulation_dir_path = "/home/vagrant/cvs/storage/test";
        $service                    = new \App\Services\CopyCsvFileService;
        $csv_files_path             = "/home/vagrant/cvs/storage/csv_files/temp";
        exec("sudo mkdir -p {$test_accumulation_dir_path}/temp/}");
        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
        $check_target               = glob($test_accumulation_dir_path . "/temp/*");
        if (empty($check_target))
        {
            $this->fail('削除予定のファイルが存在しない');
        }
        $service->setDirectoryPath($test_accumulation_dir_path)
                ->tempFileErase();
        $target = glob($test_accumulation_dir_path . "/temp/*");

        $this->assertTrue(empty($target));
        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
    }

//    DB関係はロールバックできてない
    public function testTableTemplateCreation() {
        $monthly_id       = 201707;
        $service          = new \App\Services\CopyCsvFileService;
        \App\ZenonMonthlyStatus::month($monthly_id)->truncate();
        $based_file_count = \App\ZenonCsv::count();
         $based_file_list = \App\ZenonCsv::get();
        $check_row        = \App\ZenonMonthlyStatus::month($monthly_id)->count();
        
        if (!empty($check_row))
        {
            $this->fail('DBがリセットされていない');
        }
        $service->setMonthlyId($monthly_id)
                ->tableTemplateCreation();
        $row = \App\ZenonMonthlyStatus::month($monthly_id)->get();
        $row_count = \App\ZenonMonthlyStatus::month($monthly_id)->count();
        $this->assertEquals($row_count, $based_file_count);
        foreach ($based_file_list as $based_file){
            $is_exsit =0;
            foreach ($row as $r){
                if($r->	zenon_data_csv_file_id==$based_file->id){
                    $is_exsit=1;
                }
            }
            if($is_exsit ==0){
                $this->fail('monthly_process_statusにzenon_data_csv_file_id'.$file."がデータベースに登録されていませんでした");
            }
        }
    }

    public function testRegistrationCsvFileToDatabase() {
        
         
           $monthly_id                 = "201707";
        $test_accumulation_dir_path = "/home/vagrant/cvs/storage/test";
        $csv_file_record_to_db      = \App\ZenonCsv::get();
        $temp_file_path             = $test_accumulation_dir_path . "/temp";
         $csv_files_path             = "/home/vagrant/cvs/storage/csv_files/*";
        exec("sudo mkdir -p {$temp_file_path}");
        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
        $service                    = new \App\Services\CopyCsvFileService;
        \App\ZenonMonthlyStatus::month($monthly_id)->truncate();
        $check_row_count            = \App\ZenonMonthlyStatus::month($monthly_id)->count();
        if (!empty($check_row_count))
        {
            $this->fail('DBがリセットされていない');
        }
        $service->setMonthlyId($monthly_id)
                ->setDirectoryPath($test_accumulation_dir_path)
                ->copyCsvFile()
                ->tableTemplateCreation()
                ->registrationCsvFileToDatabase();

        $row            = \App\ZenonMonthlyStatus::month($monthly_id)->where("is_exist", "=", "1")->get();
        $tmp_file_lists = $service->getCsvFileList($test_accumulation_dir_path . "/temp");
        foreach ($tmp_file_lists as $l) {

            if ($l['cycle'] == 'M' && date('Ym', strtotime($l['csv_file_set_on'] . ' -1 month')) == $monthly_id)
            {
                foreach ($csv_file_record_to_db as $csv_lile) {
                    if ($l['identifier'] == $csv_lile->identifier)
                    {
                        $copy_source_files[$l['identifier']] = $l;
                    }
                }
            }
        }


        foreach ($copy_source_files as $file) {
            $is_exist = 0;
            foreach ($row as $r) {
                if ($file["csv_file_name"] == $r->csv_file_name)
                {
                    $is_exist = 1;
                }
            }
             if($is_exist==0){
            $this->fail('DBに登録された内容と実際のファイルが異なります'.$file."がデータベースに登録されていませんでした");
        }
          
        }
       

        
 
    }



    public function testSetDirectoryPath() {
        $not_exists_dir_path       = "/not_exists_dir";
        $not_exists_storing_target = "/home/vagrant/cvs/storage/test/dir";
        $service                   = new \App\Services\CopyCsvFileService;
        exec("sudo mkdir -p {$not_exists_storing_target}/temp");
        foreach ($this->directorys as $d) {
            exec("sudo mkdir -p {$not_exists_storing_target}/{$d}");
        }
        try {
            $service->setDirectoryPath($not_exists_dir_path);
            $this->fail('例外発生なし');
        } catch (Exception $e) {

            $this->assertEquals("存在しないファイルパスが指定されました。（マウント先：{$not_exists_dir_path}）", $e->getMessage());
        }
        foreach ($this->directorys as $dr) {
            if ($dr != "temp")
            {

                exec("sudo rm -rf {$not_exists_storing_target}/{$dr}");
                try {
                    $service->setDirectoryPath($not_exists_storing_target);
                    $this->fail('例外発生なし');
                } catch (Exception $e) {

                    $this->assertEquals("格納先ファイルパスが存在しません。（格納先ファイル：{$not_exists_storing_target}/{$dr}）", $e->getMessage());
                }
                exec("sudo mkdir -p {$not_exists_storing_target}/{$dr}");
            }
            exec("sudo mkdir -p {$not_exists_storing_target}/{$dr}");
        }
        exec("sudo rm -rf {$not_exists_storing_target}");
    }

    public function testGetCsvFileList() {
        $not_exists_dir_path        = "/not_exists_dir";
        $service                    = new \App\Services\CopyCsvFileService;
        $csv_files_path             = "/home/vagrant/cvs/storage/csv_files/*";
        $test_accumulation_dir_path = "/home/vagrant/cvs/storage/test";
        $temp_file_path             = $test_accumulation_dir_path . "/temp";
        $source_file_list           = scandir($temp_file_path);
        exec("sudo mkdir -p {$temp_file_path}");
        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
        try {
            $service->setDirectoryPath($test_accumulation_dir_path)
                    ->getCsvFileList($not_exists_dir_path);
            $this->fail('例外発生なし');
        } catch (Exception $ex) {
            $this->assertEquals("存在しないファイルパスが指定されました。（指定先：{$not_exists_dir_path}）", $ex->getMessage());
        }
        $target_list = $service->setDirectoryPath($test_accumulation_dir_path)
                ->getCsvFileList($temp_file_path);
        foreach ($source_file_list as $source_file) {
            $f = pathinfo($source_file);
            $is_matched =0;
            $source_file_size = round(filesize($temp_file_path."/".$source_file) / 1024);
            
            if (!empty($f['extension']) && $f['extension'] == 'csv')
            {
                foreach ($target_list as $target) {
                    if($target["csv_file_name"]==$source_file&&$source_file_size==$target["kb_size"]){
                        $is_matched =1;
                        
                    }
                }
            }
        }
        if($is_matched==0){
            $this->fail('ファイルリストに不備があります'.$source_file."がリスト内に存在しないかファイルサイズが異なります");
        }
        exec("sudo rm -rf {$temp_file_path}");
    }

}
