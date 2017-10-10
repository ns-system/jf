<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitCopyCsvFileServiceTest extends TestCase
{

//    use DatabaseTransactions;

    protected $directorys         = [
        'temp'    => 'temp',
        'monthly' => 'monthly',
        'daily'   => 'daily',
        'ignore'  => 'ignore',
    ];
    protected $dummy_file_name    = [
        'S_D_302_D0255_20170801.csv',
//        'S_D_302_D0256_20170801.csv',
//        'S_D_302_D0257_20170801.csv',
//        'S_D_302_D0258_20170801.csv',
//        'S_D_302_D0259_20170801.csv',
        'K_D_902_M0332_20170801.csv',
//        'K_D_902_M0333_20170801.csv',
        'K_D_902_M9999_20170801.csv',
        'S_D_301_D0263_20170802',
    ];
    protected $dummy_csv_template = [
        [
//            "id"                    => null,
            "identifier"            => "M9999",
            "zenon_data_type_id"    => 9999,
            "zenon_data_name"       => "テストデータ1",
            "reference_return_date" => "月初翌営業日",
            "cycle"                 => "M",
            "database_name"         => "zenon_data_db",
            "table_name"            => "test_table_1",
//            "first_column_position" => 0,
//            "last_column_position"  => 189,
//            "column_length"         => 190,
//            "is_cumulative"         => 1,
//            "is_account_convert"    => 0,
//            "is_process"            => 1,
//            "is_split"              => 0,
//            "zenon_format_id"       => 1,
        ],
        [
            "identifier"            => "M0332",
            "zenon_data_type_id"    => 332,
            "zenon_data_name"       => "テストデータ2",
            "reference_return_date" => "月初翌営業日",
            "cycle"                 => "M",
            "database_name"         => "zenon_data_db",
            "table_name"            => "test_table_2",
        ],
    ];
    protected static $init        = false;

    public function setUp() {
        parent::setUp();

        if (!static::$init)
        {
            static::$init = true;
            \Artisan::call('db:reset', ['--dbenv' => 'testing', '--hide' => 'true']);
            \Artisan::call('db:create', ['--dbenv' => 'testing', '--hide' => 'true']);
            \Artisan::call('migrate');
            \App\ZenonCsv::insert($this->dummy_csv_template);
        }

        $base_path = storage_path() . '/tests/csv_files';
        if (file_exists($base_path))
        {
            system("rm -rf {$base_path}");
        }

        if (!file_exists($base_path))
        {
            mkdir($base_path, 0777);
        }
        foreach ($this->directorys as $d) {
            $path = $base_path . "/{$d}";
            if (!file_exists($path))
            {
                mkdir($path, 0777);
            }
        }
        foreach ($this->dummy_file_name as $name) {
            $file_path = $base_path . '/' . $this->directorys['temp'] . '/' . $name;
            touch($file_path);
        }
    }

    private function getSimpleFileList(array $file_list): array {
        $array = [];
        foreach ($file_list as $f) {
            if (isset($f['csv_file_name']))
            {
                $array[] = $f['csv_file_name'];
            }
        }
        return $array;
    }

    private function removeFileCreateTimeFromList(array $array) {
        $result = [];
        foreach ($array as $t) {
            if (!is_array($t))
            {
                continue;
            }
            $row = [];
            foreach ($t as $key => $value) {
                if ($key != 'file_create_time')
                {
                    $row[$key] = $value;
                }
            }
            $result[] = $row;
        }
        return $result;
    }

    public function tearDown() {
//        parent::tearDown();
        $base_path = storage_path() . '/tests/csv_files';

        if (file_exists($base_path))
        {
            system("rm -rf {$base_path}");
        }
    }

    /**
     * @test
     */
    public function 正常系_ファイルリストが正常取得できる() {
//        dd();
        $service   = new \App\Services\CopyCsvFileService;
        $base_path = storage_path() . '/tests/csv_files';

        $file_path_1 = $base_path . '/' . $this->directorys['temp'] . '/S_D_302_D0255_20179999.csv';
        $file_path_2 = $base_path . '/' . $this->directorys['temp'] . '/S_D_302_T0255_20170801.csv';
        touch($file_path_1);
        touch($file_path_2);
        $tmp_1       = $service->getCsvFileList($base_path . '/temp', $base_path);
        $result_1    = $this->getSimpleFileList($tmp_1);

        $expect_1 = [
//            ["destination" => "/home/vagrant/cvs/storage/tests/csv_files/daily/201708/01", "csv_file_name" => "S_D_302_D0255_20170801.csv", "monthly_id" => "201708", "cycle" => "D", "csv_file_set_on" => "2017-08-01", "identifier" => "D0255", "kb_size" => 0.0,],
//            ["destination" => "/home/vagrant/cvs/storage/tests/csv_files/daily/201708/01", "csv_file_name" => "S_D_302_D0256_20170801.csv", "monthly_id" => "201708", "cycle" => "D", "csv_file_set_on" => "2017-08-01", "identifier" => "D0256", "kb_size" => 0.0,],
//            ["destination" => "/home/vagrant/cvs/storage/tests/csv_files/daily/201708/01", "csv_file_name" => "S_D_302_D0257_20170801.csv", "monthly_id" => "201708", "cycle" => "D", "csv_file_set_on" => "2017-08-01", "identifier" => "D0257", "kb_size" => 0.0,],
//            ["destination" => "/home/vagrant/cvs/storage/tests/csv_files/daily/201708/01", "csv_file_name" => "S_D_302_D0258_20170801.csv", "monthly_id" => "201708", "cycle" => "D", "csv_file_set_on" => "2017-08-01", "identifier" => "D0258", "kb_size" => 0.0,],
//            ["destination" => "/home/vagrant/cvs/storage/tests/csv_files/daily/201708/01", "csv_file_name" => "S_D_302_D0259_20170801.csv", "monthly_id" => "201708", "cycle" => "D", "csv_file_set_on" => "2017-08-01", "identifier" => "D0259", "kb_size" => 0.0,],
//            ["destination" => "/home/vagrant/cvs/storage/tests/csv_files/monthly/201707", "csv_file_name" => "K_D_902_M0332_20170801.csv", "monthly_id" => "201708", "cycle" => "M", "csv_file_set_on" => "2017-08-01", "identifier" => "M0332", "kb_size" => 0.0,],
//            ["destination" => "/home/vagrant/cvs/storage/tests/csv_files/monthly/201707", "csv_file_name" => "K_D_902_M0333_20170801.csv", "monthly_id" => "201708", "cycle" => "M", "csv_file_set_on" => "2017-08-01", "identifier" => "M0333", "kb_size" => 0.0,],
//            ["destination" => "/home/vagrant/cvs/storage/tests/csv_files/monthly/201707", "csv_file_name" => "K_D_902_M9999_20170801.csv", "monthly_id" => "201708", "cycle" => "M", "csv_file_set_on" => "2017-08-01", "identifier" => "M9999", "kb_size" => 0.0,],
//            ["destination" => "/home/vagrant/cvs/storage/tests/csv_files/ignore/201708", "csv_file_name" => "S_D_302_T0255_20170801.csv", "monthly_id" => "201708", "cycle" => "T", "csv_file_set_on" => "2017-08-01", "identifier" => "T0255", "kb_size" => 0.0,],
            "S_D_302_D0255_20170801.csv",
            "K_D_902_M0332_20170801.csv",
            "K_D_902_M9999_20170801.csv",
            "S_D_302_T0255_20170801.csv",
        ];

        $this->assertEquals($result_1, $expect_1);
    }

    /**
     * @test
     */
    public function 異常系_ファイルリストパスが存在しない() {
        $service    = new \App\Services\CopyCsvFileService;
        $wrong_path = '/not_exist_path';
        try {
            $service->getCsvFileList($wrong_path);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("ファイルパスが存在しません。（ファイルパス：{$wrong_path}）", $e->getMessage());
        }
    }

//    public function testFalseWhenInputEmptyDirPath() {
    /**
     * @test
     */
    public function 異常系_ディレクトリパスが存在しない() {
        $service        = new \App\Services\CopyCsvFileService;
        $empty_dir_path = "emptydirpath";
        //$empty_dir_path ="/home/vagrant";
        try {
//            $service->inputCheck(2017, $empty_dir_path);
            $service->setDirectoryPath($empty_dir_path);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("存在しないファイルパスが指定されました。（マウント先：{$empty_dir_path}）", $e->getMessage());
        }
        $empty_dir_path = storage_path() . '/tests/csv_files/ignore';
        try {
//            $service->inputCheck(2017, $empty_dir_path);
            $service->setDirectoryPath($empty_dir_path);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("格納先ファイルパスが存在しません。（格納先ファイル：{$empty_dir_path}/temp）", $e->getMessage());
        }
    }

//    public function testFalseWhenInputWrongMonthlyId() {
    /**
     * @tests
     */
    public function 異常系_月別IDが正しくない() {
        $service          = new \App\Services\CopyCsvFileService;
        $wrong_monthly_id = 201459;
//        $base_path        = storage_path() . '/tests/csv_files';
//        $empty_dir_path   = "/home/vagrant";
        try {
//            $service->inputCheck($wrong_monthly_id, $base_path);
            $service->setMonthlyId($wrong_monthly_id);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("月別IDの指定が誤っているようです。（値：{$wrong_monthly_id}）", $e->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系_一時ディレクトリから累積ディレクトリにコピーできる() {
        $monthly_id = 201707;
//        $csv_files_path             = "/home/vagrant/cvs/storage/csv_files/*";
//        $test_accumulation_dir_path = "/home/vagrant/cvs/storage/test";
        $base_path  = storage_path() . '/tests/csv_files';
        $service    = new \App\Services\CopyCsvFileService;
//        $temp_file_path = $base_path . '/' . $this->directorys['temp'];
//        exec("sudo mkdir -p {$temp_file_path}");
//        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
//        $file_list      = $service->getCsvFileList($temp_file_path);
//        $is_exist       = TRUE;
//        $count          = 0;
//        $service->setMonthlyId($monthly_id)->setDirectoryPath($base_path);
//        foreach ($file_list as $f) {
//            exec("sudo rm -rf exile {$f['destination']}");
//        }
        $expect_1   = [
            'K_D_902_M0332_20170801.csv',
//            'K_D_902_M0333_20170801.csv',
            'K_D_902_M9999_20170801.csv',
        ];
        $expect_2   = [
            'S_D_302_D0255_20170801.csv',
//            'S_D_302_D0256_20170801.csv',
//            'S_D_302_D0257_20170801.csv',
//            'S_D_302_D0258_20170801.csv',
//            'S_D_302_D0259_20170801.csv',
        ];
        $service->setMonthlyId($monthly_id)
                ->setDirectoryPath($base_path)
                ->copyCsvFile()
        ;
//        dd();
        $tmp_1      = $service->getCsvFileList($base_path . '/' . $this->directorys['monthly'] . '/' . $monthly_id);
        $result_1   = $this->getSimpleFileList($tmp_1);
        $this->assertEquals($result_1, $expect_1);

        $tmp_2    = $service->getCsvFileList($base_path . '/' . $this->directorys['daily'] . '/' . '201708/01');
        $result_2 = $this->getSimpleFileList($tmp_2);
        $this->assertEquals($result_2, $expect_2);

//        foreach ($file_list as $f) {
//
//            if (!(file_exists($test_accumulation_dir_path . "/" . $f['destination'] . "/" . $f["csv_file_name"])))
//            {
//                $is_exist = FALSE;
//            }
//        }
//        $this->assertTrue($is_exist);
    }

//    public function testTrueTempFileErase() {
    /**
     * @tests
     */
    public function 正常系_ディレクトリの削除ができる() {
//        $test_accumulation_dir_path = "/home/vagrant/cvs/storage/test";
        $service   = new \App\Services\CopyCsvFileService;
//        $csv_files_path             = "/home/vagrant/cvs/storage/csv_files/temp";
//        exec("sudo mkdir -p {$test_accumulation_dir_path}/temp/}");
//        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
//        $check_target               = glob($test_accumulation_dir_path . "/temp/*");
//        if (empty($check_target))
//        {
//            $this->fail('削除予定のファイルが存在しない');
//        }
        $base_path = storage_path() . '/tests/csv_files/';
        $temp_path = $base_path . $this->directorys['temp'];
//        dd($temp_path);
        $result_1  = $this->getSimpleFileList($service->getCsvFileList($temp_path));
        $expect_1  = [
            "S_D_302_D0255_20170801.csv",
//            "S_D_302_D0256_20170801.csv",
//            "S_D_302_D0257_20170801.csv",
//            "S_D_302_D0258_20170801.csv",
//            "S_D_302_D0259_20170801.csv",
            "K_D_902_M0332_20170801.csv",
//            "K_D_902_M0333_20170801.csv",
            'K_D_902_M9999_20170801.csv',
        ];

        $service->setDirectoryPath($base_path)->tempFileErase();
        $result_2 = $service->getCsvFileList($temp_path);
        $this->assertEquals($result_1, $expect_1);
        $this->assertEquals($result_2, []);

//        $target    = glob($test_accumulation_dir_path . "/temp/*");
//        $this->assertTrue(empty($target));
//        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
    }

//    public function testTableTemplateCreation() {
//    DB関係はロールバックできてない
    /**
     * @tests
     */
    public function 正常系_ステータステーブル用のテンプレートが作成できる() {

        try {
            \DB::connection('mysql_suisin')->beginTransaction();
            \App\ZenonCsv::insert($this->dummy_csv_template);
            $id        = \App\ZenonCsv::where('zenon_data_type_id', '=', 9999)->first()->id;
            $templates = \App\ZenonCsv::where('zenon_data_type_id', '=', 9999)->get();
            $service   = new \App\Services\CopyCsvFileService();
            $service->setMonthlyId(201707)->tableTemplateCreation($templates);
            $result_1  = \App\ZenonMonthlyStatus::where('zenon_data_csv_file_id', '=', $id)->get();
            $this->assertFalse($result_1->isEmpty());
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
            $this->fail('予期しないエラーです。');
        } finally {
            \DB::connection('mysql_suisin')->rollback();
        }
//        $monthly_id       = 201707;
//        $service          = new \App\Services\CopyCsvFileService;
//        \App\ZenonMonthlyStatus::month($monthly_id)->truncate();
//        $based_file_count = \App\ZenonCsv::count();
//        $based_file_list  = \App\ZenonCsv::get();
//        $check_row        = \App\ZenonMonthlyStatus::month($monthly_id)->count();
//
//        if (!empty($check_row))
//        {
//            $this->fail('DBがリセットされていない');
//        }
//        $service->setMonthlyId($monthly_id)
//                ->tableTemplateCreation();
//        $row       = \App\ZenonMonthlyStatus::month($monthly_id)->get();
//        $row_count = \App\ZenonMonthlyStatus::month($monthly_id)->count();
//        $this->assertEquals($row_count, $based_file_count);
//        foreach ($based_file_list as $based_file) {
//            $is_exsit = 0;
//            foreach ($row as $r) {
//                if ($r->zenon_data_csv_file_id == $based_file->id)
//                {
//                    $is_exsit = 1;
//                }
//            }
//            if ($is_exsit == 0)
//            {
//                $this->fail('monthly_process_statusにzenon_data_csv_file_id' . $file . "がデータベースに登録されていませんでした");
//            }
//        }
    }

//    public function testRegistrationCsvFileToDatabase() {
    /**
     * @tests
     */
    public function 正常系_CSVファイルがデータベースに登録できる() {

        try {
//            \DB::connection('mysql_suisin')->beginTransaction();
            // preparation
//            \App\ZenonCsv::insert($this->dummy_csv_template);
            $id          = \App\ZenonCsv::where('zenon_data_type_id', '=', 9999)->first()->id;
            $templates   = \App\ZenonCsv::where('zenon_data_type_id', '=', 9999)->get();
            $service     = new \App\Services\CopyCsvFileService();
            $base_path   = storage_path() . '/tests/csv_files';
            $file_path_1 = $base_path . '/' . $this->directorys['temp'] . '/K_D_902_M8888_20170801.csv';
            $file_path_2 = $base_path . '/' . $this->directorys['temp'] . '/K_D_902_M7777_20170801.csv';
            $file_path_3 = $base_path . '/' . $this->directorys['temp'] . '/K_D_902_M7777_20179999.csv';
            touch($file_path_1);
            touch($file_path_2);
            touch($file_path_3);

            // test
            $service->setMonthlyId(201707)
                    ->setDirectoryPath($base_path)
                    ->tableTemplateCreation(/* $templates */)
            ;

            $lists              = $service->copyCsvFile()->registrationCsvFileToDatabase();
            $ignore_list        = $this->removeFileCreateTimeFromList($lists['ignore']);
            $not_exist_list     = $this->removeFileCreateTimeFromList($lists['not_exist']);
//            $result_1           = \App\ZenonMonthlyStatus::where('zenon_data_csv_file_id', '=', $id)->first()->toArray();
//            $expect_1           = ["K_D_902_M9999_20170801.csv",];
            $expect_ignore_1    = ["S_D_302_D0255_20170801.csv",];
            $expect_not_exist_1 = ["K_D_902_M7777_20170801.csv", "K_D_902_M8888_20170801.csv"];
        } catch (\Exception $exc) {
            echo $exc->getMessage();
//            echo $exc->getTraceAsString();
            $this->fail('予期しないエラーです。');
        } finally {
//            \DB::connection('mysql_suisin')->rollback();
        }
//        var_dump($expect_not_exist_1);
//        dd($not_exist_list);
//        $this->assertEquals($expect_1, $this->getSimpleFileList($result_1));
        $this->assertEquals($expect_ignore_1, $this->getSimpleFileList($ignore_list));
        $this->assertEquals($expect_not_exist_1, $this->getSimpleFileList($not_exist_list));
//        $monthly_id                 = "201707";
//        $test_accumulation_dir_path = "/home/vagrant/cvs/storage/test";
//        $csv_file_record_to_db      = \App\ZenonCsv::get();
//        $temp_file_path             = $test_accumulation_dir_path . "/temp";
//        $csv_files_path             = "/home/vagrant/cvs/storage/csv_files/*";
//        exec("sudo mkdir -p {$temp_file_path}");
//        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
//        $service                    = new \App\Services\CopyCsvFileService;
//        \App\ZenonMonthlyStatus::month($monthly_id)->truncate();
//        $check_row_count            = \App\ZenonMonthlyStatus::month($monthly_id)->count();
//        if (!empty($check_row_count))
//        {
//            $this->fail('DBがリセットされていない');
//        }
//        $service->setMonthlyId($monthly_id)
//                ->setDirectoryPath($test_accumulation_dir_path)
//                ->copyCsvFile()
//                ->tableTemplateCreation()
//                ->registrationCsvFileToDatabase();
//
//        $row            = \App\ZenonMonthlyStatus::month($monthly_id)->where("is_exist", "=", "1")->get();
//        $tmp_file_lists = $service->getCsvFileList($test_accumulation_dir_path . "/temp");
//        foreach ($tmp_file_lists as $l) {
//
//            if ($l['cycle'] == 'M' && date('Ym', strtotime($l['csv_file_set_on'] . ' -1 month')) == $monthly_id)
//            {
//                foreach ($csv_file_record_to_db as $csv_lile) {
//                    if ($l['identifier'] == $csv_lile->identifier)
//                    {
//                        $copy_source_files[$l['identifier']] = $l;
//                    }
//                }
//            }
//        }
//
//
//        foreach ($copy_source_files as $file) {
//            $is_exist = 0;
//            foreach ($row as $r) {
//                if ($file["csv_file_name"] == $r->csv_file_name)
//                {
//                    $is_exist = 1;
//                }
//            }
//            if ($is_exist == 0)
//            {
//                $this->fail('DBに登録された内容と実際のファイルが異なります' . $file . "がデータベースに登録されていませんでした");
//            }
//        }
    }

//    public function testSetDirectoryPath() {
//        $not_exists_dir_path       = "/not_exists_dir";
//        $not_exists_storing_target = "/home/vagrant/cvs/storage/test/dir";
//        $service                   = new \App\Services\CopyCsvFileService;
//        exec("sudo mkdir -p {$not_exists_storing_target}/temp");
//        foreach ($this->directorys as $d) {
//            exec("sudo mkdir -p {$not_exists_storing_target}/{$d}");
//        }
//        try {
//            $service->setDirectoryPath($not_exists_dir_path);
//            $this->fail('例外発生なし');
//        } catch (Exception $e) {
//
//            $this->assertEquals("存在しないファイルパスが指定されました。（マウント先：{$not_exists_dir_path}）", $e->getMessage());
//        }
//        foreach ($this->directorys as $dr) {
//            if ($dr != "temp")
//            {
//
//                exec("sudo rm -rf {$not_exists_storing_target}/{$dr}");
//                try {
//                    $service->setDirectoryPath($not_exists_storing_target);
//                    $this->fail('例外発生なし');
//                } catch (Exception $e) {
//
//                    $this->assertEquals("格納先ファイルパスが存在しません。（格納先ファイル：{$not_exists_storing_target}/{$dr}）", $e->getMessage());
//                }
//                exec("sudo mkdir -p {$not_exists_storing_target}/{$dr}");
//            }
//            exec("sudo mkdir -p {$not_exists_storing_target}/{$dr}");
//        }
//        exec("sudo rm -rf {$not_exists_storing_target}");
//    }
//
//    public function testGetCsvFileList() {
//        $not_exists_dir_path        = "/not_exists_dir";
//        $service                    = new \App\Services\CopyCsvFileService;
//        $csv_files_path             = "/home/vagrant/cvs/storage/csv_files/*";
//        $test_accumulation_dir_path = "/home/vagrant/cvs/storage/test";
//        $temp_file_path             = $test_accumulation_dir_path . "/temp";
//        $source_file_list           = scandir($temp_file_path);
//        exec("sudo mkdir -p {$temp_file_path}");
//        exec("sudo cp -rf  {$csv_files_path} {$test_accumulation_dir_path}");
//        try {
//            $service->setDirectoryPath($test_accumulation_dir_path)
//                    ->getCsvFileList($not_exists_dir_path);
//            $this->fail('例外発生なし');
//        } catch (Exception $ex) {
//            $this->assertEquals("存在しないファイルパスが指定されました。（指定先：{$not_exists_dir_path}）", $ex->getMessage());
//        }
//        $target_list = $service->setDirectoryPath($test_accumulation_dir_path)
//                ->getCsvFileList($temp_file_path);
//        foreach ($source_file_list as $source_file) {
//            $f                = pathinfo($source_file);
//            $is_matched       = 0;
//            $source_file_size = round(filesize($temp_file_path . "/" . $source_file) / 1024);
//
//            if (!empty($f['extension']) && $f['extension'] == 'csv')
//            {
//                foreach ($target_list as $target) {
//                    if ($target["csv_file_name"] == $source_file && $source_file_size == $target["kb_size"])
//                    {
//                        $is_matched = 1;
//                    }
//                }
//            }
//        }
//        if ($is_matched == 0)
//        {
//            $this->fail('ファイルリストに不備があります' . $source_file . "がリスト内に存在しないかファイルサイズが異なります");
//        }
//        exec("sudo rm -rf {$temp_file_path}");
//    }
}
