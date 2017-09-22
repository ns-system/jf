<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitDataUsableTest extends TestCase
{

    protected $s;

    public function __construct() {
        $this->s = $this->getMockForTrait(\App\Services\Traits\DateUsable::class);
    }

    private function setReflection($function_name) {
        $s = new \ReflectionMethod($this->s, $function_name);
        $s->setAccessible(true);
        return $s;
    }

    /**
     * @test
     */
    public function 異常系_日付セットエラー_ハイフンが入っていた場合() {
        $date_1 = '2017-09-09-01';
        $date_2 = 'This test is not date.';
        $date_3 = 'This test is-not date.';
        $s      = $this->setReflection('setYearMonthDateIncludeHyphoneOrSlash');
        try {
            $s->invoke($this->s, $date_1);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("想定外のエラーが発生しました。（引数：{$date_1}）", $e->getMessage());
        }
        try {
            $s->invoke($this->s, $date_2);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("文字列が日付型ではないようです。（引数：{$date_2}）", $e->getMessage());
        }
        try {
            $s->invoke($this->s, $date_3);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("文字列が数字ではないようです。（引数：{$date_3}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_日付セット_ハイフンが入っていた場合() {
        $date_1 = '2017-09-08';
        $date_2 = '2017/12/13';
        $s      = $this->setReflection('setYearMonthDateIncludeHyphoneOrSlash');

        $res_1 = $s->invoke($this->s, $date_1);
        $res_2 = $s->invoke($this->s, $date_2);
        $this->assertEquals($res_1, ['year' => 2017, 'month' => 9, 'day' => 8]);
        $this->assertEquals($res_2, ['year' => 2017, 'month' => 12, 'day' => 13]);
    }

    /**
     * @test
     */
    public function 正常系_日付セット_ハイフンが入っていない場合() {
        $date_1 = '20170908';
        $date_2 = '201712';
        $s      = $this->setReflection('setYearMonthDate');

        $res_1 = $s->invoke($this->s, $date_1);
        $res_2 = $s->invoke($this->s, $date_2);
        $this->assertEquals($res_1, ['year' => 2017, 'month' => 9, 'day' => 8]);
        $this->assertEquals($res_2, ['year' => 2017, 'month' => 12, 'day' => 1]);
    }

    /**
     * @test
     */
    public function 異常系_日付セット_ハイフンが入っていない場合() {
        $date_1 = '2017090888';
        $date_2 = 'This text is not date.';
        $s      = $this->setReflection('setYearMonthDate');
        try {
            $s->invoke($this->s, $date_1);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("文字数が日付型と一致しませんでした。（引数：{$date_1}）", $e->getMessage());
        }
        try {
            $s->invoke($this->s, $date_2);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("文字列が日付型ではないようです。（引数：{$date_2}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_日付である() {
        $date_1 = '20170908';
        $res_1  = $this->s->isDate($date_1);
        $this->assertTrue($res_1);
    }

    /**
     * @test
     */
    public function 正常系_日付でない() {
        $date_1 = '20170908898888';
        $date_2 = 'This is not date.';
        $res_1  = $this->s->isDate($date_1);
        $res_2  = $this->s->isDate($date_2);
        $this->assertFalse($res_1);
        $this->assertFalse($res_2);
    }

    /**
     * @test
     */
    public function 異常系_時刻型でない() {
        $date_1 = '12:13:14:15';
        $date_2 = 'is:not:time';
        $date_3 = 'This is not time.';

        $s = $this->setReflection('setHourTimeMinute');
        try {
            $s->invoke($this->s, $date_1);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("配列が多すぎます。（引数：{$date_1}）", $e->getMessage());
        }
        try {
            $s->invoke($this->s, $date_2);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("時刻型でないようです。（引数：{$date_2}）", $e->getMessage());
        }
        try {
            $s->invoke($this->s, $date_3);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("時刻型以外のものが指定されました。（引数：{$date_3}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_時刻型である() {
        $date_1 = '12:13:14';
        $date_2 = '12:13';
        $date_3 = null;

        $s     = $this->setReflection('setHourTimeMinute');
        $res_1 = $s->invoke($this->s, $date_1);
        $res_2 = $s->invoke($this->s, $date_2);
        $res_3 = $s->invoke($this->s, $date_3);

        $this->assertEquals($res_1, ['hour' => 12, 'min' => 13, 'sec' => 14,]);
        $this->assertEquals($res_2, ['hour' => 12, 'min' => 13, 'sec' => 0,]);
        $this->assertEquals($res_3, ['hour' => 0, 'min' => 0, 'sec' => 0,]);
    }

    /**
     * @test
     */
    public function 正常系_日付セット処理() {
        $date_1 = '2017-02-27 12:13:14';
        $date_2 = '2017-02-27';
        $date_3 = '2017/02/27';
        $date_4 = '20170227';
        $date_5 = '201702';
        $date_6 = null;
        $date_7 = '00000000';

        $res_1 = $this->s->setDate($date_1);
        $res_2 = $this->s->setDate($date_2);
        $res_3 = $this->s->setDate($date_3);
        $res_4 = $this->s->setDate($date_4);
        $res_5 = $this->s->setDate($date_5);
        $res_6 = $this->s->setDate($date_6);
        $res_7 = $this->s->setDate($date_7);

        $this->assertEquals($res_1->format('Y-m-d H:i:s'), '2017-02-27 12:13:14');
        $this->assertEquals($res_2->format('Y-m-d H:i:s'), '2017-02-27 00:00:00');
        $this->assertEquals($res_3->format('Y-m-d'), '2017-02-27');
        $this->assertEquals($res_4->format('Y-m-d'), '2017-02-27');
        $this->assertEquals($res_5->format('Y-m-d'), '2017-02-01');
        $this->assertEquals($res_6, null);
        $this->assertEquals($res_7, null);
    }

    /**
     * @test
     */
    public function 異常系_日付セット処理() {
        $date_1 = '2017-022-27 12:13:14';
        $date_2 = '20172';

        try {
            $res_1 = $this->s->setDate($date_1);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("日付型以外のものが指定されました。（引数：2017-022-27）", $e->getMessage());
        }
        try {
            $res_2 = $this->s->setDate($date_2);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("文字数が日付型と一致しませんでした。（引数：20172）", $e->getMessage());
        }
    }

}
