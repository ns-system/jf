<?php

namespace App\Services;

class ProcessStatusService
{

    use \App\Services\Traits\JobStatusUsable;

    protected $rows;
    protected $parameters;

    // 似たようなことでJoinしてる。臭い。
    public function setRows($id) {
        $rows       = \App\ZenonMonthlyStatus::month($id)
                ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->leftJoin('zenon_data_types', 'zenon_data_csv_files.zenon_data_type_id', '=', 'zenon_data_types.id')
                ->select(\DB::raw('*, zenon_data_monthly_process_status.updated_at AS process_updated_at'))
        ;
        $this->rows = $rows;
        return $this;
    }

    // 臭い。
    public function getProcessRows($job_status_id) {
        $rows = \App\ZenonMonthlyStatus::where('job_status_id', '=', $job_status_id)
                ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
        ;
        return $rows;
    }

    public function getNowJobStatusArray($job_status_id) {
        $job   = \App\JobStatus::find($job_status_id);
        $array = [
            'is_copy_start'   => $job->is_copy_start,
            'is_copy_end'     => $job->is_copy_end,
            'is_import_start' => $job->is_import_start,
            'is_import_end'   => $job->is_import_end,
            'is_copy_error'   => $job->is_copy_error,
            'is_import_error' => $job->is_import_error,
            'danger_message'  => $job->error_message,
        ];
        return $array;
    }

    public function getNowMonthlyStatusArray($job_status_id) {
        $rows = $this->getProcessRows($job_status_id)->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))->get();
//        $max_cnt = $rows->count();
//        $now_cnt = $this->service->getProcessRows($job_status_id)->where('is_post_process_end', '=', true)->count();

        $array = [];
        foreach ($rows as $r) {
            $s = (($r->process_started_at == '0000-00-00 00:00:00') ? '-' : date('G:i:s', strtotime($r->process_started_at)));
            $e = (($r->process_ended_at == '0000-00-00 00:00:00') ? '-' : date('G:i:s', strtotime($r->process_ended_at)));

            $array[$r->key_id] = [
                'key_id'                => $r->key_id,
                'is_pre_process_start'  => $r->is_pre_process_start,
                'is_pre_process_end'    => $r->is_pre_process_end,
                'is_pre_process_error'  => $r->is_pre_process_error,
                'is_post_process_start' => $r->is_post_process_start,
                'is_post_process_end'   => $r->is_post_process_end,
                'is_post_process_error' => $r->is_post_process_error,
                'is_execute'            => $r->is_execute,
                'is_import'             => $r->is_import,
                'process_started_at'    => $s,
                'process_ended_at'      => $e,
                'row_count'             => $r->row_count,
                'executed_row_count'    => $r->executed_row_count,
                'warning_message'       => $r->error_message,
            ];
        }
        return $array;
    }

    // そもそもロールバックうまく行ってたらこんなことする必要なくね？臭い。
    public function resetProcessStatus($process_ids) {
        $rows = \App\ZenonMonthlyStatus::where(function ($query) use ($process_ids) {
                    foreach ($process_ids as $key) {
                        $query->orWhere('id', '=', $key);
                    }
                })
                ->get()
        ;

        foreach ($rows as $r) {
            $r->is_execute            = true;
            $r->is_import             = false;
            $r->row_count             = 0;
            $r->executed_row_count    = 0;
            $r->is_pre_process_start  = false;
            $r->is_pre_process_end    = false;
            $r->is_pre_process_error  = false;
            $r->is_post_process_start = false;
            $r->is_post_process_end   = false;
            $r->is_post_process_error = false;
            $r->process_started_at    = null;
            $r->process_ended_at      = null;
            $r->error_message         = '';
            $r->save();
        }
    }

    public function getCount() {
        return $this->rows->count();
    }

    public function getRowPages($pages) {
        return $this->rows->paginate($pages);
    }

    public function getRows() {
        return $this->rows;
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function where($input) {
        $params = [];
        $key    = 'cycle';
        $rows   = $this->rows;
        if (isset($input[$key]))
        {
            $params[$key] = $input[$key];
            if ($input[$key] === 'daily')
            {
                $rows = $rows->where('cycle', '=', 'D');
            }
            elseif ($input[$key] === 'monthly')
            {
                $rows = $rows->where('cycle', '=', 'M');
            }
        }

        $key = 'exist';
        if (isset($input[$key]) && $input[$key] !== '')
        {
            $params[$key] = $input[$key];
            $rows         = $rows->where('is_' . $key, '=', (int) $input[$key]);
        }

        $key = 'import';
        if (isset($input[$key]) && $input[$key] !== '')
        {
            $params[$key] = $input[$key];
            $rows         = $rows->where('is_' . $key, '=', (int) $input[$key]);
        }

        $key = 'cumulative';
        if (isset($input[$key]) && $input[$key] !== '')
        {
            $params[$key] = $input[$key];
            $rows         = $rows->where('is_' . $key, '=', (int) $input[$key]);
        }

        $key = 'split';
        if (isset($input[$key]) && $input[$key] !== '')
        {
            $params[$key] = $input[$key];
            $rows         = $rows->where('is_' . $key, '=', (int) $input[$key]);
        }

        $key = 'process';
        if (isset($input[$key]) && $input[$key] !== '')
        {
            $params[$key] = $input[$key];
            $rows         = $rows->where('is_' . $key, '=', (int) $input[$key]);
        }
        $key = 'table';
        if (isset($input[$key]) && $input[$key] != null)
        {
            $params[$key] = $input[$key];
            $rows         = $rows->where('table_name', 'LIKE', "%{$input[$key]}%");
        }
        $key = 'file';
        if (isset($input[$key]) && $input[$key] != null)
        {
            $params[$key] = $input[$key];
            $rows         = $rows->where('zenon_data_name', 'LIKE', "%{$input[$key]}%");
        }
        $this->parameters = $params;
        $this->rows       = $rows;
        return $this;
    }

}
