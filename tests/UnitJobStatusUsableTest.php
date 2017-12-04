<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Services\Traits\JobStatusUsable;

class UnitJobStatusUsableTest extends TestCase
{

    use JobStatusUsable;

    protected $s;
    protected $monthly_status_id;

    public function setUp() {
        parent::setUp();
        \DB::connection('mysql_suisin')->beginTransaction();
        $monthly_status          = [
            'csv_file_name'          => 'K_D_902_M0332_20101001.csv',
            'file_kb_size'           => 338,
            'monthly_id'             => 201009,
            'csv_file_set_on'        => '2010-10-01',
            'zenon_data_csv_file_id' => 25,
            'is_execute'             => 1,
            'is_pre_process_start'   => 0,
            'is_pre_process_end'     => 0,
            'is_pre_process_error'   => 0,
            'is_post_process_start'  => 0,
            'is_post_process_end'    => 0,
            'is_post_process_error'  => 0,
            'is_exist'               => 1,
            'is_import'              => 0,
            'row_count'              => 0,
            'executed_row_count'     => 0,
        ];
        $this->monthly_status_id = \App\ZenonMonthlyStatus::insertGetId($monthly_status);
    }

    public function tearDown() {
        \DB::connection('mysql_suisin')->rollback();
    }

    public function __construct() {
        $this->s = $this->getMockForTrait(JobStatusUsable::class);
    }

