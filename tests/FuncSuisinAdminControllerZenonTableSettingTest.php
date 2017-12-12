<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Services\Traits;

class FuncSuisinAdminControllerConsignorSettingTest extends TestCase
{

    use Traits\CsvUsable;
    use Traits\Testing\DbDisconnectable;

    protected static $init = false;
    protected static $user;

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


    protected $dummy_zenon_csv_data = [
        ['identifier' => "identifier1", 'zenon_data_type_id' => "2", 'zenon_data_name' => "zenondataname1", 'first_column_position' => "0", 'last_column_position' => "198", 'column_length' => "190", 'reference_return_date' => "referencereturndate1", 'cycle' => "M", 'database_name' => "zenon_data_db", 'table_name' => "customer_information_files", 'is_cumulative' => "1", 'is_account_convert' => "0", 'is_process' => "1", 'is_split' => "1", 'zenon_format_id' => "2", 'account_column_name' => "", 'subject_column_name' => "", 'split_foreign_key_1' => "", 'split_foreign_key_2' => "", 'split_foreign_key_3' => "", 'split_foreign_key_4' => "",],];

    //システム設定
    //全オン還元CSVファイル設定 
    /**
     * @tests
     */
    public function 正常系_全オン還元CSVファイル設定を表示できる() {
        $user = static::$user;
        \App\ZenonTable::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonCsv')
                ->seePageIs('/admin/super_user/config/Admin/ZenonCsv')
                ->type('1', 'zenon_format_id')
                ->type('1', 'zenon_data_name')
                ->type('1', 'identifier')
                ->type('1', 'reference_return_date')
                ->type('1', 'cycle')
                ->type('1', 'table_name')
                ->type('1', 'is_cumulative')
                ->type('1', 'is_account_convert')
                ->type('1', 'is_process')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_全オン還元CSVファイル設定でCSVファイルをインポートできる() {
        $user      = static::$user;
        \App\ZenonCsv::truncate();
        $file_name = '全オン還元CSVファイル設定.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonCsv')
                ->seePageIs('/admin/super_user/config/Admin/ZenonCsv')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/super_user/config/Admin/ZenonCsv/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/super_user/config/Admin/ZenonCsv')
                ->see('件の処理が終了しました。')
                ->dontSee('要修正')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data  = explode(',', $csv_file[$i]);
            $where = [
                'identifier'            => trim($data[1]),
                'zenon_data_type_id'    => trim($data[2]),
                'zenon_data_name'       => trim($data[3]),
                'first_column_position' => trim($data[4]),
                'last_column_position'  => trim($data[5]),
                'column_length'         => trim($data[6]),
                'reference_return_date' => trim($data[7]),
                'cycle'                 => trim($data[8]),
                'database_name'         => trim($data[9]),
                'table_name'            => trim($data[10]),
                'is_cumulative'         => trim($data[11]),
                'is_account_convert'    => trim($data[12]),
                'is_process'            => trim($data[13]),
                'is_split'              => trim($data[14]),
                'zenon_format_id'       => trim($data[15]),
                'account_column_name'   => trim($data[16]),
                'subject_column_name'   => trim($data[17]),
                'split_foreign_key_1'   => trim($data[18]),
                'split_foreign_key_2'   => trim($data[19]),
                'split_foreign_key_3'   => trim($data[20]),
                'split_foreign_key_4'   => trim($data[21]),
            ];
            $res   = \App\ZenonCsv::where($where)->count();
            $this->assertEquals($res, 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系_全オン還元CSVファイル設定で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\ZenonCsv::truncate();
        $file_name = '全オン還元CSVファイル設定.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonCsv')
                ->seePageIs('/admin/super_user/config/Admin/ZenonCsv')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/super_user/config/Admin/ZenonCsv')
                ->see('CSVファイルの内容に不備がありました。')
                ->dontSee('要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_全オン還元CSVファイル設定で誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\ZenonCsv::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonCsv')
                ->seePageIs('/admin/super_user/config/Admin/ZenonCsv')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/super_user/config/Admin/ZenonCsv')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
                ->dontSee('要修正')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_全オン還元CSVファイル設定ファイルがエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonCsv')
                ->seePageIs('/admin/super_user/config/Admin/ZenonCsv')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //システム設定
    //テーブルカラム設定 
    /**
     * @tests
     */
    public function 正常系_テーブルカラム設定を表示できる() {
        $user = static::$user;
        \App\ZenonTable::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonTable')
                ->seePageIs('/admin/super_user/config/Admin/ZenonTable')
                ->type('1', 'identifier')
                ->type('1', 'zenon_data_name')
                ->type('1', 'column_name')
                ->type('1', 'japanese_column_name')
                ->type('1', 'column_type')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_テーブルカラム設定でCSVファイルをインポートできる() {
        $user      = static::$user;
        \App\ZenonTable::truncate();
        \App\ZenonCsv::truncate();
        \App\ZenonCsv::insert($this->dummy_zenon_csv_data);
        $file_name = 'テーブルカラム設定.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonTable')
                ->seePageIs('/admin/super_user/config/Admin/ZenonTable')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/super_user/config/Admin/ZenonTable/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/super_user/config/Admin/ZenonTable')
                ->see('件の処理が終了しました。')
                ->dontSee('要修正')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $res  = \App\ZenonTable::where('zenon_format_id', trim($data[0]))->where('column_name', trim($data[1]))->where('japanese_column_name', trim($data[2]))->where('column_type', trim($data[3]))->count();
            $this->assertEquals($res, 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系_テーブルカラム設定で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\ZenonTable::truncate();
        \App\ZenonCsv::truncate();
        \App\ZenonCsv::insert($this->dummy_zenon_csv_data);
        $file_name = 'テーブルカラム設定.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonTable')
                ->seePageIs('/admin/super_user/config/Admin/ZenonTable')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/super_user/config/Admin/ZenonTable')
                ->see('CSVファイルの内容に不備がありました。')
                ->dontSee('要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_テーブルカラム設定で誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\ZenonTable::truncate();
        \App\ZenonCsv::truncate();
        \App\ZenonCsv::insert($this->dummy_zenon_csv_data);
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonTable')
                ->seePageIs('/admin/super_user/config/Admin/ZenonTable')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/super_user/config/Admin/ZenonTable')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
                ->dontSee('要修正')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_テーブルカラム設定ファイルがエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonTable')
                ->seePageIs('/admin/super_user/config/Admin/ZenonTable')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //システム設定
    //全オンカテゴリ名 
    /**
     * @tests
     */
    public function 正常系_全オンカテゴリ名を表示できる() {
        $user = static::$user;
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonType')
                ->seePageIs('/admin/super_user/config/Admin/ZenonType')
                ->type('1', 'data_type_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_全オンカテゴリ名でCSVファイルをインポートできる() {
        $user      = static::$user;
        \App\ZenonType::truncate();
        $file_name = '全オン還元データ種類.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonType')
                ->seePageIs('/admin/super_user/config/Admin/ZenonType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/super_user/config/Admin/ZenonType/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/super_user/config/Admin/ZenonType')
                ->see('件の処理が終了しました。')
                ->dontSee('要修正')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $res  = \App\ZenonType::where('data_type_name', trim($data[1]))->count();
            $this->assertEquals($res, 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系_全オンカテゴリ名で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\ZenonType::truncate();
        $file_name = '全オン還元データ種類.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonType')
                ->seePageIs('/admin/super_user/config/Admin/ZenonType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/super_user/config/Admin/ZenonType')
                ->see('CSVファイルの内容に不備がありました。')
                ->dontSee('要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_全オンカテゴリ名で誤ったCSVファイルがインポートされたときエラー() {
        $user      = static::$user;
        \App\ZenonType::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonType')
                ->seePageIs('/admin/super_user/config/Admin/ZenonType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/super_user/config/Admin/ZenonType')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
                ->dontSee('要修正')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_全オンカテゴリ名ファイルがエクスポートできる() {
        $user = static::$user;
        \App\ZenonType::truncate();

        $this->actingAs($user)
                ->visit('/admin/super_user/config/Admin/ZenonType')
                ->seePageIs('/admin/super_user/config/Admin/ZenonType')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

}
