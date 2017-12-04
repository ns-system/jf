<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\Traits\Testing\FileTestable;

class UnitFileUploadTestableTest extends TestCase
{

    use FileTestable;

    protected $s;

    public function __construct() {
        $this->s = $this->getMockForTrait(FileTestable::class);
    }

    /**
     * @tests
     */
    public function 正常系_アップロード済みファイルを作成() {
        $res_1 = $this->s->createUploadFile(storage_path() . '/tests', 'testfile.csv', 'text/csv');
        $this->assertEquals(get_class($res_1), 'Symfony\Component\HttpFoundation\File\UploadedFile');
    }

    /**
     * @tests
     */
    public function 異常系_ファイルパスが存在しない() {
        try {
            $this->s->createUploadFile(storage_path() . '/not_exist_path', 'testfile.csv', 'text/csv');
            $this->fail("予期しないエラーです。");
        } catch (Exception $exc) {
            $this->assertEquals("ファイルパス（" . storage_path() . "/not_exist_path/testfile.csv）が見つかりません。", $exc->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系_空の偽装CSVファイルが生成できる() {
        $path       = storage_path() . '/tests/';
        $empty_file = 'empty_file.csv';
        $this->s->createCsvFile($empty_file);
        $this->assertTrue(file_exists($path . $empty_file));
        $this->assertEquals(0, filesize($path . $empty_file));
    }

    /**
     * @tests
     */
    public function 正常系_中身のある偽装CSVファイルが生成できる() {
        $path       = storage_path() . '/tests/';
        $empty_file = 'not_empty_file.csv';
        $csv_datas  = [
            ['aaa', 'bbb', 'ccc'],
            ['あああ', 'いいい', 'ううう',],
        ];
        $this->s->createCsvFile($empty_file, $csv_datas);
        $this->assertTrue(file_exists($path . $empty_file));
        $this->assertThat(filesize($path . $empty_file), $this->greaterThan(0));
    }

}
