<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitJsonServiceTest extends TestCase
{

    protected $s;

    public function __construct() {
        $this->s = new \App\Services\JsonService();
//        $path = storage_path() . '/tests/';
    }

    /**
     * @test
     */
    public function 異常系_拡張子がjson以外() {
        $path       = storage_path() . '/tests/';
        $wrong_name = 'testfile.txt';
        try {
            $this->s->setFilePath($path, $wrong_name);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("拡張子が.json以外です。（ファイルパス：{$path}{$wrong_name}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 異常系_ファイルパスがディレクトリ() {
        $path       = storage_path() . '/tests/';
        $wrong_name = 'directory';
        try {
            $this->s->setFilePath($path, $wrong_name);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("指定されたパスはファイル形式ではありません。（ファイルパス：{$path}{$wrong_name}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_Jsonファイルの読み込み() {
        $path  = storage_path() . '/tests/';
        $name  = 'testfile.json';
        $array = $this->s->setFilePath($path, $name)->getJsonFile();
        $this->assertEquals($array['test'], 'pass!');
    }

    /**
     * @test
     */
    public function 異常系_Jsonファイル書き出し失敗() {
        $path  = storage_path() . '/tests/';
        $name  = 'testfile_failed.json';
        $array = ['data_1' => 'abc', 'data_2' => 'def'];

        try {
            $this->s->setFilePath($path, $name)->outputForJsonFile($array);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("Jsonファイル読み込み時にエラーが発生しました。（ファイルパス：{$path}{$name}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_Jsonファイル新規作成成功_一次元配列() {
        $path  = storage_path() . '/tests/';
        $name  = 'new_file.json';
        $array = [
            'data_1' => 'abc',
            'data_2' => 'def'
        ];

        $this->s->setFilePath($path, $name)->outputForJsonFile($array);
        $result = $this->s->getJsonFile($path);
        $this->assertEquals($result, $array);
        unlink($path . $name);
    }

    /**
     * @test
     */
    public function 正常系_Jsonファイル新規作成成功_多次元配列() {
        $path  = storage_path() . '/tests/';
        $name  = 'new_file.json';
        $array = [
            ['multi_data_1' => 'abc'],
            ['multi_data_2' => 'def'],
            ['multi_data_3' =>
                [
                    'data_3_1' => 'ghi',
                    'data_3_2' => 'jkl',
                ]
            ],
        ];

        $this->s->setFilePath($path, $name)->outputForJsonFile($array);
        $result = $this->s->getJsonFile($path);
        $this->assertEquals($result, $array);
        unlink($path . $name);
    }

    /**
     * @test
     */
    public function 正常系_Jsonファイル上書き成功() {
        $path     = storage_path() . '/tests/';
        $name     = 'new_file.json';
        $array_1  = [
            'data_1' => 'abc',
            'data_2' => 'def'
        ];
        $array_2  = [
            'data_3' => 'ghi',
            'data_4' => 'jkl'
        ];
        $expected = [
            'data_1' => 'abc',
            'data_2' => 'def',
            'data_3' => 'ghi',
            'data_4' => 'jkl',
        ];

        $this->s->setFilePath($path, $name)->outputForJsonFile($array_1);
        $this->s->setFilePath($path, $name)->outputForJsonFile($array_2);
        $result = $this->s->getJsonFile($path);
        $this->assertEquals($result, $expected);
        unlink($path . $name);
    }

}
