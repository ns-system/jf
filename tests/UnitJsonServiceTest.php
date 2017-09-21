<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitJsonServiceTest extends TestCase
{

    protected $s;

    public function __construct() {
        $this->s = new \App\Services\JsonService();
    }

    /**
     * @test
     */
    public function 異常系_ファイルパスが存在しない() {
        $wrong_path = storage_path() . '/tests/notexist.json';
        try {
            $this->s->setFilePath($wrong_path);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("ファイルパスが存在しません。（ファイルパス：{$wrong_path}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 異常系_拡張子がjson以外() {
        $wrong_path = storage_path() . '/tests/testfile.txt';
        try {
            $this->s->setFilePath($wrong_path);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("拡張子が.json以外です。（ファイルパス：{$wrong_path}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 異常系_ファイルパスがディレクトリ() {
        $wrong_path = storage_path() . '/tests';
        try {
            $this->s->setFilePath($wrong_path);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("指定されたパスはファイル形式ではありません。（ファイルパス：{$wrong_path}）", $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function 正常系_Jsonファイルの読み込み() {
        $path  = storage_path() . '/tests/testfile.json';
        $array = $this->s->setFilePath($path)->getJsonFile();
        $this->assertEquals($array['test'], 'pass!');
    }

    /**
     * @test
     */
    public function 異常系_Jsonファイル書き出し失敗() {
        $path = storage_path() . '/tests/testfile_failed.json';
        try {
            $this->s->setFilePath($path)->outputForJsonFile(['a', 'b', 'c']);
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("Jsonファイル読み込み時にエラーが発生しました。（ファイルパス：{$path}）", $e->getMessage());
        }
    }

}
