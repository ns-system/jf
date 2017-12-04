<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\Traits\Testing\FileTestable;

class UnitImportZenonDataServiceTest extends TestCase
{

    use FileTestable;

    protected $s;
    protected $param;
    protected $rows;
    protected static $init = false;

    public function setUp() {
        parent::setUp();
        if (!static::$init)
        {
            static::$init = true;
            try {
                \Artisan::call('db:reset', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('db:create', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('migrate');
                $csv_lines     = $this->createImportFile();
                $this->unlinkFile(storage_path() . '/tests/K_D_902_M0332_20101001.csv');
                $this->createCsvFile('K_D_902_M0332_20101001.csv', $csv_lines);
                $table_columns = [
                    ['zenon_format_id' => '137', 'column_name' => 'data_state', 'japanese_column_name' => 'データ種類', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'file_state', 'japanese_column_name' => 'ファイル区分', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'prefecture_code', 'japanese_column_name' => '県コード', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'organization_code', 'japanese_column_name' => '漁協信漁連コード', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'store_number', 'japanese_column_name' => '店番', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'account_number', 'japanese_column_name' => '口座番号', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'contract_number', 'japanese_column_name' => '契約番号', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'spare_1', 'japanese_column_name' => '予備', 'column_type' => 'char'],
                    ['zenon_format_id' => '137', 'column_name' => 'created_base_on', 'japanese_column_name' => '作成基準日', 'column_type' => 'date'],
                    ['zenon_format_id' => '137', 'column_name' => 'return_prefecture_code', 'japanese_column_name' => '還元県コード', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'second_organization_code', 'japanese_column_name' => '漁協信漁連コード', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'second_store_number', 'japanese_column_name' => '店番', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'small_store_number', 'japanese_column_name' => '小規模店番', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'term_on', 'japanese_column_name' => '期日', 'column_type' => 'date'],
                    ['zenon_format_id' => '137', 'column_name' => 'subject_code', 'japanese_column_name' => '科目コード', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'second_account_number', 'japanese_column_name' => '口座番号', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'deposit_number', 'japanese_column_name' => '預入番号', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'deposit_amount', 'japanese_column_name' => '入金額', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'payment_amount', 'japanese_column_name' => '出金額', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'process_detail', 'japanese_column_name' => '処理内容', 'column_type' => 'char'],
                    ['zenon_format_id' => '137', 'column_name' => 'category_code', 'japanese_column_name' => '種類コード', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'assist_product_code', 'japanese_column_name' => '補助商品コード', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'customer_number', 'japanese_column_name' => '顧客番号', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'filioparental_state', 'japanese_column_name' => '親子区分', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'taxation_code', 'japanese_column_name' => '課税区分', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'interest_handle', 'japanese_column_name' => '利息取扱方法', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'contracted_on', 'japanese_column_name' => '契約日', 'column_type' => 'date'],
                    ['zenon_format_id' => '137', 'column_name' => 'maturity_on', 'japanese_column_name' => '満期日', 'column_type' => 'date'],
                    ['zenon_format_id' => '137', 'column_name' => 'receipt_started_on', 'japanese_column_name' => '受取開始日', 'column_type' => 'date'],
                    ['zenon_format_id' => '137', 'column_name' => 'receipt_ended_on', 'japanese_column_name' => '受取終了日', 'column_type' => 'date'],
                    ['zenon_format_id' => '137', 'column_name' => 'contract_amount', 'japanese_column_name' => '契約額', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'balance', 'japanese_column_name' => '現在残高', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'application_interest_rate', 'japanese_column_name' => '適用利率', 'column_type' => 'float'],
                    ['zenon_format_id' => '137', 'column_name' => 'contract_deposit_count', 'japanese_column_name' => '契約受入回数', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'contract_payment_count', 'japanese_column_name' => '契約支払回数', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'scheduled_count', 'japanese_column_name' => '予定回次', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'not_deposited_count', 'japanese_column_name' => '未入金回数', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'after_term_balance', 'japanese_column_name' => '期日後残高', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'interest_amount', 'japanese_column_name' => '利息額', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'interest_tax', 'japanese_column_name' => '利子税', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'national_tax', 'japanese_column_name' => '国税', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'local_tax', 'japanese_column_name' => '地方税', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'after_tax_interest', 'japanese_column_name' => '税引後利息', 'column_type' => 'bigInteger'],
                    ['zenon_format_id' => '137', 'column_name' => 'second_created_base_on', 'japanese_column_name' => '作成基準日', 'column_type' => 'date'],
                    ['zenon_format_id' => '137', 'column_name' => 'created_base_month', 'japanese_column_name' => '作成基準年月', 'column_type' => 'integer'],
                    ['zenon_format_id' => '137', 'column_name' => 'head_count', 'japanese_column_name' => '件数', 'column_type' => 'integer'],
                ];
                \App\ZenonTable::insert($table_columns);
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
                echo $exc->getMessage();
            }
        }
    }

    public function __construct() {
        $this->s = new \App\Services\ImportZenonDataService();
    }

    private function setReflection($function_name) {
        $s = new \ReflectionMethod($this->s, $function_name);
        $s->setAccessible(true);
        return $s;
    }

    private function createImportFile() {
        $csv_data = [];
        for ($i = 1; $i <= 1500; $i++) {
            $csv_data[] = [
                1, /* データ種類 */
                10, /* ファイル区分 */
                9999, /* 県コード */
                0, /* 漁協信漁連コード */
                rand(1, 999), /* 店番 */
                rand(1, 9999999999), /* 口座番号 */
                rand(0, 999), /* 契約番号 */
                '', /* 予備 */
                date('Ymd'), /* 作成基準日 */
                9999, /* 還元県コード */
                0, /* 漁協信漁連コード */
                rand(1, 999), /* 店番 */
                rand(1, 999), /* 小規模店番 */
                0, /* 期日 */
                rand(1, 11), /* 科目コード */
                rand(1, 9999999999), /* 口座番号 */
                rand(1, 999), /* 預入番号 */
                rand(0, 99999999999), /* 入金額 */
                rand(0, 99999999999), /* 出金額 */
                '満期', /* 処理内容 */
                rand(0, 999), /* 種類コード */
                rand(0, 999), /* 補助商品コード */
                rand(100, 99999999), /* 顧客番号 */
                rand(0, 1), /* 親子区分 */
                rand(0, 1), /* 課税区分 */
                rand(0, 1), /* 利息取扱方法 */
                date('Ymd'), /* 契約日 */
                date('Ymd'), /* 満期日 */
                0, /* 受取開始日 */
                0, /* 受取終了日 */
                rand(0, 99999999999), /* 契約額 */
                rand(0, 99999999999), /* 現在残高 */
                rand(0, 99999), /* 適用利率 */
                rand(0, 999), /* 契約受入回数 */
                rand(0, 999), /* 契約支払回数 */
                rand(0, 999), /* 予定回次 */
                rand(0, 999), /* 未入金回数 */
                rand(0, 99999999999), /* 期日後残高 */
                rand(0, 99999999999), /* 利息額 */
                rand(0, 99999999999), /* 利子税 */
                rand(0, 99999999999), /* 国税 */
                rand(0, 99999999999), /* 地方税 */
                rand(0, 99999999999), /* 税引後利息 */
                date('Ymd'), /* 作成基準日 */
                date('ym'), /* 作成基準年月 */
                rand(0, 9), /* 件数 */
            ];
        }
        return $csv_data;
    }

    /**
     * @test
     */
    public function 異常系_月別IDセット処理() {
        $row = ['1', '1234567890', 'key_1', 'test user_1', '20170701', 'true'];
        $this->s->setRow($row);
        $s   = $this->setReflection('setMonthlyIdToRow');
        try {
            $s->invoke($this->s, true);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("月別IDが指定されていません。", $e->getMessage());
        }
        try {
            $s->invoke($this->s, true, 'this is not date.');
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("月別IDの指定が不正です。（指定：this is not date.）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_月別IDセット処理() {
        $row      = ['1', '1234567890', 'key_1', 'test user_1', '20170701', 'true'];
        $this->s->setRow($row);
        $s        = $this->setReflection('setMonthlyIdToRow');
        $s->invoke($this->s, false);
        $result_1 = $this->s->getRow();

        $s->invoke($this->s, true, '2017-09-09');
        $result_2 = $this->s->getRow();

        $this->assertFalse(key_exists('monthly_id', $result_1));
        $this->assertEquals('201709', $result_2['monthly_id']);
    }

    /**
     * @test
     */
    public function 正常系_月別ID指定() {
        // FIX:テストになってない
        $this->s->monthlyStatus(201707, [1, 2, 3]);
    }

    /**
     * @test
     */
    public function 異常系_列セット時に失敗() {
        $rows = [
            ['1', '1234567890', 'key_1', 'テストユーザー1', '20170701', 'true'],
            ['4', '2345678901', 'key_2', 'test user_2', '2017-08-21', 'false'],
        ];
        $keys = ['subject_code', 'account_number', 'split_key', 'user_name',];

        try {
            foreach ($rows as $r) {
                $this->s->setRow($r);
                $s = $this->setReflection('setKeyToRow');
                $s->invoke($this->s, $keys);
            }
            $this->fail("予期しないエラーです。");
        } catch (\Exception $e) {
            $this->assertEquals("配列長が一致しませんでした。（想定：4 実際：6）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_タイムスタンプをセットできる() {
        $rows      = ['key_1' => 1];
        $timestamp = date('Y-m-d H:i:s');
        $this->s->setRow($rows);
        $s         = $this->setReflection('setTimeStamp');
        $s->invoke($this->s);
        $result_1  = $this->s->getRow();
        $s->invoke($this->s, $timestamp);
        $result_2  = $this->s->getRow();

        $this->assertTrue(key_exists('created_at', $result_1));
        $this->assertTrue(key_exists('updated_at', $result_1));
        $this->assertEquals(['key_1' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp], $result_2);
    }

    /**
     * @test
     */
    public function 正常系_列セット_分割あり() {
        $rows              = [
            ['split_key' => 'key_1', 'user_name' => 'test user_1', 'created_on' => '2017-07-01', 'is_administrator' => true, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 1234567,],
            ['split_key' => 'key_2', 'user_name' => 'test user_2', 'created_on' => '2017-08-21', 'is_administrator' => false, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 2345678901,],
        ];
        $expect_1          = [
            ['is_administrator' => true, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 1234567, 'split_key' => 'key_1',],
            ['is_administrator' => false, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 2345678901, 'split_key' => 'key_2',],
        ];
        $split_key_configs = ['split_foreign_key_1' => 'split_key', 'split_foreign_key_2' => 'key_account_number'];
        $result_1          = [];
        foreach ($rows as $r) {
            $this->s->setRow($r);
            $s          = $this->setReflection('splitRow');
            $s->invoke($this->s, true, 3, 5, $split_key_configs);
            $result_1[] = $this->s->getRow();
        }
        $this->assertEquals($expect_1, $result_1);
    }

    /**
     * @test
     */
    public function 正常系_列セット_変換処理なし() {
        $rows     = [
            ['split_key' => 'key_1', 'user_name' => 'test user_1', 'created_on' => '2017-07-01', 'is_administrator' => true, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 1234567,],
            ['split_key' => 'key_2', 'user_name' => 'test user_2', 'created_on' => '2017-08-21', 'is_administrator' => false, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 2345678901,],
        ];
        $result_1 = [];
        foreach ($rows as $r) {
            $this->s->setRow($r);
            $s          = $this->setReflection('splitRow');
            $s->invoke($this->s, false);
            $result_1[] = $this->s->getRow();
        }
        $this->assertEquals($rows, $result_1);
    }

    /**
     * @test
     */
    public function 異常系_配列分割処理失敗() {
        $row               = ['1', '1234567890', 'key_1', 'test user_1', '20170701', 'true'];
        $split_key_configs = ['split_foreign_key_1' => 'split_key', 'split_foreign_key_2' => 'key_account_number'];

        $this->s->setRow($row);
        $s = $this->setReflection('splitRow');
        try {
            $s->invoke($this->s, true, -1, -1, $split_key_configs);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("配列切り落としの開始位置が誤っているようです。", $e->getMessage());
        }
        try {
            $s->invoke($this->s, true, 1, -1, $split_key_configs);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("配列切り落としの終了位置が誤っているようです。", $e->getMessage());
        }
        try {
            $s->invoke($this->s, true, 10, 1, $split_key_configs);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("配列切り落としの指定は開始位置 < 終了位置となるように指定してください。", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 異常系_テーブル名未指定() {
        $s = $this->setReflection('getTableObject');
        try {
            $s->invoke($this->s, '', '');
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("コネクションもしくはテーブル名が指定されていないようです。", $e->getMessage());
        }
        try {
            $s->invoke($this->s, 'mysql_zenon', '');
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("コネクションもしくはテーブル名が指定されていないようです。", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 異常系_口座分割処理() {

        $row = [
            "subject_code"     => "1",
            "account_number"   => "1234567890",
            "split_key"        => "key_1",
            "user_name"        => "test user_1",
            "created_on"       => "20170701",
            "is_administrator" => "true",
            "id"               => null,
        ];
        $this->s->setRow($row);
        $s   = $this->setReflection('setConvertedAccountToRow');
        try {
            $s->invoke($this->s, true, null);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("口座分割設定値が指定されていません。", $e->getMessage());
        }
        try {
            $s->invoke($this->s, true, ['account_column_name' => null, 'subject_column_name' => null]);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("指定された口座番号変換キーが不正です。", $e->getMessage());
        }

        $row['account_number'] = null;
        $this->s->setRow($row);

        try {
            $s->invoke($this->s, true, ['account_column_name' => 'account_number', 'subject_column_name' => 'subject_code']);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("口座変換データが不正です。", $e->getMessage());
        }

        $row['account_number'] = 12;
        $this->s->setRow($row);

        try {
            $s->invoke($this->s, true, ['account_column_name' => 'account_number', 'subject_column_name' => 'subject_code']);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("口座番号が短すぎるようです。（科目：1， 口座番号：12）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_口座分割処理() {

        $row      = [
            "subject_code"   => "1",
            "account_number" => "1234567890",
        ];
        $this->s->setRow($row);
        $s        = $this->setReflection('setConvertedAccountToRow');
        $s->invoke($this->s, false);
        $result_1 = $this->s->getRow();

        $s->invoke($this->s, true, ['account_column_name' => 'account_number', 'subject_column_name' => 'subject_code']);
        $result_2 = $this->s->getRow();
//        dd($result_2);

        $this->assertEquals($row, $result_1);
        $this->assertEquals(1234567.0, $result_2['key_account_number']);
    }

    /**
     * @test
     */
    public function 正常系_INSERT成功() {
        $zenon_csv      = [
//            'id'                    => 25,
            'identifier'            => 'M0332',
            'zenon_data_type_id'    => '1',
            'zenon_data_name'       => '貯金期日データ',
//            'first_column_position' => 0,
            'last_column_position'  => 45,
            'column_length'         => 46,
            'reference_return_date' => '月初翌営業日',
            'cycle'                 => 'M',
            'database_name'         => 'zenon_data_db',
            'table_name'            => 'deposit_term_ledgers',
            'is_cumulative'         => 1,
            'is_account_convert'    => 1,
            'is_process'            => 1,
//            'is_split'              => 0,
            'zenon_format_id'       => 137,
            'account_column_name'   => 'account_number',
            'subject_column_name'   => 'subject_code',
//            'split_foreign_key_1'   => '',
//            'split_foreign_key_2'   => '',
//            'split_foreign_key_3'   => '',
//            'split_foreign_key_4'   => '',
        ];
        $monthly_status = ['csv_file_name' => 'K_D_902_M0332_20101001.csv', 'monthly_id' => 201009, 'csv_file_set_on' => '2010-10-01', 'zenon_data_csv_file_id' => null, 'is_execute' => 1, 'is_exist' => 1,];

        $zenon_csv_obj = \App\ZenonCsv::create($zenon_csv);

        $monthly_status['zenon_data_csv_file_id'] = $zenon_csv_obj->id;
        \App\ZenonMonthlyStatus::insert($monthly_status);

        $table_configs = \App\ZenonMonthlyStatus::month(201009)
                ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->where('zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', $zenon_csv_obj->id)
                ->get()
        ;

        $csv_file_object = $this->s->setCsvFileObject(storage_path() . '/tests/K_D_902_M0332_20101001.csv')->getCsvFileObject();

        $start_time = date('Y-m-d H:i:s');
        foreach ($table_configs as $t) {
            $this->s->uploadToDatabase($t, $csv_file_object, 201009);
        }
        $end_time = date('Y-m-d H:i:s');

        $count = \DB::connection('mysql_zenon')->table('deposit_term_ledgers')->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time)->count();

        $this->assertEquals($count, 1500);
    }

    /**
     * @test
     */ public function 正常系_エラーログ出力() {
//        try {
//            \DB::connection('mysql_suisin')->beginTransaction();
        $monthly_status = ['csv_file_name' => 'K_D_902_M0332_20101001.csv', 'file_kb_size' => 338, 'monthly_id' => 201009, 'csv_file_set_on' => '2010-10-01', 'zenon_data_csv_file_id' => 25, 'is_execute' => 1, 'is_pre_process_start' => 0, 'is_pre_process_end' => 0, 'is_pre_process_error' => 0, 'is_post_process_start' => 0, 'is_post_process_end' => 0, 'is_post_process_error' => 0, 'is_exist' => 1, 'is_import' => 0, 'row_count' => 0, 'executed_row_count' => 0,];
        \App\ZenonMonthlyStatus::insert($monthly_status);
        $monthly_object = \App\ZenonMonthlyStatus::month(201009)->first();
        $s              = $this->setReflection('makeErrorLog');
        $result_1       = $s->invoke($this->s, $monthly_object, '無視できるエラー発生');
//        } catch (\Exception $exc) {
//            echo $exc->getMessage();
//        } finally {
//            \DB::connection('mysql_suisin')->rollback();
//        }
        $this->assertEquals('無視できるエラー発生', $result_1['reason']);
    }

    /**
     * @test
     */
    public function 正常系_Database反映時にテーブル設定がない() {
        $monthly_status = [
            'csv_file_name'   => 'K_D_902_M0332_20101001.csv',
//            'file_kb_size'           => 338,
            'monthly_id'      => 201009,
            'csv_file_set_on' => '2010-10-01',
//            'zenon_data_csv_file_id' => 25,
//            'is_execute'             => 1,
//            'is_pre_process_start'   => 0,
//            'is_pre_process_end'     => 0,
//            'is_pre_process_error'   => 0,
//            'is_post_process_start'  => 0,
//            'is_post_process_end'    => 0,
//            'is_post_process_error'  => 0,
//            'is_exist'               => 1,
//            'is_import'              => 0,
//            'row_count'              => 0,
//            'executed_row_count'     => 0,
        ];

        $zenon_data_csv_files = [
            'database_name' => 'zenon_db',
            'table_name'    => 'not_exist_table_name',
        ];


//        try {
//            \DB::connection('mysql_suisin')->beginTransaction();
        \App\ZenonCsv::insert($zenon_data_csv_files);
        $zenon_csv = \App\ZenonCsv::where('table_name', '=', 'not_exist_table_name')->first();

        $monthly_status['zenon_data_csv_file_id'] = $zenon_csv->id;
        \App\ZenonMonthlyStatus::insert($monthly_status);

        $csv_file_object = $this->s->setCsvFileObject(storage_path() . '/tests/K_D_902_M0332_20101001.csv')->getCsvFileObject();
        $zenon_monthly   = \App\ZenonMonthlyStatus::where('zenon_data_csv_file_id', '=', $zenon_csv->id)->first();

        $result_1 = $this->s->uploadToDatabase($zenon_monthly, $csv_file_object, 201009);
//        } catch (\Exception $exc) {
//            echo $exc->getMessage();
//        } finally {
//            \DB::connection('mysql_suisin')->rollback();
//        }
        $this->assertEquals('テーブル設定が取り込まれていないようです。MySQL側 全オンテーブル設定から取込処理を行ってください。', $result_1['reason']);
    }

    /**
     * @test
     */
    public function 異常系_テーブルオブジェクトが取得できない() {
        $s = $this->setReflection('getTableObject');
        try {
            $s->invoke($this->s, 'mysql_zenon', 'not_exist_table_name');
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("ベーステーブルが存在しないようです。（テーブル名：not_exist_table_name）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_Database反映時にテーブルオブジェクトが生成できない() {
        \App\ZenonCsv::truncate();

        $zenon_data_csv_files = ['database_name' => 'zenon_db', 'table_name' => 'not_exist_table_name', 'zenon_format_id' => 99999,];
        $zenon_table_configs  = ['zenon_format_id' => 99999, 'column_name' => 'sample_column',];
        $csv                  = \App\ZenonCsv::create($zenon_data_csv_files);
        \App\ZenonTable::insert($zenon_table_configs);
        $monthly_status       = ['csv_file_name' => 'not_exist_table.csv', 'monthly_id' => 201009, 'zenon_data_csv_file_id' => $csv->id, 'csv_file_set_on' => '2010-10-01',];
        \App\ZenonMonthlyStatus::insert($monthly_status);

        $zenon_csv       = \App\ZenonCsv::where('table_name', '=', 'not_exist_table_name')->first();
        $csv_file_object = $this->s->setCsvFileObject(storage_path() . '/tests/K_D_902_M0332_20101001.csv')->getCsvFileObject();
        $zenon_monthly   = \App\ZenonMonthlyStatus::where('zenon_data_csv_file_id', '=', $zenon_csv->id)
                ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id AS id'))
                ->first()
        ;
        $result_1        = $this->s->uploadToDatabase($zenon_monthly, $csv_file_object, 201009);
        $this->assertEquals("ベーステーブルが存在しないようです。（テーブル名：not_exist_table_name）", $result_1['reason']);
    }

}
