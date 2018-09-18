<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FuncSuisinAdminController_Deposit
 *
 * @author r-kawanishi
 */
use App\Services\Traits;

class FuncSuisinAdminControllerDepositTest extends TestCase
{

    use Traits\CsvUsable;
    use Traits\Testing\DbDisconnectable;

    protected static $init             = false;
    protected static $user;
    protected $dummy_subject_code_data = [
        [
            "subject_code" => "1",
            "subject_name" => "test1"
        ],
        [
            "subject_code" => "2",
            "subject_name" => "test2"
        ],
        [
            "subject_code" => "3",
            "subject_name" => "test3"
        ],
        [
            "subject_code" => "4",
            "subject_name" => "test4"
        ],
    ];

    public function setUp() {
        parent::setUp();

        if (!static::$init)
        {
            static::$init = true;
            try {
                \Artisan::call('db:reset', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('db:create', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('migrate');
                static::$user = factory(\App\User::class)->create(['is_super_user' => '1']);
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
    }

    public function tearDown() {
        $this->disconnect();
        parent::tearDown();
    }

    //科目コード
    /**
     * @tests
     */
    public function 正常系_科目コードを表示できる() {
        \App\Models\Common\Subject::truncate();
        $user = static::$user;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Subject')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')
                ->type('1', 'subject_code')
                ->type('1', 'subject_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_科目コードでCSVファイルインポートできる() {
        $user      = static::$user;
        \App\Models\Common\Subject::truncate();
        $file_name = '科目コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Subject')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Subject/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Common\Subject::
                            where('subject_code', trim($data[0]))->
                            where('subject_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 正常系_科目コードでマスタの削除ができる() {
        $user      = static::$user;
        \App\Models\Common\Subject::truncate();
        $file_name = '科目コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Subject')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Subject/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Common\Subject::
                            where('subject_code', trim($data[0]))->
                            where('subject_name', trim($data[1]))->
                            count(), 1);
        }
        $system   = 'Suisin';
        $category = "Subject";
        $this->actingAs($user)
                ->visit(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->seePageIs(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->post(route('admin::suisin::delete', ['system' => $system, 'category' => $category,]), ['_token' => csrf_token(), "confirm" => 1])

        ;
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $res = \App\Models\Common\Subject::count();
        $this->assertEquals($res, 0);
    }

    /**
     * @tests
     */
    public function 異常系_科目コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Common\Subject::truncate();
        $file_name = '科目コード.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Subject')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_科目コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Common\Subject::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Subject')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_科目コードがエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Subject')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //業種コード
    /**
     * @tests
     */
    public function 正常系_業種コードを表示できる() {
        $user = static::$user;
        \App\Models\Common\Industry::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Industry')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')
                ->type('1', 'industry_code')
                ->type('1', 'industry_name')
                ->type('1', 'industry_content')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_業種コードでCSVファイルインポートできる() {
        $user      = static::$user;
        \App\Models\Common\Industry::truncate();
        $file_name = '業種コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Industry')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Industry/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'industry_code'    => trim($data[0]),
                'industry_name'    => trim($data[1]),
                'industry_content' => trim($data[2]),
            ];
            $this->assertEquals(\App\Models\Common\Industry::where($where)->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 正常系_業種コードでマスタの削除ができる() {
        $user      = static::$user;
        \App\Models\Common\Industry::truncate();
        $file_name = '業種コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Industry')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Industry/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'industry_code'    => trim($data[0]),
                'industry_name'    => trim($data[1]),
                'industry_content' => trim($data[2]),
            ];
            $this->assertEquals(\App\Models\Common\Industry::where($where)->count(), 1);
        }
        $system   = 'Suisin';
        $category = "Industry";
        $this->actingAs($user)
                ->visit(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->seePageIs(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->post(route('admin::suisin::delete', ['system' => $system, 'category' => $category,]), ['_token' => csrf_token(), "confirm" => 1])

        ;
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $res = \App\Models\Common\Industry::count();
        $this->assertEquals($res, 0);
    }

    /**
     * @tests
     */
    public function 異常系_業種コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Common\Industry::truncate();
        $file_name = '業種コード.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Industry')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_業種コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Common\Industry::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Industry')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_業種コードがエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Industry')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //資格区分
    /**
     * @tests
     */
    public function 正常系_資格区分を表示できる() {
        $user = static::$user;
        \App\Models\Common\Qualification::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Qualification')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')
                ->type('1', 'qualification_code')
                ->type('1', 'qualification_type')
                ->type('1', 'qualification_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_資格区分でCSVファイルインポートできる() {
        $user      = static::$user;
        \App\Models\Common\Qualification::truncate();
        $file_name = '資格区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Qualification')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'qualification_code' => trim($data[0]),
                'qualification_type' => trim($data[1]),
                'qualification_name' => trim($data[2]),
            ];
            $this->assertEquals(\App\Models\Common\Qualification::where($where)->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 正常系_資格区分でマスタの削除ができる() {
        $user      = static::$user;
        \App\Models\Common\Qualification::truncate();
        $file_name = '資格区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Qualification')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'qualification_code' => trim($data[0]),
                'qualification_type' => trim($data[1]),
                'qualification_name' => trim($data[2]),
            ];
            $this->assertEquals(\App\Models\Common\Qualification::where($where)->count(), 1);
        }
        $system   = 'Suisin';
        $category = "Qualification";
        $this->actingAs($user)
                ->visit(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->seePageIs(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->post(route('admin::suisin::delete', ['system' => $system, 'category' => $category,]), ['_token' => csrf_token(), "confirm" => 1])

        ;
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $res = \App\Models\Common\Qualification::count();
        $this->assertEquals($res, 0);
    }

    /**
     * @tests
     */
    public function 異常系_資格区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Common\Qualification::truncate();
        $file_name = '資格区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Qualification')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_資格区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Common\Qualification::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Qualification')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_資格区分がエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Qualification')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //人格コード
    /**
     * @tests
     */
    public function 正常系_人格コードを表示できる() {
        $user = static::$user;
        \App\Models\Common\Personality::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Personality')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')
                ->type('1', 'personality_code')
                ->type('1', 'personality_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_人格コードでCSVファイルインポートできる() {
        $user      = static::$user;
        \App\Models\Common\Personality::truncate();
        $file_name = '人格コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Personality')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Personality/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'personality_code' => trim($data[0]),
                'personality_name' => trim($data[1]),
            ];
            $this->assertEquals(\App\Models\Common\Personality::where($where)->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 正常系_人格コードでマスタの削除ができる() {
        $user      = static::$user;
        \App\Models\Common\Personality::truncate();
        $file_name = '人格コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Personality')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Personality/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'personality_code' => trim($data[0]),
                'personality_name' => trim($data[1]),
            ];
            $this->assertEquals(\App\Models\Common\Personality::where($where)->count(), 1);
        }
        $system   = 'Suisin';
        $category = "Personality";
        $this->actingAs($user)
                ->visit(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->seePageIs(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->post(route('admin::suisin::delete', ['system' => $system, 'category' => $category,]), ['_token' => csrf_token(), "confirm" => 1])

        ;
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $res = \App\Models\Common\Personality::count();
        $this->assertEquals($res, 0);
    }

    /**
     * @tests
     */
    public function 異常系_人格コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Common\Personality::truncate();
        $file_name = '人格コード.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Personality')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_人格コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Common\Personality::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Personality')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_人格コードがエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Personality')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貯金
    //課税区分
    /**
     * @tests
     */
    public function 正常系_課税区分を表示できる() {
        $user = static::$user;
        \App\Models\Deposit\Taxation::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTaxation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')
                ->type('1', 'taxation_code')
                ->type('1', 'taxation_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_課税区分でCSVファイルインポートできる() {
        $user      = static::$user;
        \App\Models\Deposit\Taxation::truncate();
        $file_name = '課税区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTaxation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'taxation_code' => trim($data[0]),
                'taxation_name' => trim($data[1]),
            ];
            $this->assertEquals(\App\Models\Deposit\Taxation::where($where)->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 正常系_課税区分でマスタの削除ができる() {
        $user      = static::$user;
        \App\Models\Deposit\Taxation::truncate();
        $file_name = '課税区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTaxation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'taxation_code' => trim($data[0]),
                'taxation_name' => trim($data[1]),
            ];
            $this->assertEquals(\App\Models\Deposit\Taxation::where($where)->count(), 1);
        }
        $system   = 'Suisin';
        $category = "DepositTaxation";
        $this->actingAs($user)
                ->visit(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->seePageIs(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->post(route('admin::suisin::delete', ['system' => $system, 'category' => $category,]), ['_token' => csrf_token(), "confirm" => 1])

        ;
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $res = \App\Models\Deposit\Taxation::count();
        $this->assertEquals($res, 0);
    }

    /**
     * @tests
     */
    public function 異常系_課税区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\Taxation::truncate();
        $file_name = '課税区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTaxation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_課税区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\Taxation::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTaxation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_課税区分がエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTaxation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貯金
    //期間コード
    /**
     * @tests
     */
    public function 正常系_期間コードを表示できる() {
        $user = static::$user;
        \App\Models\Deposit\Term::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTerm')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')
                ->type('1', 'term_code')
                ->type('1', 'term_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_期間コードでCSVファイルインポートできる() {
        $user      = static::$user;
        \App\Models\Deposit\Term::truncate();
        $file_name = '期間コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTerm')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'term_code' => trim($data[0]),
                'term_name' => trim($data[1]),
            ];
            $this->assertEquals(\App\Models\Deposit\Term::where($where)->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 正常系_期間コードでマスタの削除ができる() {
        $user      = static::$user;
        \App\Models\Deposit\Term::truncate();
        $file_name = '期間コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTerm')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'term_code' => trim($data[0]),
                'term_name' => trim($data[1]),
            ];
            $this->assertEquals(\App\Models\Deposit\Term::where($where)->count(), 1);
        }
        $system   = 'Suisin';
        $category = "DepositTerm";
        $this->actingAs($user)
                ->visit(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->seePageIs(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->post(route('admin::suisin::delete', ['system' => $system, 'category' => $category,]), ['_token' => csrf_token(), "confirm" => 1])

        ;
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $res = \App\Models\Deposit\Term::count();
        $this->assertEquals($res, 0);
    }

    /**
     * @tests
     */
    public function 異常系_期間コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\Term::truncate();
        $file_name = '期間コード.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTerm')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_期間コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\Term::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTerm')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_期間コードがエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTerm')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貯金
    //継続区分
    /**
     * @tests
     */
    public function 正常系_継続区分を表示できる() {
        $user = static::$user;
        \App\Models\Deposit\Continuation::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositContinuation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')
                ->type('1', 'continuation_code')
                ->type('1', 'continuation_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_継続区分でCSVファイルインポートできる() {
        $user      = static::$user;
        \App\Models\Deposit\Continuation::truncate();
        $file_name = '継続区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositContinuation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'continuation_code' => trim($data[0]),
                'continuation_name' => trim($data[1]),
            ];
            $this->assertEquals(\App\Models\Deposit\Continuation::where($where)->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 正常系_継続区分でマスタの削除ができる() {
        $user      = static::$user;
        \App\Models\Deposit\Continuation::truncate();
        $file_name = '継続区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositContinuation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'continuation_code' => trim($data[0]),
                'continuation_name' => trim($data[1]),
            ];
            $this->assertEquals(\App\Models\Deposit\Continuation::where($where)->count(), 1);
        }
        $system   = 'Suisin';
        $category = "DepositContinuation";
        $this->actingAs($user)
                ->visit(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->seePageIs(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->post(route('admin::suisin::delete', ['system' => $system, 'category' => $category,]), ['_token' => csrf_token(), "confirm" => 1])

        ;
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $res = \App\Models\Deposit\Continuation::count();
        $this->assertEquals($res, 0);
    }

    /**
     * @tests
     */
    public function 異常系_継続区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\Continuation::truncate();
        $file_name = '継続区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositContinuation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_継続区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\Continuation::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositContinuation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_継続区分がエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositContinuation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貯金
    //種類コード
    /**
     * @tests
     */
    public function 正常系_貯金種類コードを表示できる() {
        $user = static::$user;
        \App\Models\Deposit\Category::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositCategory')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory')
                ->type('1', 'subject_code')
                ->type('1', 'subject_name')
                ->type('1', 'category_code')
                ->type('1', 'category_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_貯金種類コードでCSVファイルインポートできる() {
        $user      = static::$user;
        \App\Models\Deposit\Category::truncate();
        \App\Models\Common\Subject::truncate();
        \App\Models\Common\Subject::insert($this->dummy_subject_code_data);
        $file_name = '貯金種類コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositCategory')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'subject_code'  => trim($data[0]),
                'category_code' => trim($data[2]),
                'category_name' => trim($data[3]),
            ];
            $this->assertEquals(\App\Models\Deposit\Category::where($where)->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 正常系_貯金種類コードでマスタの削除ができる() {
        $user      = static::$user;
        \App\Models\Deposit\Category::truncate();
        \App\Models\Common\Subject::truncate();
        \App\Models\Common\Subject::insert($this->dummy_subject_code_data);
        $file_name = '貯金種類コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositCategory')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'subject_code'  => trim($data[0]),
                'category_code' => trim($data[2]),
                'category_name' => trim($data[3]),
            ];
            $this->assertEquals(\App\Models\Deposit\Category::where($where)->count(), 1);
        }
        $system   = 'Suisin';
        $category = "DepositCategory";
        $this->actingAs($user)
                ->visit(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->seePageIs(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->post(route('admin::suisin::delete', ['system' => $system, 'category' => $category,]), ['_token' => csrf_token(), "confirm" => 1])

        ;
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $res = \App\Models\Deposit\Category::count();
        $this->assertEquals($res, 0);
    }

    /**
     * @tests
     */
    public function 異常系_貯金種類コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\Category::truncate();
        \App\Models\Common\Subject::truncate();
        \App\Models\Common\Subject::insert($this->dummy_subject_code_data);
        $file_name = '貯金種類コード.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositCategory')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_貯金種類コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\Category::truncate();
        \App\Models\Common\Subject::truncate();
        \App\Models\Common\Subject::insert($this->dummy_subject_code_data);
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositCategory')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_貯金種類コードがエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositCategory')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貯金
    //通証タイプ
    /**
     * @tests
     */
    public function 正常系_通証タイプを表示できる() {
        $user = static::$user;
        \App\Models\Deposit\BankbookType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositBankbookType')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')
                ->type('1', 'bankbook_deed_type')
                ->type('1', 'bankbook_deed_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_通証タイプでCSVファイルインポートできる() {
        $user      = static::$user;
        \App\Models\Deposit\BankbookType::truncate();
        $file_name = '通証タイプ.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositBankbookType')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'bankbook_deed_type' => trim($data[0]),
                'bankbook_deed_name' => trim($data[1]),
            ];
            $this->assertEquals(\App\Models\Deposit\BankbookType::where($where)->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 正常系_通証タイプでマスタの削除ができる() {
        $user      = static::$user;
        \App\Models\Deposit\BankbookType::truncate();
        $file_name = '通証タイプ.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositBankbookType')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'bankbook_deed_type' => trim($data[0]),
                'bankbook_deed_name' => trim($data[1]),
            ];
            $this->assertEquals(\App\Models\Deposit\BankbookType::where($where)->count(), 1);
        }
        $system   = 'Suisin';
        $category = "DepositBankbookType";
        $this->actingAs($user)
                ->visit(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->seePageIs(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->post(route('admin::suisin::delete', ['system' => $system, 'category' => $category,]), ['_token' => csrf_token(), "confirm" => 1])

        ;
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $res = \App\Models\Deposit\BankbookType::count();
        $this->assertEquals($res, 0);
    }

    /**
     * @tests
     */
    public function 異常系_通証タイプで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\BankbookType::truncate();
        $file_name = '通証タイプ.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositBankbookType')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_通証タイプで誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\BankbookCode::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositBankbookType')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_通証タイプがエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositBankbookType')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貯金
    //摘要コード
    /**
     * @tests
     */
    public function 正常系_摘要コードを表示できる() {
        $user = static::$user;
        \App\Models\Deposit\Gist::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositGist')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')
                ->type('1', 'gist_code')
                ->type('1', 'display_gist')
                ->type('1', 'keizai_gist_kanji')
                ->type('1', 'keizai_gist_full_kana')
                ->type('1', 'is_keizai')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_摘要コードでCSVファイルインポートできる() {
        $user      = static::$user;
        \App\Models\Deposit\Gist::truncate();
        $file_name = '摘要コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositGist')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);

            $where = [
                'gist_code'             => trim($data[0]),
                'display_gist'          => trim($data[1]),
                'zenon_gist'            => trim($data[2]),
                'keizai_gist_kanji'     => trim($data[3]),
                'keizai_gist_half_kana' => trim($data[4]),
                'keizai_gist_full_kana' => trim($data[5]),
                'is_keizai'             => trim($data[6]),
            ];

            $this->assertEquals(\App\Models\Deposit\Gist:: where($where)->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 正常系_摘要コードでマスタの削除ができる() {
        $user      = static::$user;
        \App\Models\Deposit\Gist::truncate();
        $file_name = '摘要コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositGist')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist/import')
                ->see('CSVインポート処理を開始しました。処理結果はメールにて通知いたします。')
                ->dontSee('要修正');
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);

            $where = [
                'gist_code'             => trim($data[0]),
                'display_gist'          => trim($data[1]),
                'zenon_gist'            => trim($data[2]),
                'keizai_gist_kanji'     => trim($data[3]),
                'keizai_gist_half_kana' => trim($data[4]),
                'keizai_gist_full_kana' => trim($data[5]),
                'is_keizai'             => trim($data[6]),
            ];

            $this->assertEquals(\App\Models\Deposit\Gist:: where($where)->count(), 1);
        }
        $system   = 'Suisin';
        $category = "DepositGist";
        $this->actingAs($user)
                ->visit(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->seePageIs(route('admin::suisin::index', ['system' => $system, 'category' => $category,]))
                ->post(route('admin::suisin::delete', ['system' => $system, 'category' => $category,]), ['_token' => csrf_token(), "confirm" => 1])

        ;
        exec("php artisan queue:listen --timeout=4");
        sleep(5);
        $res = \App\Models\Deposit\Gist::count();
        $this->assertEquals($res, 0);
    }

    /**
     * @tests
     */
    public function 異常系_摘要コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\Gist::truncate();
        $file_name = '摘要コード.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositGist')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_摘要コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\Models\Deposit\Gist::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositGist')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_摘要コードがエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositGist')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

}
