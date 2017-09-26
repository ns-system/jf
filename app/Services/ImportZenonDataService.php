<?php

namespace App\Services;

use \App\Services\Traits\JsonUsable;
use \App\Services\Traits\CsvUsable;
use \App\Services\Traits\TypeConvertable;

class ImportZenonDataService
{

    use JsonUsable,
        CsvUsable,
        TypeConvertable;

    protected $row;
    protected $status;

    public function getRow() {
        return $this->row;
    }

    public function setRow($row) {
        $this->row = $row;
        return $this;
    }

    public function setTimeStamp($timestamp = null) {
        if (empty($timestamp))
        {
            $timestamp = date('Y-m-d H:i:s');
        }
        $this->row['created_at'] = $timestamp;
        $this->row['updated_at'] = $timestamp;
        return $this;
    }

    public function convertRow($types, $is_ceil = true) {
        $row       = $this->convertTypes($types, $this->row, $is_ceil);
        $this->row = $row;
        return $this;
    }

    public function setKeyToRow($keys) {
        $row       = array_combine($keys, $this->row);
        $row['id'] = null;
        $this->row = $row;
        return $this;
    }

    public function setMonthlyIdToRow($is_cumulative, $monthly_id = null) {
        if (!$is_cumulative)
        {
            return $this;
        }
        if (empty($monthly_id))
        {
            throw new \Exception('月別IDが指定されていません。');
        }
        $this->row['monthly_id'] = $monthly_id;
        return $this;
    }

    public function setConvertedAccountToRow($is_account_convert, $account_convert_param = null) {
        if (!$is_account_convert)
        {
            return $this;
        }
        if (empty($account_convert_param))
        {
            throw new \Exception('口座分割設定値が指定されていません。');
        }
        if (empty($account_convert_param['account_column_name']) || empty($account_convert_param['subject_column_name']))
        {
            throw new \Exception("指定された口座番号変換キーが不正です。");
        }
        $account_key = $account_convert_param['account_column_name'];
        $subject_key = $account_convert_param['subject_column_name'];

        if (empty($this->row[$account_key]) || empty($this->row[$subject_key]))
        {
            throw new \Exception("口座変換データが不正です。");
        }

        $account_number = $this->row[$account_key];
        $subject_code   = $this->row[$subject_key];
        $key_account    = null;
        switch ($subject_code) {
            case 1:
            case 2:
            case 8:
            case 9:
            case 11:
                if (mb_strlen($account_number) <= 3)
                {
                    throw new \Exception("口座番号が短すぎるようです。（科目：{$subject_code}， 口座番号：{$account_number}）");
                }
//                return mb_substr($buf, 0, /* mb_strlen($row[$sb_key]) - 3 */ -3);
                $key_account = mb_substr($account_number, 0, -3);

                break;
            default:
                $key_account = $account_number;
                break;
        }

        $this->row['key_account_number'] = (double) $key_account;
        return $this;
    }

    public function splitRow($is_split, $pos_first = 0, $pos_last = 0, $split_key_configs = null) {
        if (!$is_split)
        {
            return $this;
        }

        if (!isset($pos_first) || $pos_first < 0)
        {
            throw new \Exception("配列切り落としの開始位置が誤っているようです。");
        }
        if (!isset($pos_last) || $pos_last < 0)
        {
            throw new \Exception("配列切り落としの終了位置が誤っているようです。");
        }
        if ($pos_first >= $pos_last)
        {
            throw new \Exception("配列切り落としの指定は開始位置 < 終了位置となるように指定してください。");
        }
        $slice_row = array_slice($this->row, $pos_first, $pos_last, true);
        for ($i = 1; $i <= 4; $i++) {
            $k = 'split_foreign_key_' . $i;
            if (!empty($split_key_configs[$k]))
            {
                $fk             = $split_key_configs[$k];
                $slice_row[$fk] = $this->row[$fk];
            }
        }
        $this->row = $slice_row;
        return $this;
    }

    public function monthlyStatus($ym, $processes) {
        $rows = \App\ZenonMonthlyStatus::month($ym)
                ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->where(function($query) use($processes) {
                    foreach ($processes as $id) {
                        $query->orWhere('zenon_data_monthly_process_status.id', '=', $id);
                    }
                })
                ->orderBy('zenon_format_id', 'asc')
        ;

        return $rows;
    }

