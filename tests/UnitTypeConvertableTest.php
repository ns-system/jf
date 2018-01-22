<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitTypeConvertableTest extends TestCase
{

    protected $s;

    public function __construct() {
        $this->s = $this->getMockForTrait(\App\Services\Traits\TypeConvertable::class);
//        $this->s = new \App\Services\JsonService();
//        $path = storage_path() . '/tests/';
    }

    /**
     * @test
     */
    public function 正常系_スペース変換成功() {
        $buf_1 = 'a aa         aa 　　　　  ';
        $buf_2 = ' 　　　　  ';

        $res_1 = $this->s->splitSpace($buf_1);
        $res_2 = $this->s->splitSpace($buf_2);
        $this->assertEquals('a aa         aa', $res_1);
        $this->assertEquals('', $res_2);
    }

    /**
     * @test
     */
    public function 正常系_数値型変換成功() {
        $buf_1 = '123456789';
        $buf_2 = '000012345';

        $res_1 = $this->s->convertType('integer', $buf_1);
        $res_2 = $this->s->convertType('bigInteger', $buf_2);
        $res_3 = $this->s->convertType('float', $buf_1);
        $res_4 = $this->s->convertType('double', $buf_2);
        $this->assertEquals(123456789, $res_1);
        $this->assertEquals(12345, $res_2);
        $this->assertEquals(123456789, $res_3);
        $this->assertEquals(12345, $res_4);
    }

    /**
     * @test
     */
    public function 正常系_日付型変換成功_頭4桁が西暦で月日にゼロが入っていた場合() {
        $buf_1 = '19250000';
        $buf_2 = '00001205';

        $res_1 = $this->s->convertType('date', $buf_1);
        $this->assertEquals('1925-01-01', $res_1);
        try {
            $this->s->convertType('date', $buf_2);
            $this->fail('エラーです。');
        } catch (Exception $e) {
            $this->assertEquals("値が日付型ではありません。（引数：'{$buf_2}'）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_日付型変換成功() {
        $buf_1 = '2017-07-25 12:24:35';
        $buf_2 = '2017-07-25';
        $buf_3 = '2017/07/25';
        $buf_4 = '20170725';
        $buf_5 = '00000000';
//        $buf_6 = '12:24:35';

        $res_1 = $this->s->convertType('date', $buf_1);
        $res_2 = $this->s->convertType('date', $buf_2);
        $res_3 = $this->s->convertType('date', $buf_3);
        $res_4 = $this->s->convertType('date', $buf_4);
        $res_5 = $this->s->convertType('date', $buf_5);

        $this->assertEquals($buf_2, $res_1);
        $this->assertEquals($buf_2, $res_2);
        $this->assertEquals($buf_2, $res_3);
        $this->assertEquals($buf_2, $res_4);
        $this->assertEquals(null, $res_5);
    }

    /**
     * @test
     */
    public function 正常系_日付時刻型変換成功() {
        $buf_1 = '2017-07-25 12:24:35';

        $res_1 = $this->s->convertType('dateTime', $buf_1);

        $this->assertEquals($buf_1, $res_1);
    }

    /**
     * @test
     */
    public function 正常系_配列型変換成功() {
        $array = ['12345', '20170606', 'test message', 'true',];
        $types = ['integer', 'date', 'string', 'boolean',];

        $res_1 = $this->s->convertTypes($types, $array);

        $this->assertEquals($res_1, [12345, '2017-06-06', 'test message', true,]);
    }

    /**
     * @test
     */
    public function 正常系_時刻型変換成功() {
        $buf_1 = '12:04:35';

        $res_1 = $this->s->convertType('time', $buf_1);

        $this->assertEquals($res_1, '12:4:35');
    }

    /**
     * @test
     */
    public function 正常系_論理値変換成功() {
        $buf_1 = 'true';
        $buf_2 = 'false';

        $res_1 = $this->s->convertType('boolean', $buf_1);
        $res_2 = $this->s->convertType('boolean', $buf_2);

        $this->assertTrue($res_1);
        $this->assertFalse($res_2);
    }

    /**
     * @test
     */
    public function 異常系_変換失敗() {
        $buf_1 = 'this text is not number or date.';

        try {
            $this->s->convertType('integer', $buf_1);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("値が数字型ではありません。（引数：'{$buf_1}'）", $e->getMessage());
        }
        try {
            $this->s->convertType('float', $buf_1);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("値が数字型ではありません。（引数：'{$buf_1}'）", $e->getMessage());
        }
        try {
            $this->s->convertType('date', $buf_1);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("値が日付型ではありません。（引数：'{$buf_1}'）", $e->getMessage());
        }
        try {
            $this->s->convertType('dateTime', $buf_1);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("値が日付時刻型ではありません。（引数：'{$buf_1}'）", $e->getMessage());
        }
    }

}
