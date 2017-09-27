<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitImportZenonDataServiceTest extends TestCase
{

    protected $s;
    protected $param;
    protected $rows;

    public function __construct() {
        $this->s = new \App\Services\ImportZenonDataService();
    }

    private function setReflection($function_name) {
        $s = new \ReflectionMethod($this->s, $function_name);
        $s->setAccessible(true);
        return $s;
    }

    /**
     * @test
     */
    public function 異常系_月別IDセット処理() {
        $row = ['1', '1234567890', 'key_1', 'test user_1', '20170701', 'true'];
        try {
            $this->s->setRow($row)->setMonthlyIdToRow(true);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("月別IDが指定されていません。", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_月別ID指定() {
        $obj = $this->s->monthlyStatus(201707, [1, 2, 3]);
    }

    /**
     * @test
     */
    public function 正常系_列セット_分割なし() {
        $rows        = [
            ['1', '1234567890', 'key_1', 'テストユーザー1', '20170701', 'true'],
            ['4', '2345678901', 'key_2', 'test user_2', '2017-08-21', 'false'],
        ];
        $timestamp   = date('Y-m-d H:i:s');
        $expection_1 = [
            ['subject_code' => 1, 'account_number' => 1234567890.0, 'split_key' => 'key_1', 'user_name' => 'テストユーザー1', 'created_on' => '2017-07-01', 'is_administrator' => true, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 1234567, 'created_at' => $timestamp, 'updated_at' => $timestamp,],
            ['subject_code' => 4, 'account_number' => 2345678901.0, 'split_key' => 'key_2', 'user_name' => 'test user_2', 'created_on' => '2017-08-21', 'is_administrator' => false, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 2345678901, 'created_at' => $timestamp, 'updated_at' => $timestamp,],
        ];
        $types       = [
            'subject_code'     => 'integer',
            'account_number'   => 'double',
            'split_key'        => 'string',
            'user_name'        => 'string',
            'created_on'       => 'date',
            'is_administrator' => 'boolean',
        ];
        $keys        = ['subject_code', 'account_number', 'split_key', 'user_name', 'created_on', 'is_administrator'];

        $res_1 = [];
//        var_dump($rows);
        foreach ($rows as $r) {
            $res_1[] = $this->s->setRow($r)
                    ->setKeyToRow($keys)
                    ->convertRow($types, true)
                    ->setMonthlyIdToRow(true, 201707)
                    ->setConvertedAccountToRow(true, ['account_column_name' => 'account_number', 'subject_column_name' => 'subject_code'])
                    ->splitRow(false)
                    ->setTimeStamp()
                    ->setTimeStamp($timestamp)
                    ->getRow()
            ;
        }
        $this->assertEquals($res_1, $expection_1);
    }

    /**
     * @test
     */
    public function 正常系_列セット_分割あり() {
        $rows              = [
            ['1', '1234567890', 'key_1', 'test user_1', '20170701', 'true'],
            ['4', '2345678901', 'key_2', 'test user_2', '2017-08-21', 'false'],
        ];
        $timestamp         = date('Y-m-d H:i:s');
        $expection_1       = [
            ['split_key' => 'key_1', 'user_name' => 'test user_1', 'created_on' => '2017-07-01', 'is_administrator' => true, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 1234567, 'created_at' => $timestamp, 'updated_at' => $timestamp,],
            ['split_key' => 'key_2', 'user_name' => 'test user_2', 'created_on' => '2017-08-21', 'is_administrator' => false, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 2345678901, 'created_at' => $timestamp, 'updated_at' => $timestamp,],
        ];
        $types             = ['integer', 'double', 'string', 'string', 'date', 'boolean'];
        $keys              = ['subject_code', 'account_number', 'split_key', 'user_name', 'created_on', 'is_administrator'];
        $split_key_configs = ['split_foreign_key_1' => 'split_key', 'split_foreign_key_2' => 'key_account_number'];
        $convert_keys      = ['account_column_name' => 'account_number', 'subject_column_name' => 'subject_code'];


        $res_1 = [];
        foreach ($rows as $r) {
            $res_1[] = $this->s->setRow($r)
                    ->convertRow($types, true)
                    ->setKeyToRow($keys)
                    ->setMonthlyIdToRow(true, 201707)
                    ->setConvertedAccountToRow(true, $convert_keys)
                    ->splitRow(true, 3, 5, $split_key_configs)
                    ->setTimeStamp($timestamp)
                    ->getRow()
            ;
        }
        $this->assertEquals($res_1, $expection_1);
    }

    /**
     * @test
     */
    public function 正常系_列セット_変換処理なし() {
        $rows        = [
            ['1', '1234567890', 'key_1', 'test user_1', '20170701', 'true'],
            ['4', '2345678901', 'key_2', 'test user_2', '2017/08/21', 'false'],
        ];
        $expection_1 = [
            ['subject_code' => 1, 'account_number' => 1234567890.0, 'split_key' => 'key_1', 'user_name' => 'test user_1', 'created_on' => '2017-07-01', 'is_administrator' => true, 'id' => null,],
            ['subject_code' => 4, 'account_number' => 2345678901.0, 'split_key' => 'key_2', 'user_name' => 'test user_2', 'created_on' => '2017-08-21', 'is_administrator' => false, 'id' => null,],
        ];
        $types       = ['integer', 'double', 'string', 'string', 'date', 'boolean'];
        $keys        = ['subject_code', 'account_number', 'split_key', 'user_name', 'created_on', 'is_administrator'];

        $res_1 = [];
        foreach ($rows as $r) {
            $res_1[] = $this->s->setRow($r)
                    ->convertRow($types, true)
                    ->setKeyToRow($keys)
                    ->setMonthlyIdToRow(false)
                    ->setConvertedAccountToRow(false)
                    ->getRow()
            ;
        }
        $this->assertEquals($res_1, $expection_1);
    }

    /**
     * @test
     */
    public function 異常系_配列分割処理失敗() {
        $row               = ['1', '1234567890', 'key_1', 'test user_1', '20170701', 'true'];
        $split_key_configs = ['split_foreign_key_1' => 'split_key', 'split_foreign_key_2' => 'key_account_number'];
        try {
            $this->s->setRow($row)->splitRow(true, -1, -1, $split_key_configs);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("配列切り落としの開始位置が誤っているようです。", $e->getMessage());
        }
        try {
            $this->s->setRow($row)->splitRow(true, 1, -1, $split_key_configs);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("配列切り落としの終了位置が誤っているようです。", $e->getMessage());
        }
        try {
            $this->s->setRow($row)->splitRow(true, 10, 1, $split_key_configs);
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
        $row  = ['1', '1234567890', 'key_1', 'test user_1', '20170701', 'true'];
        $keys = ['subject_code', 'account_number', 'split_key', 'user_name', 'created_on', 'is_administrator'];

        try {
            $this->s->setRow($row)
                    ->setKeyToRow($keys)
                    ->setConvertedAccountToRow(true, null)
            ;
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("口座分割設定値が指定されていません。", $e->getMessage());
        }
        try {
            $this->s->setRow($row)
                    ->setKeyToRow($keys)
                    ->setConvertedAccountToRow(true, ['account_column_name' => null, 'subject_column_name' => null])
            ;
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("指定された口座番号変換キーが不正です。", $e->getMessage());
        }
        $row[1] = null;
        try {
            $this->s->setRow($row)
                    ->setKeyToRow($keys)
                    ->setConvertedAccountToRow(true, ['account_column_name' => 'account_number', 'subject_column_name' => 'subject_code'])
            ;
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("口座変換データが不正です。", $e->getMessage());
        }
        $row[1] = 12;
        try {
            $this->s->setRow($row)
                    ->setKeyToRow($keys)
                    ->setConvertedAccountToRow(true, ['account_column_name' => 'account_number', 'subject_column_name' => 'subject_code'])
            ;
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("口座番号が短すぎるようです。（科目：1， 口座番号：12）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_INSERT成功() {
        $monthly_status = [
            'csv_file_name'          => 'K_D_902_M0332_20170801.csv',
            'file_kb_size'           => 8645,
            'monthly_id'             => 201707,
            'csv_file_set_on'        => '2017-08-01',
            'zenon_data_csv_file_id' => 25,
            'is_execute'             => 1,
            'is_pre_process_start'   => 0,
            'is_pre_process_end'     => 0,
            'is_pre_process_error'   => 0,
            'is_post_process_start'  => 0,
            'is_post_process_end'    => 0,
            'is_post_process_error'  => 0,
            'is_exist'               => 1,
            'is_import'              => 0,
            'row_count'              => 0,
            'executed_row_count'     => 0,
        ];

        try {
            \DB::connection('mysql_zenon')->beginTransaction();
            \DB::connection('mysql_suisin')->beginTransaction();
            \App\ZenonMonthlyStatus::insert($monthly_status);
            $table_configs = \App\ZenonMonthlyStatus::month(201707)
                    ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                    ->where('zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 25)
                    ->get()
            ;

            $csv_file_object = $this->s->setCsvFileObject(storage_path() . '/tests/K_D_902_M0332_20170801.csv')->getCsvFileObject();

            $start_time = date('Y-m-d H:i:s');
            foreach ($table_configs as $t) {
                $this->s->uploadToDatabase($t, $csv_file_object, 201707);
            }
            $end_time = date('Y-m-d H:i:s');
            $count    = \DB::connection('mysql_zenon')
                    ->table('deposit_term_ledgers')
                    ->where('created_at', '>=', $start_time)
                    ->where('created_at', '<=', $end_time)
                    ->count()
            ;
            $this->assertEquals($count, 1500);
        } catch (\Exception $exc) {
            $this->fail('予期しないエラー');
            //          echo $exc->getTraceAsString();
        } finally {
            \DB::connection('mysql_zenon')->rollback();
            \DB::connection('mysql_suisin')->rollback();
        }
    }

    /**
     * @test
     */
    public function 正常系_事前ステータス変更() {
        $monthly_status = [
            'csv_file_name'          => 'K_D_902_M0332_20170801.csv',
            'file_kb_size'           => 8645,
            'monthly_id'             => 201707,
            'csv_file_set_on'        => '2017-08-01',
            'zenon_data_csv_file_id' => 25,
            'is_execute'             => 1,
            'is_pre_process_start'   => 0,
            'is_pre_process_end'     => 0,
            'is_pre_process_error'   => 0,
            'is_post_process_start'  => 0,
            'is_post_process_end'    => 0,
            'is_post_process_error'  => 0,
            'is_exist'               => 1,
            'is_import'              => 0,
            'row_count'              => 0,
            'executed_row_count'     => 0,
        ];

        try {
            \DB::connection('mysql_suisin')->beginTransaction();
            \App\ZenonMonthlyStatus::insert($monthly_status);
            $table_configs = \App\ZenonMonthlyStatus::month(201707)
                    ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                    ->where('zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 25)
                    ->first()
            ;
            $before        = [
                'start' => $table_configs->is_pre_process_start,
                'end'   => $table_configs->is_pre_process_end,
                'count' => $table_configs->row_count,
            ];

            $this->s->setPreProcessStartToMonthlyStatus($table_configs);
            $after['start'] = $table_configs->is_pre_process_start;
            $this->s->setPreProcessEndToMonthlyStatus($table_configs, 200);
            $after['end']   = $table_configs->is_pre_process_end;
            $after['count'] = $table_configs->row_count;

            $this->assertEquals($before, ['start' => false, 'end' => false, 'count' => 0]);
            $this->assertEquals($after, ['start' => true, 'end' => true, 'count' => 200]);
            \DB::connection('mysql_suisin')->rollback();
        } catch (\Exception $exc) {
            $this->fail('予期しないエラー');

            \DB::connection('mysql_suisin')->rollback();
        }
    }

    /**
     * @test
     */
    public function 正常系_本ステータス変更() {
        $monthly_status = [
            'csv_file_name'          => 'K_D_902_M0332_20170801.csv',
            'file_kb_size'           => 8645,
            'monthly_id'             => 201707,
            'csv_file_set_on'        => '2017-08-01',
            'zenon_data_csv_file_id' => 25,
            'is_execute'             => 1,
            'is_pre_process_start'   => 0,
            'is_pre_process_end'     => 0,
            'is_pre_process_error'   => 0,
            'is_post_process_start'  => 0,
            'is_post_process_end'    => 0,
            'is_post_process_error'  => 0,
            'is_exist'               => 1,
            'is_import'              => 0,
            'row_count'              => 0,
            'executed_row_count'     => 0,
        ];


        try {
            \DB::connection('mysql_suisin')->beginTransaction();
            \App\ZenonMonthlyStatus::insert($monthly_status);
            $table_configs = \App\ZenonMonthlyStatus::month(201707)
                    ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                    ->where('zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 25)
                    ->first()
            ;
//        var_dump($table_configs);
            $before        = [
                'import' => $table_configs->is_import,
                'start'  => $table_configs->is_post_process_start,
                'end'    => $table_configs->is_post_process_end,
                'count'  => $table_configs->executed_row_count,
            ];

            $this->s->setPostProcessStartToMonthlyStatus($table_configs);
            $after['start']  = $table_configs->is_post_process_start;
            $this->s->setPostProcessEndToMonthlyStatus($table_configs);
            $after['end']    = $table_configs->is_post_process_end;
            $after['import'] = $table_configs->is_import;
            $this->s->setExecutedRowCountToMonthlyStatus($table_configs, 200);
            $after['count']  = $table_configs->executed_row_count;

            $this->assertEquals($before, ['import' => false, 'start' => false, 'end' => false, 'count' => 0]);
            $this->assertEquals($after, ['import' => true, 'start' => true, 'end' => true, 'count' => 200]);
            \DB::connection('mysql_suisin')->rollback();
        } catch (\Exception $exc) {
            $this->fail('予期しないエラー');
            \DB::connection('mysql_suisin')->rollback();
        }
    }

    /**
     * @test
     */
    public function 正常系_更新日時取得() {
        $res_1 = $this->s->getLastTraded(null, '2017-07-21');
        $res_2 = $this->s->getLastTraded('2017-12-09', '2017-07-21');

        $this->assertEquals($res_1, '2017-07-21');
        $this->assertEquals($res_2, '2017-12-09');
    }

}
