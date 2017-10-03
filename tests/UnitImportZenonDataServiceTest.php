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
        // FIX:テストになってない
        $obj = $this->s->monthlyStatus(201707, [1, 2, 3]);
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
                $this->s->setRow($r)->setKeyToRow($keys);
            }
            $this->fail("予期しないエラーです。");
        } catch (\Exception $e) {
            $this->assertEquals("配列長が一致しませんでした。（想定：4 実際：6）", $e->getMessage());
        }
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
        $types       = ['subject_code' => 'integer', 'account_number' => 'double', 'split_key' => 'string', 'user_name' => 'string', 'created_on' => 'date', 'is_administrator' => 'boolean',];
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
        // 環境依存してない？これ
        $monthly_status = ['csv_file_name' => 'K_D_902_M0332_20101001.csv', 'file_kb_size' => 338, 'monthly_id' => 201009, 'csv_file_set_on' => '2010-10-01', 'zenon_data_csv_file_id' => 25, 'is_execute' => 1, 'is_pre_process_start' => 0, 'is_pre_process_end' => 0, 'is_pre_process_error' => 0, 'is_post_process_start' => 0, 'is_post_process_end' => 0, 'is_post_process_error' => 0, 'is_exist' => 1, 'is_import' => 0, 'row_count' => 0, 'executed_row_count' => 0,];

        try {
            \DB::connection('mysql_zenon')->beginTransaction();
            \DB::connection('mysql_suisin')->beginTransaction();
            \App\ZenonMonthlyStatus::insert($monthly_status);
            $table_configs = \App\ZenonMonthlyStatus::month(201009)
                    ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                    ->where('zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 25)
                    ->get()
            ;

            $csv_file_object = $this->s->setCsvFileObject(storage_path() . '/tests/K_D_902_M0332_20101001.csv')->getCsvFileObject();

            $start_time = date('Y-m-d H:i:s');
            foreach ($table_configs as $t) {
                $this->s->uploadToDatabase($t, $csv_file_object, 201009);
            }
            $end_time = date('Y-m-d H:i:s');
            $count    = \DB::connection('mysql_zenon')
                    ->table('deposit_term_ledgers')
                    ->where('created_at', '>=', $start_time)
                    ->where('created_at', '<=', $end_time)
                    ->count()
            ;
        } catch (\Exception $exc) {
            echo $exc->getMessage();
            $this->fail('予期しないエラー');
            //          echo $exc->getTraceAsString();
        } finally {
            \DB::connection('mysql_zenon')->rollback();
            \DB::connection('mysql_suisin')->rollback();
        }
        $this->assertEquals($count, 1500);
    }

    /**
     * @test
     */ public function 正常系_エラーログ出力() {
        try {
            \DB::connection('mysql_suisin')->beginTransaction();
            $monthly_status = ['csv_file_name' => 'K_D_902_M0332_20101001.csv', 'file_kb_size' => 338, 'monthly_id' => 201009, 'csv_file_set_on' => '2010-10-01', 'zenon_data_csv_file_id' => 25, 'is_execute' => 1, 'is_pre_process_start' => 0, 'is_pre_process_end' => 0, 'is_pre_process_error' => 0, 'is_post_process_start' => 0, 'is_post_process_end' => 0, 'is_post_process_error' => 0, 'is_exist' => 1, 'is_import' => 0, 'row_count' => 0, 'executed_row_count' => 0,];
            \App\ZenonMonthlyStatus::insert($monthly_status);
            $monthly_object = \App\ZenonMonthlyStatus::month(201009)->first();
            $s              = $this->setReflection('makeErrorLog');
            $result_1       = $s->invoke($this->s, $monthly_object, '無視できるエラー発生');
        } catch (\Exception $exc) {
            echo $exc->getMessage();
        } finally {
            \DB::connection('mysql_suisin')->rollback();
        }
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
            'database_name' => 'zenon_data_db',
            'table_name'    => 'not_exist_table_name',
        ];


        try {
            \DB::connection('mysql_suisin')->beginTransaction();
            \App\ZenonCsv::insert($zenon_data_csv_files);
            $zenon_csv = \App\ZenonCsv::where('table_name', '=', 'not_exist_table_name')->first();

            $monthly_status['zenon_data_csv_file_id'] = $zenon_csv->id;
            \App\ZenonMonthlyStatus::insert($monthly_status);

            $csv_file_object = $this->s->setCsvFileObject(storage_path() . '/tests/K_D_902_M0332_20101001.csv')->getCsvFileObject();
            $zenon_monthly   = \App\ZenonMonthlyStatus::where('zenon_data_csv_file_id', '=', $zenon_csv->id)->first();
//            dd($zenon_monthly);
            $result_1        = $this->s->uploadToDatabase($zenon_monthly, $csv_file_object, 201009);
        } catch (\Exception $exc) {
            echo $exc->getMessage();
        } finally {
            \DB::connection('mysql_suisin')->rollback();
        }
//        dd($result_1);
        $this->assertEquals('テーブル設定が取り込まれていないようです。MySQL側 全オンテーブル設定から取込処理を行ってください。', $result_1['reason']);
    }

    /**
     * @test
     */
    public function 正常系_Database反映時にテーブルオブジェクトが生成できない() {
        $monthly_status = [
            'csv_file_name'   => 'K_D_902_M0332_20101001.csv',
            'monthly_id'      => 201009,
            'csv_file_set_on' => '2010-10-01',
        ];

        $zenon_data_csv_files = [
            'database_name'   => 'zenon_data_db',
            'table_name'      => 'not_exist_table_name',
            'zenon_format_id' => 99999,
        ];

        $zenon_table_configs = [
            'zenon_format_id' => 99999,
            'column_name'     => 'sample_column',
        ];



        try {
            \DB::connection('mysql_suisin')->beginTransaction();
            \App\ZenonCsv::insert($zenon_data_csv_files);
            \App\ZenonTable::insert($zenon_table_configs);
            $zenon_csv = \App\ZenonCsv::where('table_name', '=', 'not_exist_table_name')->first();
//            $zenon_table = \App\ZenonTable::where('zenon_format_id', '=', 99999)->first();

            $monthly_status['zenon_data_csv_file_id'] = $zenon_csv->id;
            \App\ZenonMonthlyStatus::insert($monthly_status);

            $csv_file_object = $this->s->setCsvFileObject(storage_path() . '/tests/K_D_902_M0332_20101001.csv')->getCsvFileObject();
            $zenon_monthly   = \App\ZenonMonthlyStatus::where('zenon_data_csv_file_id', '=', $zenon_csv->id)
                    ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                    ->select(\DB::raw('*, zenon_data_monthly_process_status.id AS id'))
                    ->first()
            ;
//            var_dump($zenon_monthly);
            $result_1        = $this->s->uploadToDatabase($zenon_monthly, $csv_file_object, 201009);
        } catch (\Exception $exc) {
            $result_1 = ['reason' => ''];
            echo $exc->getMessage();
//            echo $exc->getTraceAsString();
        } finally {
            \DB::connection('mysql_suisin')->rollback();
        }
//        dd($result_1);
        $this->assertEquals("SQLSTATE[42S02]: Base table or view not found: 1146 Table 'zenon_data_db.not_exist_table_name' doesn't exist (SQL: select * from `not_exist_table_name`)", $result_1['reason']);
    }

}
