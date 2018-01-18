<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitCopyCsvFileServiceTest extends TestCase
{

    use \App\Services\Traits\Testing\DbDisconnectable;

    protected $directorys         = [
        'temp'    => 'temp',
        'log'     => '',
        'monthly' => 'monthly',
        'daily'   => 'daily',
        'ignore'  => 'ignore',
        'weekly'  => 'weekly',
        'times'   => 'times' /** 随時ファイルを指す* */
    ];
    protected $dummy_file_name    = [
        'S_D_302_D0255_20170801.csv',
        'K_D_902_M0332_20170801.csv',
        'S_D_301_D0263_20170802',
        'S_D_398_W0106_20170801.csv',
        'S_D_016_T6843_20170801.csv',
    ];
    protected $dummy_csv_template = [
        [
            "identifier"            => "M9999",
            "zenon_data_type_id"    => 9999,
            "zenon_data_name"       => "テストデータ1",
            "reference_return_date" => "月初翌営業日",
            "cycle"                 => "M",
            "database_name"         => "zenon_data_db",
            "table_name"            => "test_table_1",
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

        $base_path = storage_path() . '/tests/csv_files';

        if (file_exists($base_path))
        {
            system("rm -rf {$base_path}");
        }
        $this->disconnect();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function 正常系_ファイルリストが正常取得できる() {

        $service   = new \App\Services\CopyCsvFileService;
        $base_path = storage_path() . '/tests/csv_files';

        $file_path_1 = $base_path . '/' . $this->directorys['temp'] . '/S_D_302_D0255_20179999.csv';
        $file_path_2 = $base_path . '/' . $this->directorys['temp'] . '/S_D_302_T0255_20170801.csv';
        touch($file_path_1);
        touch($file_path_2);
        $tmp_1       = $service->getCsvFileList($base_path . '/temp', $base_path);
        $result_1    = $this->getSimpleFileList($tmp_1);

        $expect_1 = [
            "S_D_302_D0255_20170801.csv",
            "K_D_902_M0332_20170801.csv",
            "S_D_302_T0255_20170801.csv",
            'S_D_016_T6843_20170801.csv',
            'S_D_398_W0106_20170801.csv',
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

    /**
     * @test
     */
    public function 異常系_ディレクトリパスが存在しない() {
        $service        = new \App\Services\CopyCsvFileService;
        $empty_dir_path = "emptydirpath";

        try {
            $service->setDirectoryPath($empty_dir_path);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("存在しないファイルパスが指定されました。（マウント先：{$empty_dir_path}）", $e->getMessage());
        }
        $empty_dir_path = storage_path() . '/tests/csv_files/ignore';
        try {
//           
            $service->setDirectoryPath($empty_dir_path);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("格納先ファイルパスが存在しません。（格納先ファイル：{$empty_dir_path}/temp）", $e->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 異常系_月別IDが正しくない() {
        $service          = new \App\Services\CopyCsvFileService;
        $wrong_monthly_id = 201459;

        try {
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

        $base_path = storage_path() . '/tests/csv_files';
        $service   = new \App\Services\CopyCsvFileService;

        $expect_1 = [
            'K_D_902_M0332_20170801.csv',
        ];
        $expect_2 = [
            'S_D_302_D0255_20170801.csv',
        ];
        $expect_3 = [
            'S_D_398_W0106_20170801.csv',
        ];
        $expect_4 = [
            'S_D_016_T6843_20170801.csv',
        ];
        $service->setMonthlyId($monthly_id)
                ->setDirectoryPath($base_path)
                ->copyCsvFile()
        ;
        $tmp_1    = $service->getCsvFileList($base_path . '/' . $this->directorys['monthly'] . '/' . $monthly_id);
        $result_1 = $this->getSimpleFileList($tmp_1);
        $this->assertEquals($result_1, $expect_1);

        $tmp_2    = $service->getCsvFileList($base_path . '/' . $this->directorys['daily'] . '/' . '201708/01');
        $result_2 = $this->getSimpleFileList($tmp_2);
        $this->assertEquals($result_2, $expect_2);

        $tmp_3    = $service->getCsvFileList($base_path . '/' . $this->directorys['weekly'] . '/201708' );
        $result_3 = $this->getSimpleFileList($tmp_3);
        $this->assertEquals($result_3, $expect_3);
        
        $tmp_4    = $service->getCsvFileList($base_path . '/' . $this->directorys['times'] . '/201708');
        $result_4 = $this->getSimpleFileList($tmp_4);
        $this->assertEquals($result_4, $expect_4);
    }

    /**
     * @tests
     */
    public function 正常系_コピーする対象が一軒も無くても実行できる() {
        $monthly_id = 201707;

        $base_path = storage_path() . '/tests/csv_files';
        $service   = new \App\Services\CopyCsvFileService;
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
        $service->setMonthlyId($monthly_id)
                ->setDirectoryPath($base_path)
                ->copyCsvFile()
        ;
    }

    /**
     * @tests
     */
    public function 異常系_ディレクトリの削除ができる() {

        $service = new \App\Services\CopyCsvFileService;

        $base_path = storage_path() . '/tests/csv_files/';
        $temp_path = $base_path . $this->directorys['temp'];
        $result_1  = $this->getSimpleFileList($service->getCsvFileList($temp_path));
        $expect_1  = [
            "S_D_302_D0255_20170801.csv",
            "K_D_902_M0332_20170801.csv",
            'S_D_016_T6843_20170801.csv',
            'S_D_398_W0106_20170801.csv',
            
        ];
        $service->setDirectoryPath($base_path)->tempFileErase();
        $result_2 = $service->getCsvFileList($temp_path);
        $this->assertEquals($result_1, $expect_1);
        $this->assertEquals($result_2, []);
    }

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
    }

    /**
     * @tests
     */
    public function 正常系_CSVファイルがデータベースに登録できる() {

        try {

            $id                 = \App\ZenonCsv::where('zenon_data_type_id', '=', 9999)->first()->id;
            $templates          = \App\ZenonCsv::where('zenon_data_type_id', '=', 9999)->get();
            $service            = new \App\Services\CopyCsvFileService();
            $base_path          = storage_path() . '/tests/csv_files';
            $file_path_1        = $base_path . '/' . $this->directorys['temp'] . '/K_D_902_C8888_20170801.csv';
            $file_path_2        = $base_path . '/' . $this->directorys['temp'] . '/S_D_302_D0255_20170801.csv';
            $file_path_3        = $base_path . '/' . $this->directorys['temp'] . '/K_D_902_M7777_20170801.csv';
            touch($file_path_1);
            touch($file_path_2);
            touch($file_path_3);
            $service->setMonthlyId(201707)
                    ->setDirectoryPath($base_path)
                    ->tableTemplateCreation(/* $templates */)
                    ->copyCsvFile()
            ;
            $lists              = $service->registrationCsvFileToDatabase();
            $not_exist_list     = $this->removeFileCreateTimeFromList($lists['not_exist']);
            $expect_not_exist_1 = ["K_D_902_M7777_20170801.csv"];
        } catch (\Exception $exc) {
            var_dump($exc->getMessage());

            $this->fail('予期しないエラーです。');
        } finally {
            
        }

        $this->assertEquals($expect_not_exist_1, $this->getSimpleFileList($not_exist_list));
        $this->assertEquals(0, \App\ZenonMonthlyStatus::where("csv_file_name", "K_D_902_M9999_20170801.csv")->value("is_exist"));
        $this->assertEquals(1, \App\ZenonMonthlyStatus::where("csv_file_name", "K_D_902_M0332_20170801.csv")->value("is_exist"));
    }

}
