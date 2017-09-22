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
            'account' => 'account_number',
            'subject' => 'subject_code',
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
    }

    /**
     * @test
     */
    public function 正常系_口座番号変換成功() {
        $param = [
            'account' => 'account_number',
            'subject' => 'subject_code',
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

}
