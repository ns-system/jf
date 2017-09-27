<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Services\Traits\FileUploadTestable;

class UnitCsvUsableTest extends TestCase
{

    use FileUploadTestable;

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
            $this->assertEquals("行数：2行目のCSVのカラム数が一致しませんでした。（想定：20，実際：3）", $e->getMessage());
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
        $path_1 = storage_path() . '/tests/testfile.csv';
        $path_2 = storage_path() . '/tests/K_D_902_M0332_20170801.csv';
        $len_1  = $this->s
                ->setCsvFilePath($path_1)
                ->setCsvFileObject()
                ->checkCsvFileLength(3)
                ->getCsvLines()
        ;
        $len_2  = $this->s
                ->setCsvFilePath($path_2)
                ->setCsvFileObject()
                ->checkCsvFileLength(46)
                ->getCsvLines()
        ;
        $this->assertEquals($len_1, 1);
        $this->assertEquals($len_2, 1500);
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

    /**
     * @test
     */
    public function 異常系_Webから指定したCSVの形式が誤っている() {
        try {
            $file_object = $this->createUploadFile(storage_path() . '/tests', 'testfile.txt', 'text/csv');
            $this->s->setCsvFileObjectFromRequest($file_object, false, 3);
            $this->fail('例外発生なし');
        } catch (\Exception $exc) {
            $this->assertEquals("拡張子が違うようです。（ファイルパス：testfile.txt）", $exc->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_Web画面からCSVカラム名をファイルを取り込む() {
//        \Session::start();
////        var_dump(csrf_token());
//
////        var_dump($para);
//        $path     = storage_path() . '/tests/';
//        $name     = 'testfile.csv';
//        $full_path = $path.$name;
//        var_dump($full_path);
//
//        $tmp_file = new UploadedFile(
//                $full_path,
//                $name,
//                'text/csv',
//                filesize($full_path),
//                null,
//                true
//        );
//        var_dump($tmp_file);
////        $file     = ['test' => $tmp_file];
////        var_dump($file);
//        $para     = [
//            '_token' => csrf_token(),
//            'test'=>'test_user 1234567890',
////            'file'=>$tmp_file,
////            'file_name' => storage_path() . '/tests/testfile.csv',
//        ];
//        $response = $this->call(
//                'POST',
//                '/test/upload_csv',
//                $para,
//                [],
//                ['file'=>$tmp_file]
//        );
//        $response = $this->getResponse(storage_path() . '/tests', 'testfile.csv', 'text/csv');
//        \Session::start();

        $file_object = $this->createUploadFile(storage_path() . '/tests', 'testfile.csv', 'text/csv');
        $this->s->setCsvFileObjectFromRequest($file_object, false, 3);
//        var_dump($file_object);
//        $response    = $this->call('POST', 'test/file_upload', $this->getInputs(), [], ['file' => $file_object]);
//        var_dump($file_object->getClientOriginalExtension());
//        var_dump($response);
//        $this->s->setCsvFileObjectFromRequest();
    }

}
