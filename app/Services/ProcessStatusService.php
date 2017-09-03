<?php

namespace App\Services;

class ProcessStatusService
{

    protected $rows;
    protected $parameters;

    public function setRows($id) {
        $rows       = \DB::connection('mysql_suisin')
                ->table('zenon_data_process_status')
                ->join('zenon_data_csv_files', 'zenon_data_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->join('zenon_data_types', 'zenon_data_csv_files.zenon_data_type_id', '=', 'zenon_data_types.id')
                ->where('zenon_data_process_status.monthly_id', '=', $id)
                ->orderBy('is_exist', 'desc')
                ->orderBy('is_import', 'desc')
                ->orderBy('zenon_data_csv_files.id', 'asc')
        ;
        $this->rows = $rows;
        return $this;
    }
    
    public function getCount(){
        return $this->rows->count();
    }

    public function getRows($pages) {
        return $this->rows->paginate($pages);
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
                $rows = $rows->where('is_daily', '=', (int) true);
            }
            elseif ($input[$key] === 'monthly')
            {
                $rows = $rows->where('is_monthly', '=', (int) true);
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
