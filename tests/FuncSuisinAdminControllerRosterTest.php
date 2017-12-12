<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FuncSuisinAdminControllerRosterTest
 *
 * @author r-kawanishi
 */
use App\Services\Traits;

class FuncSuisinAdminControllerRosterTest extends TestCase
{

    use Traits\CsvUsable;
    use Traits\Testing\DbDisconnectable;

    protected static $init = false;
    protected static $user;
    protected static $user1;
    protected static $user2;
    protected static $user3;
    protected static $user4;

    public function setUp() {
        parent::setUp();

        if (!static::$init)
        {
            static::$init = true;
            try {
                \Artisan::call('db:reset', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('db:create', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('migrate');
                static::$user  = factory(\App\User::class)->create(['is_super_user' => '1']);
                static::$user1 = factory(\App\User::class)->create();
                static::$user2 = factory(\App\User::class)->create();
                static::$user3 = factory(\App\User::class)->create();
                static::$user4 = factory(\App\User::class)->create();
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        \App\User::truncate();
        \App\RosterUser::truncate();
        \App\SinrenUser::truncate();
        \App\Division::truncate();
        \App\ControlDivision::truncate();
        \App\WorkType::truncate();
        \App\Rest::truncate();
        \App\RosterUser::firstOrCreate(['user_id' => static::$user1->id, "is_administrator" => '0', "is_chief" => '1', "is_proxy" => '0', "is_proxy_active" => '0', "work_type_id" => '1']);
        \App\RosterUser::firstOrCreate(['user_id' => static::$user2->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0', "work_type_id" => '1']);
        \App\RosterUser::firstOrCreate(['user_id' => static::$user3->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '1', "work_type_id" => '1']);
        \App\RosterUser::firstOrCreate(['user_id' => static::$user4->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0', "work_type_id" => '1']);
        \App\RosterUser::firstOrCreate(['user_id' => static::$user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0', "work_type_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => static::$user1->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => static::$user2->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => static::$user3->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => static::$user4->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => static::$user->id, "division_id" => '1']);
        \App\ControlDivision::firstOrCreate(['user_id' => static::$user1->id, "division_id" => '1']);
        \App\Rest::firstOrCreate(["rest_reason_id" => 1, "rest_reason_name" => "テスト用理由"]);
        \App\Division::firstOrCreate(["division_id" => '1', 'division_name' => 'test']);
        \App\WorkType::firstOrCreate(["work_type_id" => '1', "work_type_name" => "テスト用"]);
    }

    public function tearDown() {
        $this->disconnect();
        parent::tearDown();
    }

    /**
     * @tests
     */
    public function 正常系_勤務時間マスタを表示できる() {

        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/WorkType')
                ->seePageIs('/admin/roster/config/Roster/WorkType')
                ->see("勤務時間マスタ")
                ->type('1', 'work_type_id')
                ->type('1', 'work_type_name')
                ->type('1', 'work_start_time')
                ->type('1', 'work_end_time')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_勤務時間マスタCSVファイルインポートできる() {

        \App\WorkType::truncate();
        $file_name = '勤務時間マスタ.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/WorkType')
                ->seePageIs('/admin/roster/config/Roster/WorkType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/roster/config/Roster/WorkType/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/roster/config/Roster/WorkType')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\WorkType::
                            where('work_type_id', trim($data[0]))->
                            where('work_type_name', trim($data[1]))->
                            where('work_start_time', trim($data[2]))->
                            where('work_end_time', trim($data[3]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系_勤務時間マスタで内容に不備のあるCSVファイルがインポートされたときエラー() {
        \App\WorkType::truncate();
        $file_name = '勤務時間マスタ.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/WorkType')
                ->seePageIs('/admin/roster/config/Roster/WorkType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/roster/config/Roster/WorkType')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_勤務時間マスタで誤ったCSVファイルがインポートされたときエラー() {
        \App\WorkType::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('admin/roster/config/Roster/WorkType')
                ->seePageIs('admin/roster/config/Roster/WorkType')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('admin/roster/config/Roster/WorkType')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_勤務時間マスタがエクスポートできる() {
        \App\WorkType::truncate();
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/WorkType')
                ->seePageIs('/admin/roster/config/Roster/WorkType')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    /**
     * @tests
     */
    public function 正常系_部署マスタを表示できる() {

        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Division')
                ->seePageIs('/admin/roster/config/Roster/Division')
                ->see("部署マスタ")
                ->type('1', 'division_id')
                ->type('1', 'division_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_部署マスタCSVファイルインポートできる() {

        \App\Division::truncate();
        $file_name = '部署マスタ.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Division')
                ->seePageIs('/admin/roster/config/Roster/Division')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/roster/config/Roster/Division/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/roster/config/Roster/Division')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Division::
                            where('division_id', trim($data[0]))->
                            where('division_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系_部署マスタで内容に不備のあるCSVファイルがインポートされたときエラー() {
        \App\Division::truncate();
        $file_name = '部署マスタ.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Division')
                ->seePageIs('/admin/roster/config/Roster/Division')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/roster/config/Roster/Division')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_部署マスタで誤ったCSVファイルがインポートされたときエラー() {
        \App\Division::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('admin/roster/config/Roster/Division')
                ->seePageIs('admin/roster/config/Roster/Division')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('admin/roster/config/Roster/Division')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_部署マスタがエクスポートできる() {
        \App\Division::truncate();
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Division')
                ->seePageIs('/admin/roster/config/Roster/Division')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    /**
     * @tests
     */
    public function 正常系_休暇マスタを表示できる() {

        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Rest')
                ->seePageIs('/admin/roster/config/Roster/Rest')
                ->type('1', 'rest_reason_id')
                ->type('1', 'rest_reason_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_休暇マスタCSVファイルインポートできる() {

        \App\Rest::truncate();
        $file_name = '休暇マスタ.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Rest')
                ->seePageIs('/admin/roster/config/Roster/Rest')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/roster/config/Roster/Rest/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/roster/config/Roster/Rest')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Rest::
                            where('rest_reason_id', trim($data[0]))->
                            where('rest_reason_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系_休暇マスタで内容に不備のあるCSVファイルがインポートされたときエラー() {
        \App\Division::truncate();
        $file_name = '休暇マスタ.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Rest')
                ->seePageIs('/admin/roster/config/Roster/Rest')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/roster/config/Roster/Rest')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_休暇マスタで誤ったCSVファイルがインポートされたときエラー() {
        \App\Division::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('admin/roster/config/Roster/Rest')
                ->seePageIs('admin/roster/config/Roster/Rest')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('admin/roster/config/Roster/Rest')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_休暇マスタがエクスポートできる() {
        \App\Division::truncate();
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Rest')
                ->seePageIs('/admin/roster/config/Roster/Rest')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    /**
     * @tests
     */
    public function 正常系_ユーザーマスタを表示できる() {

        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/RosterUser')
                ->seePageIs('/admin/roster/config/Roster/RosterUser')
                ->type('1', 'division_name')
                ->type('1', 'last_name')
                ->type('1', 'first_name')
                ->type('1', 'staff_number')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_ユーザーマスタCSVファイルインポートできる() {


        $file_name = 'ユーザーマスタ.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/RosterUser')
                ->seePageIs('/admin/roster/config/Roster/RosterUser')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/roster/config/Roster/RosterUser/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/roster/config/Roster/RosterUser')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\RosterUser::
                            where('user_id', trim($data[0]))->
                            where('staff_number', trim($data[4]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系_ユーザーマスタで内容に不備のあるCSVファイルがインポートされたときエラー() {


        $file_name = 'ユーザーマスタ.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/RosterUser')
                ->seePageIs('/admin/roster/config/Roster/RosterUser')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/roster/config/Roster/RosterUser')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_ユーザーマスタで誤ったCSVファイルがインポートされたときエラー() {


        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('admin/roster/config/Roster/RosterUser')
                ->seePageIs('admin/roster/config/Roster/RosterUser')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('admin/roster/config/Roster/RosterUser')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_ユーザーマスタがエクスポートできる() {
        \App\Division::truncate();
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/RosterUser')
                ->seePageIs('/admin/roster/config/Roster/RosterUser')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    /**
     * @tests
     */
    public function 正常系_休日マスタを表示できる() {

        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Holiday')
                ->seePageIs('/admin/roster/config/Roster/Holiday')
                ->see("休日マスタ")
                ->type('1', 'holiday')
                ->type('1', 'holiday_name')
                ->press('検索する')
                ->assertResponseOk()
        ;
    }

    /**
     * @tests
     */
    public function 正常系_休日マスタCSVファイルインポートできる() {

        \App\Holiday::truncate();
        $file_name = '休日マスタ.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Holiday')
                ->seePageIs('/admin/roster/config/Roster/Holiday')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/roster/config/Roster/Holiday/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/roster/config/Roster/Holiday')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Holiday::
                            where('holiday', trim($data[0]))->
                            where('holiday_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 異常系_休日マスタで内容に不備のあるCSVファイルがインポートされたときエラー() {
        \App\Holiday::truncate();
        $file_name = '休日マスタ.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Holiday')
                ->seePageIs('/admin/roster/config/Roster/Holiday')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/roster/config/Roster/Holiday')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_休日マスタで誤ったCSVファイルがインポートされたときエラー() {
        \App\Holiday::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs(static::$user)
                ->visit('admin/roster/config/Roster/Holiday')
                ->seePageIs('admin/roster/config/Roster/Holiday')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('admin/roster/config/Roster/Holiday')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_休日マスタがエクスポートできる() {
        \App\Holiday::truncate();
        $this->actingAs(static::$user)
                ->visit('/admin/roster/config/Roster/Holiday')
                ->seePageIs('/admin/roster/config/Roster/Holiday')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

}
