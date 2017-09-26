<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitCsvUsableTest extends TestCase
{

    protected $s;

    public function __construct() {
        $this->s = $this->getMockForTrait(\App\Services\Traits\CsvUsable::class);
    }

    /**
     * @test
     */
    public function 異常系_ファイルパスが存在しない() {
        $path = storage_path() . '/tests/not_exist_file.csv';
        try {
            $this->s->setCsvFilePath($path);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("ファイルが存在しないようです。（ファイルパス：{$path}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 異常系_拡張子エラー() {
        $path = storage_path() . '/tests/testfile.txt';
        try {
            $this->s->setCsvFilePath($path);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("CSVファイル以外が選択されました。（ファイルパス：{$path}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_CSVファイルオブジェクト取得() {
        $path  = storage_path() . '/tests/testfile.csv';
        $obj_1 = $this->s
                ->setCsvFilePath($path)
                ->setCsvFileObject()
                ->getCsvFileObject()
        ;
        $obj_2 = $this->s
                ->setCsvFileObject($path)
                ->getCsvFileObject()
        ;
        $obj_3 = $this->s->getCsvFileObject($path);
        $this->assertInstanceOf('SplFileObject', $obj_1);
        $this->assertInstanceOf('SplFileObject', $obj_2);
        $this->assertInstanceOf('SplFileObject', $obj_3);
    }

    /**
     * @test
     */
    public function 異常系_CSVファイル配列数が違う() {
        $path = storage_path() . '/tests/testfile.csv';
        try {
            $this->s
                    ->setCsvFilePath($path)
                    ->setCsvFileObject()
                    ->checkCsvFileLength(20)
            ;
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("行数：1行目のCSVのカラム数が一致しませんでした。（想定：20，実際：3）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 異常系_ファイル配列チェック時にパス未指定() {
        try {
            $this->s->checkCsvFileLength(20);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("CSVファイルが指定されていないようです。", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_エンコード変換() {
        $buf    = mb_convert_encoding('あああ,1234,nnn', 'SJIS-WIN', 'UTF-8');
        $result = $this->s->lineEncode($buf);
        $this->assertEquals('あああ,1234,nnn', $result);
    }

    /**
     * @test
     */
    public function 正常系_ファイル列数カウント() {
        $path = storage_path() . '/tests/testfile.csv';
        $len  = $this->s
                ->setCsvFilePath($path)
                ->setCsvFileObject()
                ->checkCsvFileLength(3)
                ->getCsvLines()
        ;
        $this->assertEquals($len, 1);
    }

    /**
     * @test
     */
    public function 正常系_ファイル名取得() {
        $path = storage_path() . '/tests/testfile.csv';
        $name = $this->s
                ->setCsvFilePath($path)
                ->getFileName()
        ;
        $this->assertEquals($name, 'testfile.csv');
    }

    /**
     * @test
     */
    public function 正常系_CSVファイル出力成功() {
//        $path = storage_path() . '/tests/testfile.csv';
        $file_name = 'export.csv';
        $array     = [['data1', 1000, '山田']];
        $result    = $this->s->exportCsv($array, $file_name);
        $this->assertEquals(200, $result->status());
    }

    /**
     * @test
     */
    public function 正常系_配列判定() {
        $array = null;
        $res_1 = $this->s->isArrayEmpty($array);
        $this->assertTrue($res_1);
        $res_2 = $this->s->isArrayEmpty([null]);
        $this->assertTrue($res_2);
        $res_3 = $this->s->isArrayEmpty([[[null]]]);
        $this->assertTrue($res_3);
        $res_4 = $this->s->isArrayEmpty([null, 1, 2, 3]);
        $this->assertFalse($res_4);
        $res_5 = $this->s->isArrayEmpty([null, [null, null, [null]]]);
        $this->assertTrue($res_5);
    }

}
