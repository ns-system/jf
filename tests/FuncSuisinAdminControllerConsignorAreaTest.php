<?php

use App\Services\Traits;

class FuncSuisinAdminControllerConsignorAreaTest extends TestCase
{

    use Traits\CsvUsable;

    protected static $init                    = false;
    protected $user;
    protected $dummy_prefecture_data          = [
        "prefecture_code" => "9491",
        "prefecture_name" => "長崎県",
    ];
    protected $dummy_store_data               = [
        "prefecture_code" => "9491",
        "store_name"      => "本店",
        "store_number"    => "1",
    ];
    protected $dummy_smallstore_data          = [
        [
            "prefecture_code"    => "9491",
            "small_store_name"   => "本店",
            "store_number"       => "1",
            "small_store_number" => "1",
            "control_store_code" => "1",
        ],
        [
            "prefecture_code"    => "9491",
            "small_store_name"   => "五島支店",
            "store_number"       => "2",
            "small_store_number" => "2",
            "control_store_code" => "2",
        ]
    ];
    protected $dummy_area_data                = [
        [
            "prefecture_code"    => "9491",
            "store_number"       => "1",
            "small_store_number" => "1",
            "area_code"          => "1",
            "area_name"          => "本店",
        ],
        [
            "prefecture_code"    => "9491",
            "store_number"       => "2",
            "small_store_number" => "2",
            "area_code"          => "2",
            "area_name"          => "五島支店",
        ]
    ];
    protected $dummy_subject_code_data        = [
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
    protected $dummy_consignor_group_data     = [
        [
            "group_name"     => "test1",
            "create_user_id" => "0",
            "modify_user_id" => "0",
        ],
        [
            "group_name"     => "test2",
            "create_user_id" => "0",
            "modify_user_id" => "0",
        ],
        [
            "group_name"     => "test3",
            "create_user_id" => "0",
            "modify_user_id" => "0",
        ],
    ];
    protected $preregistration_consignor_data = [
        [
            "consignor_code"           => "11111",
            "consignor_name"           => "consignorname1",
            "total_count"              => "1",
            "reference_last_traded_on" => "",
            "display_consignor_name"   => "displayconsignorname1",
            "consignor_group_id"       => "1",
        ],
        [
            "consignor_code"           => "22222",
            "consignor_name"           => "consignorname2",
            "total_count"              => "2",
            "reference_last_traded_on" => "",
            "display_consignor_name"   => "displayconsignorname2",
            "consignor_group_id"       => "2",
        ],
        [
            "consignor_code"           => "33333",
            "consignor_name"           => "consignorname3",
            "total_count"              => "3",
            "reference_last_traded_on" => "",
            "display_consignor_name"   => "displayconsignorname3",
            "consignor_group_id"       => "3",
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

    //共通
    /**
     * @tests
     */
    public function 管理者が管理者画面を見ることができる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);

        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/')
                ->see('推進支援システム マスタ編集')
        ;
    }

    /**
     * @tests
     */
    public function 管理者以外のユーザーが管理者用ページを見たときエラーになる() {
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/')
                ->see('許可されていないアクセスを行おうとしました。')
        ;
    }

    //委託者リスト
    /**
     * @tests
     */
    public function 委託者リストを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Consignor::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Consignor')
                ->seePageIs('/admin/suisin/config/Suisin/Consignor')
                ->see('委託者リスト')
        ;
    }

//委託書リストが取り込み時エラーになるため保留
    /**
     * @tests
     */
    public function 委託者リストでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Consignor::truncate();
        \App\ConsignorGroup::truncate();
        \App\Consignor::insert($this->preregistration_consignor_data);
        \App\ConsignorGroup::insert($this->dummy_consignor_group_data);
        $file_name = '委託者リスト.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Consignor')
                ->seePageIs('/admin/suisin/config/Suisin/Consignor')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Consignor/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/Consignor')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Consignor::
                            where('consignor_code', trim($data[0]))->
                            where('consignor_name', trim($data[1]))->
                            where('display_consignor_name', trim($data[2]))->
                            where('consignor_group_id', trim($data[3]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 委託者リストで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Consignor::truncate();
        \App\ConsignorGroup::truncate();
        \App\Consignor::insert($this->preregistration_consignor_data);
        \App\ConsignorGroup::insert($this->dummy_consignor_group_data);
        $file_name = '委託者リスト.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Consignor')
                ->seePageIs('/admin/suisin/config/Suisin/Consignor')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Consignor')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 委託者リストで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Consignor::truncate();
        \App\ConsignorGroup::truncate();
        \App\Consignor::insert($this->preregistration_consignor_data);
        \App\ConsignorGroup::insert($this->dummy_consignor_group_data);
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Consignor')
                ->seePageIs('/admin/suisin/config/Suisin/Consignor')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Consignor')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 委託者リストファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Consignor::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Consignor')
                ->seePageIs('/admin/suisin/config/Suisin/Consignor')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //委託者グループ
    /**
     * @tests
     */
    public function 委託者グループを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Consignor::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/ConsignorGroup')
                ->seePageIs('/admin/suisin/config/Suisin/ConsignorGroup')

        ;
    }

    /**
     * @tests
     */
    public function 委託者グループでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ConsignorGroup::truncate();
        $file_name = '委託者グループ.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/ConsignorGroup')
                ->seePageIs('/admin/suisin/config/Suisin/ConsignorGroup')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/ConsignorGroup/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/ConsignorGroup')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\ConsignorGroup::where('group_name', trim($data[1]))->where('id', $data[0])->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 委託者グループで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Consignor::truncate();
        $file_name = '委託者グループ.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/ConsignorGroup')
                ->seePageIs('/admin/suisin/config/Suisin/ConsignorGroup')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/ConsignorGroup')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 委託者グループで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Consignor::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/ConsignorGroup')
                ->seePageIs('/admin/suisin/config/Suisin/ConsignorGroup')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/ConsignorGroup')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 委託者グループがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/ConsignorGroup')
                ->seePageIs('/admin/suisin/config/Suisin/ConsignorGroup')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    /**
     * @tests
     */
    public function 県コードを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Prefecture')
                ->seePageIs('/admin/suisin/config/Suisin/Prefecture')

        ;
    }

    /**
     * @tests
     */
    public function 県コードでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        $file_name = '県コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Prefecture')
                ->seePageIs('/admin/suisin/config/Suisin/Prefecture')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Prefecture/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/Prefecture')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Common\Prefecture::where('prefecture_code', trim($data[0]))->where('prefecture_name', trim($data[1]))->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 県コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        $file_name = '県コード.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Prefecture')
                ->seePageIs('/admin/suisin/config/Suisin/Prefecture')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Prefecture')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 県コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Prefecture')
                ->seePageIs('/admin/suisin/config/Suisin/Prefecture')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Prefecture')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 県コードがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Prefecture')
                ->seePageIs('/admin/suisin/config/Suisin/Prefecture')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //店番
    /**
     * @tests
     */
    public function 店番を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Store')
                ->seePageIs('/admin/suisin/config/Suisin/Store')

        ;
    }

    /**
     * @tests
     */
    public function 店番でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        $file_name = '店番.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Store')
                ->seePageIs('/admin/suisin/config/Suisin/Store')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Store/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/Store')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Common\Store::where('prefecture_code', trim($data[0]))->where('store_number', trim($data[1]))->where('store_name', trim($data[3]))->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 店番で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        $file_name = '店番.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Store')
                ->seePageIs('/admin/suisin/config/Suisin/Store')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Store')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 店番で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Store')
                ->seePageIs('/admin/suisin/config/Suisin/Store')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Store')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 店番がエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Store')
                ->seePageIs('/admin/suisin/config/Suisin/Store')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //小規模店番
    /**
     * @tests
     */
    public function 小規模店番を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Store')
                ->seePageIs('/admin/suisin/config/Suisin/Store')

        ;
    }

    /**
     * @tests
     */
    public function 小規模店番でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        \App\Models\Common\Store::insert($this->dummy_store_data);
        $file_name = '小規模店番.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/SmallStore')
                ->seePageIs('/admin/suisin/config/Suisin/SmallStore')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/SmallStore/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/SmallStore')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Common\SmallStore::
                            where('prefecture_code', trim($data[0]))->
                            where('store_number', trim($data[1]))->
                            where('control_store_code', trim($data[2]))->
                            where('small_store_number', trim($data[3]))->
                            where('small_store_name', trim($data[7]))->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 小規模店番で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        \App\Models\Common\Store::insert($this->dummy_store_data);
        $file_name = '小規模店番.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/SmallStore')
                ->seePageIs('/admin/suisin/config/Suisin/SmallStore')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/SmallStore')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 小規模店番で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        \App\Models\Common\Store::insert($this->dummy_store_data);
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/SmallStore')
                ->seePageIs('/admin/suisin/config/Suisin/SmallStore')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/SmallStore')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 小規模店番がエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/SmallStore')
                ->seePageIs('/admin/suisin/config/Suisin/SmallStore')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //地区コード
    /**
     * @tests
     */
    public function 地区コードを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Area::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Area')
                ->seePageIs('/admin/suisin/config/Suisin/Area')

        ;
    }

    /**
     * @tests
     */
    public function 地区コードでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        \App\Models\Common\Store::insert($this->dummy_store_data);
        \App\Models\Common\SmallStore::insert($this->dummy_smallstore_data);
        $file_name = '地区コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Area')
                ->seePageIs('/admin/suisin/config/Suisin/Area')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Area/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/Area')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Common\Area::
                            where('prefecture_code', trim($data[0]))->
                            where('store_number', trim($data[1]))->
                            where('area_code', trim($data[3]))->
                            where('area_name', trim($data[7]))->count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 地区コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        \App\Models\Common\Store::insert($this->dummy_store_data);
        \App\Models\Common\SmallStore::insert($this->dummy_smallstore_data);
        $file_name = '地区コード.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Area')
                ->seePageIs('/admin/suisin/config/Suisin/Area')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Area')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 地区コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        \App\Models\Common\Store::insert($this->dummy_store_data);
        \App\Models\Common\SmallStore::insert($this->dummy_smallstore_data);
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Area')
                ->seePageIs('/admin/suisin/config/Suisin/Area')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/Area')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 地区コードがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Area')
                ->seePageIs('/admin/suisin/config/Suisin/Area')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //管轄店舗
    /**
     * @tests
     */
    public function 管轄店舗を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Area::truncate();
        \App\ControlStore::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/ControlStore')
                ->seePageIs('/admin/suisin/config/Suisin/ControlStore')

        ;
    }

    /**
     * @tests
     */
    public function 管轄店舗でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Area::truncate();
        \App\ControlStore::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        \App\Models\Common\Store::insert($this->dummy_store_data);
        \App\Models\Common\SmallStore::insert($this->dummy_smallstore_data);
        \App\Models\Common\Area::insert($this->dummy_area_data);
        $file_name = '管轄店舗.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/ControlStore')
                ->seePageIs('/admin/suisin/config/Suisin/ControlStore')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/ControlStore/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/ControlStore')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\ControlStore::
                            where('prefecture_code', trim($data[0]))->
                            where('control_store_code', trim($data[1]))->
                            where('control_store_name', trim($data[3]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 管轄店舗で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Area::truncate();
        \App\ControlStore::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        \App\Models\Common\Store::insert($this->dummy_store_data);
        \App\Models\Common\SmallStore::insert($this->dummy_smallstore_data);
        \App\Models\Common\Area::insert($this->dummy_area_data);
        $file_name = '管轄店舗.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/ControlStore')
                ->seePageIs('/admin/suisin/config/Suisin/ControlStore')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/ControlStore')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 管轄店舗で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Common\Prefecture::truncate();
        \App\Models\Common\Store::truncate();
        \App\Models\Common\SmallStore::truncate();
        \App\Models\Common\Area::truncate();
        \App\ControlStore::truncate();
        \App\Models\Common\Prefecture::insert($this->dummy_prefecture_data);
        \App\Models\Common\Store::insert($this->dummy_store_data);
        \App\Models\Common\SmallStore::insert($this->dummy_smallstore_data);
        \App\Models\Common\Area::insert($this->dummy_area_data);
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/ControlStore')
                ->seePageIs('/admin/suisin/config/Suisin/ControlStore')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/ControlStore')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }

    /**
     * @tests
     */
    public function 管轄店舗がエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/ControlStore')
                ->seePageIs('/admin/suisin/config/Suisin/ControlStore')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

}