    /**
     * @tests
     */
    public function 異常系_月次処理ステータスが取得できない() {
        try {
            $this->setMonthlyStatus(-1);
            $this->fail("予期しないエラーです。");
        } catch (\Exception $e) {
            $this->assertEquals("オブジェクトが取得できませんでした。（ID：-1）", $e->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系_is_pre_process_startにフラグセットできる() {
        $this->setPreStartToMonthlyStatus($this->monthly_status_id);
        $s = $this->getMonthlyStatus();
        $this->assertTrue($s->is_pre_process_start);
    }

    /**
     * @tests
     */
    public function 正常系_is_pre_process_endにフラグセットできる() {
        $this->setPreEndAndRowCountToMonthlyStatus($this->monthly_status_id, 200);
        $s = $this->getMonthlyStatus();
        $this->assertTrue($s->is_pre_process_end);
        $this->assertEquals(200, $s->row_count);
    }

    /**
     * @tests
     */
    public function 正常系_is_pre_process_errorにフラグ_エラーメッセージがセットできる() {
        $this->setPreErrorToMonthlyStatus($this->monthly_status_id, "致命的なエラー");
        $s = $this->getMonthlyStatus();
        $this->assertTrue($s->is_pre_process_error);
        $this->assertEquals("致命的なエラー", $s->error_message);
    }

    /**
     * @tests
     */
    public function 正常系_is_post_process_startにフラグセットできる() {
        $this->setPostStartToMonthlyStatus($this->monthly_status_id);
        $s = $this->getMonthlyStatus();
        $this->assertTrue($s->is_post_process_start);
    }

    /**
     * @tests
     */
    public function 正常系_is_post_process_endにフラグ_行数カウントがセットできる() {
        $this->setPostEndToMonthlyStatus($this->monthly_status_id);
        $s = $this->getMonthlyStatus();
        $this->assertTrue($s->is_post_process_end);
    }

    /**
     * @tests
     */
    public function 正常系_is_post_process_errorにフラグ_エラーメッセージがセットできる() {
        $this->setPostErrorToMonthlyStatus($this->monthly_status_id, "致命的なエラー");
        $s = $this->getMonthlyStatus();
        $this->assertTrue($s->is_post_process_error);
        $this->assertEquals("致命的なエラー", $s->error_message);
    }

//    /**
//     * @tests
//     */
//    public function 正常系_エラーメッセージを配列で取得する() {
//        $s_1                = $this->setMonthlyStatus($this->monthly_status_id)->getMonthlyStatus();
//        $s_1->job_status_id = 99;
//        $s_1->save();
//        $errors_1           = $this->getErrorMessages(99);
//
//        $this->setPostErrorToMonthlyStatus($this->monthly_status_id, "致命的なエラー1");
//        $this->setMonthlyStatus($this->monthly_status_id)->getMonthlyStatus();
//        $errors_2 = $this->getErrorMessages(99);
//
//        $this->assertEquals([], $errors_1);
//        $this->assertEquals([['error_message' => '致命的なエラー1', 'csv_file_name' => 'K_D_902_M0332_20101001.csv']], $errors_2);
//    }

    /**
     * @tests
     */
    public function 正常系_処理済み件数をセットできる() {
        $this->setExecutedRowCountToMonthlyStatus($this->monthly_status_id, 120);
        $s = $this->getMonthlyStatus();
        $this->assertEquals(120, $s->executed_row_count);
    }

    /**
     * @tests
     */
    public function 正常系_ジョブステータスを作成できる() {
        $s = $this->createJobStatus()/*->getJobStatus()*/;
        $this->assertEquals(true, $s->is_copy_start);
    }

    /**
     * @tests
     */
    public function 異常系_ジョブステータスが取得できない() {
        try {
            $this->setJobStatus(-1);
            $this->fail("予期しないエラーです。");
        } catch (\Exception $e) {
            $this->assertEquals("不正なジョブIDが指定されました。", $e->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系_is_copy_endにフラグセットできる() {
        $t = $this->createJobStatus()/*->getJobStatus()*/;
        $this->setCopyEndToJobStatus($t->id);
        // 一度再インスタンス化しないとフラグが変わらない（変わる前のインスタンスを見に行ってエラーになる）
        $s = $this->setJobStatus($t->id)->getJobStatus();
        $this->assertEquals(true, $s->is_copy_end);
    }

    /**
     * @tests
     */
    public function 正常系_is_import_startにフラグセットできる() {
        $t = $this->createJobStatus()/*->getJobStatus()*/;
        $this->setImportStartToJobStatus($t->id);
        $s = $this->setJobStatus($t->id)->getJobStatus();
        $this->assertEquals(true, $s->is_import_start);
    }

    /**
     * @tests
     */
    public function 正常系_is_import_endにフラグセットできる() {
        $t = $this->createJobStatus()/*->getJobStatus()*/;
        $this->setImportEndToJobStatus($t->id);
        $s = $this->setJobStatus($t->id)->getJobStatus();
        $this->assertEquals(true, $s->is_import_end);
    }

    /**
     * @tests
     */
    public function 正常系_copy_errorにフラグ_エラーメッセージがセットできる() {
        $t = $this->createJobStatus()/*->getJobStatus()*/;
        $this->setCopyErrorToJobStatus($t->id, "処理続行不可");
        // 一度再インスタンス化しないとフラグが変わらない（変わる前のインスタンスを見に行ってエラーになる）
        $s = $this->setJobStatus($t->id)->getJobStatus();
        $this->assertEquals(true, $s->is_copy_error);
        $this->assertEquals("処理続行不可", $s->error_message);
    }

    /**
     * @tests
     */
    public function 正常系_import_errorにフラグ_エラーメッセージがセットできる() {
        $t = $this->createJobStatus()/*->getJobStatus()*/;
        $this->setImportErrorToJobStatus($t->id, "処理続行不可");
        // 一度再インスタンス化しないとフラグが変わらない（変わる前のインスタンスを見に行ってエラーになる）
        $s = $this->setJobStatus($t->id)->getJobStatus();
        $this->assertEquals(true, $s->is_import_error);
        $this->assertEquals("処理続行不可", $s->error_message);
    }

    /**
     * @tests
     */
    public function 正常系_エラーが発生しているかどうか() {
        $t     = $this->createJobStatus()/*->getJobStatus()*/;
        $false = $this->isErrorOccurred($t->id);
        // 一度再インスタンス化しないとフラグが変わらない（変わる前のインスタンスを見に行ってエラーになる）
        $this->setImportErrorToJobStatus($t->id, "処理続行不可");
        $s     = $this->setJobStatus($t->id)->getJobStatus();
        $true  = $this->isErrorOccurred($s->id);

        $this->assertFalse($false);
        $this->assertTrue($true);
    }

    /**
     * @tests
     */
    public function 正常系_MonthlyStatusにJobStatusIdを反映させる() {
        $ids   = [$this->monthly_status_id];
        $this->setJobStatusIdToMonthlyStatus($ids, 999);
        $model = \App\ZenonMonthlyStatus::find($this->monthly_status_id);
        $this->assertEquals(999, $model->job_status_id);
    }

}
