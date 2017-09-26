<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\Traits\FileUploadTestable;

class UnitFileUploadTestableTest extends TestCase
{

    use FileUploadTestable;

    protected $s;

    public function __construct() {
        $this->s = $this->getMockForTrait(\App\Services\Traits\FileUploadTestable::class);
    }

    /**
     * @tests
     */
    public function 正常系_パラメーターにトークンを付与() {
        $res_1 = $this->s->getInputs(['name' => 'test user', 'age' => 20]);
        $this->assertEquals(count($res_1), 3);
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
        } catch (Exception $exc) {
            $this->assertEquals("ファイルパス（" . storage_path() . "/not_exist_path/testfile.csv）が見つかりません。", $exc->getMessage());
        }
    }

}
