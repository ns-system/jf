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
//        $this->param = [
//            'account' => 'account_number',
//            'subject' => 'subject_code',
//        ];
//        $this->rows  = [
//            'account_number' => '1',
//            'subject_code'   => '1',
//        ];
    }

    private function setReflection($function_name) {
        $s = new \ReflectionMethod($this->s, $function_name);
        $s->setAccessible(true);
        return $s;
    }

    /**
     * @test
     */
    public function 異常系_口座番号変換時エラー() {
        $param = [
            'account_column_name' => 'account_number',
            'subject_column_name' => 'subject_code',
        ];
        $rows  = [
            'account_number' => '1',
            'subject_code'   => '1',
        ];
        $s     = $this->setReflection('convertAccount');
        try {
            $s->invoke($this->s, $param, $rows);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("口座番号が短すぎるようです（科目：1， 口座番号：1）", $e->getMessage());
        }

        try {
            $this->s->setConvertedAccountToRow(true);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("口座分割設定値が指定されていません。", $e->getMessage());
        }

        try {
            $this->s->setConvertedAccountToRow(true, ['account_column_name' => '', 'subject_column_name' => '',]);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("指定された口座番号変換キーが不正です。", $e->getMessage());
        }
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
    public function 正常系_口座番号変換成功() {
        $param = [
            'account_column_name' => 'account_number',
            'subject_column_name' => 'subject_code',
        ];
        $s     = $this->setReflection('convertAccount');
        $res_1 = $s->invoke($this->s, $param, ['account_number' => '1234567890', 'subject_code' => '1',]);
        $res_2 = $s->invoke($this->s, $param, ['account_number' => '1234567890', 'subject_code' => '2',]);
        $res_3 = $s->invoke($this->s, $param, ['account_number' => '1234567890', 'subject_code' => '8',]);
        $res_4 = $s->invoke($this->s, $param, ['account_number' => '1234567890', 'subject_code' => '9',]);
        $res_5 = $s->invoke($this->s, $param, ['account_number' => '1234567890', 'subject_code' => '11',]);
        $res_6 = $s->invoke($this->s, $param, ['account_number' => '1234567890', 'subject_code' => '3',]);
        $this->assertEquals(1234567, $res_1);
        $this->assertEquals(1234567, $res_2);
        $this->assertEquals(1234567, $res_3);
        $this->assertEquals(1234567, $res_4);
        $this->assertEquals(1234567, $res_5);
        $this->assertEquals(1234567890, $res_6);
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
            ['1', '1234567890', 'key_1', 'test user_1', '20170701', 'true'],
            ['4', '2345678901', 'key_2', 'test user_2', '2017-08-21', 'false'],
        ];
        $timestamp   = date('Y-m-d H:i:s');
        $expection_1 = [
            ['subject_code' => 1, 'account_number' => 1234567890.0, 'split_key' => 'key_1', 'user_name' => 'test user_1', 'created_on' => '2017-07-01', 'is_administrator' => true, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 1234567, 'created_at' => $timestamp, 'updated_at' => $timestamp,],
            ['subject_code' => 4, 'account_number' => 2345678901.0, 'split_key' => 'key_2', 'user_name' => 'test user_2', 'created_on' => '2017-08-21', 'is_administrator' => false, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 2345678901, 'created_at' => $timestamp, 'updated_at' => $timestamp,],
        ];
        $types       = ['integer', 'double', 'string', 'string', 'date', 'boolean'];
        $keys        = ['subject_code', 'account_number', 'split_key', 'user_name', 'created_on', 'is_administrator'];

        $res_1 = [];
        foreach ($rows as $r) {
            $res_1[] = $this->s->setRow($r)
                    ->convertRow($types, true)
                    ->setKeyToRow($keys)
                    ->setMonthlyIdToRow(true, 201707)
                    ->setConvertedAccountToRow(true, ['account_column_name' => 'account_number', 'subject_column_name' => 'subject_code'])
                    ->splitRow(false)
                    ->setTimeStamp()
                    ->setTimeStamp($timestamp)
                    ->getRow()
            ;
        }
//        var_dump($res_1);
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
//            ['subject_code' => 1, 'account_number' => 1234567890.0, 'split_key' => 'key_1', 'user_name' => 'test user_1', 'created_on' => '2017-07-01', 'is_administrator' => true, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 1234567, 'created_at' => $timestamp, 'updated_at' => $timestamp,],
//            ['subject_code' => 4, 'account_number' => 2345678901.0, 'split_key' => 'key_2', 'user_name' => 'test user_2', 'created_on' => '2017-08-21', 'is_administrator' => false, 'id' => null, 'monthly_id' => 201707, 'key_account_number' => 2345678901, 'created_at' => $timestamp, 'updated_at' => $timestamp,],
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
//        var_dump($res_1);
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
//        $timestamp   = date('Y-m-d H:i:s');
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
//        var_dump($res_1);
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

}
