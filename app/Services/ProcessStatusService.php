<?php

namespace App\Services;

class ProcessStatusService
{

    protected $rows;
    protected $parameters;

    public function setRows($id) {
        $rows       = \App\ZenonMonthlyStatus::month($id)
//                \DB::connection('mysql_suisin')
//                ->table('zenon_data_monthly_process_status')
                ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->leftJoin('zenon_data_types', 'zenon_data_csv_files.zenon_data_type_id', '=', 'zenon_data_types.id')
                ->select(\DB::raw('*, zenon_data_monthly_process_status.updated_at AS process_updated_at'))
//                ->where('zenon_data_monthly_process_status.monthly_id', '=', $id)
//                ->orderBy('is_exist', 'desc')
//                ->orderBy('is_import', 'desc')
//                ->orderBy('zenon_data_csv_files.id', 'asc')
        ;
        $this->rows = $rows;
        return $this;
    }

    public function resetProcessStatus($rows, $monthly_id) {
        \App\ZenonMonthlyStatus::month($monthly_id)
//                ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->update(['is_execute' => (int) false])
        ;

        foreach ($rows as $r) {
            $r->is_execute            = true;
            $r->is_import             = false;
            $r->row_count             = 0;
            $r->executed_row_count    = 0;
            $r->is_pre_process_start  = false;
            $r->is_pre_process_end    = false;
            $r->is_post_process_start = false;
            $r->is_post_process_end   = false;
            $r->process_started_at    = null;
            $r->process_ended_at      = null;
            $r->save();
        }
    }

    public function getProcessRows($id, $processes = []) {
        if (!empty($processes))
        {
            $rows = \App\ZenonMonthlyStatus::month($id)
                    ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                    ->where(function($query) use ($processes) {
                foreach ($processes as $val) {
                    $query->orWhere('zenon_data_monthly_process_status.id', '=', $val);
                }
            })
            ;
        }
        else
        {
            $rows = \App\ZenonMonthlyStatus::month($id)
                    ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id');
        }
        return $rows;
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