    public function uploadToDatabase($table_config, $csv_file_object, $monthly_id) {
        $bulk    = [];
        $types   = [];
        $keys    = [];
        $configs = \App\ZenonTable::format($table_config->zenon_format_id)->select(['column_name', 'column_type',])->get();
        foreach ($configs as $c) {
            $types[$c->column_name] = $c->column_type;
            $keys[]                 = $c->column_name;
        }
//        var_dump($types);
//        var_dump($keys);

        $split_key_configs = [
            'split_foreign_key_1' => $table_config->split_foreign_key_1,
            'split_foreign_key_2' => $table_config->split_foreign_key_2,
            'split_foreign_key_3' => $table_config->split_foreign_key_3,
            'split_foreign_key_4' => $table_config->split_foreign_key_4,
        ];

        $account_convert_param = [
            'account_column_name' => $table_config->account_column_name,
            'subject_column_name' => $table_config->subject_column_name,
        ];

        $table = $this->getTableObject('mysql_zenon', $table_config->table_name);
        $this->setPreProcessStartToMonthlyStatus($table_config);
//        var_dump($table_config->table_name);

        $line_number = 0;
        foreach ($csv_file_object as /* $line_number => */ $raw_line) {
            $line = $this->lineEncode($raw_line);
            if ($this->isArrayEmpty($line))
            {
                var_dump($raw_line);
//                $line_number--;
                continue;
            }
            $line_number++;
            $tmp_bulk = $this->setRow($line)
                    ->setKeyToRow($keys)
                    ->convertRow($types, true)
                    ->splitRow($table_config->is_split, $table_config->first_column_position, $table_config->last_column_position, $split_key_configs)
                    ->setMonthlyIdToRow(true, $monthly_id)
                    ->setConvertedAccountToRow($table_config->is_account_convert, $account_convert_param)
                    ->setTimeStamp(date('Y-m-d H:i:s'))
                    ->getRow()
            ;
            // MySQLのバージョンによってはプリペアドステートメントが65536までに制限されているため、動的にしきい値を設ける
            if ($line_number > 0 && (count($bulk) * count($tmp_bulk) + count($tmp_bulk)) > 65000)
            {
                $table->insert($bulk);
                $this->setExecutedRowCountToMonthlyStatus($table_config, $line_number);
                $bulk = null;
            }
            $bulk[] = $tmp_bulk;
        }
        // 端数分をここでINSERT
        if (count($bulk) !== 0)
        {
            $table->insert($bulk);
            $this->setExecutedRowCountToMonthlyStatus($table_config, $line_number);
        }
        $this->setPostProcessEndToMonthlyStatus($table_config);
    }

    private function getTableObject($connection, $table_name) {
        if (empty($table_name) || empty($connection))
        {
            throw new \Exception("コネクションもしくはテーブル名が指定されていないようです。");
        }
        return \DB::connection($connection)->table($table_name);
    }

    public function getLastTraded($reference_last_traded_on, $last_traded_on) {
//        $date = $row->reference_last_traded_on;
        if (empty($reference_last_traded_on) || $reference_last_traded_on === '0000-00-00' || $reference_last_traded_on === '00000000')
        {
            return $last_traded_on;
        }
        return $reference_last_traded_on;
    }

    public function setPreProcessStartToMonthlyStatus($monthly_status_object) {
        $monthly_status_object->is_pre_process_start = true;
        $monthly_status_object->save();
    }

    public function setPreProcessEndToMonthlyStatus($monthly_status_object, $row_count) {
        $monthly_status_object->is_pre_process_end = true;
        $monthly_status_object->row_count          = $row_count;
        $monthly_status_object->save();
    }

    public function setPostProcessStartToMonthlyStatus($monthly_status_object) {
        $monthly_status_object->is_post_process_start = true;
        $monthly_status_object->process_started_at    = date('Y-m-d H:i:s');
        $monthly_status_object->save();
    }

    public function setPostProcessEndToMonthlyStatus($monthly_status_object) {
        $monthly_status_object->is_import           = true;
        $monthly_status_object->is_post_process_end = true;
        $monthly_status_object->process_ended_at    = date('Y-m-d H:i:s');
        $monthly_status_object->save();
    }

    public function setExecutedRowCountToMonthlyStatus($monthly_status_object, $executed_row_count) {
        $monthly_status_object->executed_row_count = $executed_row_count;
        $monthly_status_object->save();
    }

}
