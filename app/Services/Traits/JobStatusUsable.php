<?php

namespace App\Services\Traits;

trait JobStatusUsable
{

    protected $monthly_status;
    protected $job_status;

    /**
     * 文字を切り落とすメソッド。
     * @param string $string : カットする対象の文字列。250文字で切り落とす。
     * @return string        : カット後の文字列。
     */
    private function cutStringTo250length(string $string): string {
        return mb_substr($string, 0, 250);
    }

    /**
     * zenon_data_monthly_process_status（モデル）をセットするメソッド。
     * @param type $id    : モデルID。
     * @return $this      : チェーンメソッド。
     * @throws \Exception : モデルが取得できなかった場合、エラー。
     */
    public function setMonthlyStatus($id) {
        $monthly_status = \App\ZenonMonthlyStatus::find($id);
        if (empty($monthly_status->id))
        {
            throw new \Exception("オブジェクトが取得できませんでした。（ID：{$id}）");
        }
        $this->monthly_status = $monthly_status;
        return $this;
    }

    public function getMonthlyStatus() {
        return $this->monthly_status;
    }

    public function setPreStartToMonthlyStatus($id) {
        $this->setMonthlyStatus($id);
        $this->monthly_status->is_pre_process_start = true;
        $this->monthly_status->save();
    }

    public function setPreEndAndRowCountToMonthlyStatus($id, $row_count) {
        $this->setMonthlyStatus($id);
        $this->monthly_status->is_pre_process_end = true;
        $this->monthly_status->row_count          = $row_count;
        $this->monthly_status->save();
    }

    public function setPostStartToMonthlyStatus($id) {
        $this->setMonthlyStatus($id);
        $this->monthly_status->is_post_process_start = true;
        $this->monthly_status->process_started_at    = date('Y-m-d H:i:s');
        $this->monthly_status->save();
    }

    public function setPostEndToMonthlyStatus($id) {
        $this->setMonthlyStatus($id);
        $this->monthly_status->is_import           = true;
        $this->monthly_status->is_post_process_end = true;
        $this->monthly_status->process_ended_at    = date('Y-m-d H:i:s');
        $this->monthly_status->save();
    }

    public function setExecutedRowCountToMonthlyStatus($id, $executed_row_count) {
        $this->setMonthlyStatus($id);
        $this->monthly_status->executed_row_count = $executed_row_count;
        $this->monthly_status->save();
    }

    public function setPreErrorToMonthlyStatus($id, $error_message) {
        $this->setMonthlyStatus($id);
        $this->monthly_status->is_pre_process_error = true;
        $this->monthly_status->error_message        = $this->cutStringTo250length($error_message);
        $this->monthly_status->save();
    }

    public function setPostErrorToMonthlyStatus($id, $error_message) {
        $this->setMonthlyStatus($id);
        $this->monthly_status->is_post_process_error = true;
        $this->monthly_status->error_message         = $this->cutStringTo250length($error_message);
        $this->monthly_status->save();
    }

//    public function getErrorMessages($job_id) {
//        $month_status = \App\ZenonMonthlyStatus::where('job_status_id', '=', $job_id)
//                ->where(function($query) {
//                    $query->orWhere('is_pre_process_error', '=', true)->orWhere('is_post_process_error', '=', true);
//                })
//                ->get(['error_message', 'csv_file_name',])
//                ->toArray()
//        ;
//        return $month_status;
//    }

    public function createJobStatus() {
        $job              = \App\JobStatus::create(['is_copy_start' => true]);
        $this->job_status = $job;
//        return $this;
        return $job;
    }

    public function setJobStatus($id) {
        $job = \App\JobStatus::find($id);
        if (empty($job->id))
        {
            throw new \Exception("不正なジョブIDが指定されました。");
        }
        $this->job_status = $job;
        return $this;
    }

    public function getJobStatus() {
        return $this->job_status;
    }

    public function setCopyEndToJobStatus($id) {
        $this->setJobStatus($id);
        $this->job_status->is_copy_end = true;
        $this->job_status->save();
    }

    public function setCopyErrorToJobStatus($id, $error_message) {
        $this->setJobStatus($id);
        $this->job_status->is_copy_error = true;
        $this->job_status->error_message = $this->cutStringTo250length($error_message);
        $this->job_status->save();
    }

    public function setImportStartToJobStatus($id) {
        $this->setJobStatus($id);
        $this->job_status->is_import_start = true;
        $this->job_status->save();
    }

    public function setImportEndToJobStatus($id) {
        $this->setJobStatus($id);
        $this->job_status->is_import_end = true;
        $this->job_status->save();
    }

    public function setImportErrorToJobStatus($id, $error_message) {
        $this->setJobStatus($id);
        $this->job_status->is_import_error = true;
        $this->job_status->error_message   = $this->cutStringTo250length($error_message);
        $this->job_status->save();
    }

    public function isErrorOccurred($id) {
        $this->setJobStatus($id);
        if ($this->job_status->is_copy_error || $this->job_status->is_import_error)
        {
            return true;
        }
        return false;
    }

    /**
     * 処理IDをセットするメソッド。
     * @param array $process_ids   : 処理対象のID（zenon_data_process_status_id）。
     * @param int   $job_status_id : セットするID（job_status_id）。
     * @return      $this          : チェーンメソッド。
     */
    public function setJobStatusIdToMonthlyStatus(array $process_ids, int $job_status_id) {
        $rows = \App\ZenonMonthlyStatus::where(function($query) use($process_ids) {
                    foreach ($process_ids as $p) {
                        $query->orWhere('id', '=', $p);
                    }
                })
                ->get()
        ;
        foreach ($rows as $r) {
            $r->job_status_id = $job_status_id;
            $r->save();
        }
        return $this;
    }

    public function resetJobStatus($job_status) {
        foreach ($job_status as $job) {
            $job->is_import = false;
        }
    }

}
