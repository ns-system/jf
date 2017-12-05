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

    protected static $init             = false;
    protected $user;
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
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
    }

    //科目コード
    /**
     * @tests
     */
    public function 正常系科目コードを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Subject::truncate();
        ;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Subject')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')

        ;
    }

    /**
     * @tests
     */
    public function 正常系科目コードでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Subject::truncate();
        $file_name = '科目コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Subject')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Subject/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/Subject')
                ->see('件の処理が終了しました。')
        ;
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
    public function 異常系科目コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 異常系科目コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系科目コードがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系業種コードを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Industry::truncate();
        ;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Industry')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')

        ;
    }

    /**
     * @tests
     */
    public function 正常系業種コードでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Industry::truncate();
        $file_name = '業種コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Industry')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Industry/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/Industry')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Common\Industry::
                            where('industry_code', trim($data[0]))->
                            where('industry_name', trim($data[1]))->
                            where('industry_content', trim($data[2]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系業種コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 異常系業種コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系業種コードがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系資格区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Qualification::truncate();
        ;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Qualification')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')

        ;
    }

    /**
     * @tests
     */
    public function 正常系資格区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Qualification::truncate();
        $file_name = '資格区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Qualification')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/Qualification')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Common\Qualification::
                            where('qualification_code', trim($data[0]))->
                            where('qualification_type', trim($data[1]))->
                            where('qualification_name', trim($data[2]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系資格区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 異常系資格区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系資格区分がエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系人格コードを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Personality::truncate();
        ;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Personality')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')

        ;
    }

    /**
     * @tests
     */
    public function 正常系人格コードでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Personality::truncate();
        $file_name = '人格コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Personality')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Personality/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/Personality')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Common\Personality::
                            where('personality_code', trim($data[0]))->
                            where('personality_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系人格コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 異常系人格コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系人格コードがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系課税区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\Taxation::truncate();
        ;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTaxation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')

        ;
    }

    /**
     * @tests
     */
    public function 正常系課税区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\Taxation::truncate();
        $file_name = '課税区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTaxation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTaxation')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Deposit\Taxation::
                            where('taxation_code', trim($data[0]))->
                            where('taxation_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系課税区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 異常系課税区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系課税区分がエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系期間コードを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\Term::truncate();
        ;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTerm')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')

        ;
    }

    /**
     * @tests
     */
    public function 正常系期間コードでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\Term::truncate();
        $file_name = '期間コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositTerm')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/DepositTerm')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Deposit\Term::
                            where('term_code', trim($data[0]))->
                            where('term_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系期間コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 異常系期間コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系期間コードがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系継続区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\Continuation::truncate();
        ;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositContinuation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')

        ;
    }

    /**
     * @tests
     */
    public function 正常系継続区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\Continuation::truncate();
        $file_name = '継続区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositContinuation')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/DepositContinuation')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Deposit\Continuation::
                            where('continuation_code', trim($data[0]))->
                            where('continuation_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系継続区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 異常系継続区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系継続区分がエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系貯金種類コードを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\Category::truncate();
        ;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositCategory')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory')

        ;
    }

    /**
     * @tests
     */
    public function 正常系貯金種類コードでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/DepositCategory')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Deposit\Category::
                            where('subject_code', trim($data[0]))->
                            where('category_code', trim($data[2]))->
                            where('category_name', trim($data[3]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系貯金種類コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 異常系貯金種類コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系貯金種類コードがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系通証タイプを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\BankbookType::truncate();
        ;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositBankbookType')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')

        ;
    }

    /**
     * @tests
     */
    public function 正常系通証タイプでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\BankbookType::truncate();
        $file_name = '通証タイプ.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositBankbookType')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/DepositBankbookType')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Deposit\BankbookType::
                            where('bankbook_deed_type', trim($data[0]))->
                            where('bankbook_deed_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系通証タイプで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 異常系通証タイプで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系通証タイプがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系摘要コードを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\Gist::truncate();
        ;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositGist')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')

        ;
    }

    /**
     * @tests
     */
    public function 正常系摘要コードでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Deposit\Gist::truncate();
        $file_name = '摘要コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/DepositGist')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/DepositGist')
                ->see('件の処理が終了しました。')
        ;
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
    public function 異常系摘要コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 異常系摘要コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
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
    public function 正常系摘要コードがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
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
