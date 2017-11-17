<?php

namespace App\Services;

use \App\Services\Traits\JsonUsable;
use \App\Services\Traits\CsvUsable;
use \App\Services\Traits\TypeConvertable;
use \App\Services\Traits\JobStatusUsable;
use \App\Services\Traits\MemoryCheckable;

class ImportZenonDataService
{

    use JsonUsable,
        CsvUsable,
        TypeConvertable,
        JobStatusUsable,
        MemoryCheckable;

    protected $row;
    protected $status;

    public function getRow() {
        return $this->row;
    }

    public function setRow($row) {
        $this->row = $row;
        return $this;
    }

    private function setTimeStamp($option_timestamp = null) {
//        if (empty($timestamp))
//        {
//            $timestamp = date('Y-m-d H:i:s');
//        }
        $timestamp               = (!empty($option_timestamp)) ? $option_timestamp : date('Y-m-d H:i:s');
        $this->row['created_at'] = $timestamp;
        $this->row['updated_at'] = $timestamp;
        return $this;
    }

    private function convertRow($types, $is_ceil = true) {
        $row       = $this->convertTypes($types, $this->row, $is_ceil);
        $this->row = $row;
        return $this;
    }

    private function setKeyToRow($keys) {
        if (count($keys) !== count($this->row))
        {
            throw new \Exception("配列長が一致しませんでした。（想定：" . count($keys) . " 実際：" . count($this->row) . "）");
        }
        $row       = array_combine($keys, $this->row);
        $row['id'] = null;
        $this->row = $row;
        return $this;
    }

    private function setMonthlyIdToRow($is_cumulative, $monthly_id = null) {
        if (!$is_cumulative)
        {
            return $this;
        }
        if (empty($monthly_id))
        {
            throw new \Exception('月別IDが指定されていません。');
        }
        if (!$this->isDate($monthly_id))
        {
            throw new \Exception("月別IDの指定が不正です。（指定：{$monthly_id}）");
        }
        $date_obj                = $this->setDate($monthly_id)->getDate();
        $this->row['monthly_id'] = $date_obj->format('Ym');
        return $this;
    }

    private function setConvertedAccountToRow($is_account_convert, $account_convert_param = null) {
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
                $key_account = mb_substr($account_number, 0, -3);

                break;
            default:
                $key_account = $account_number;
                break;
        }

        $this->row['key_account_number'] = (double) $key_account;
        return $this;
    }

    private function splitRow($is_split, $pos_first = 0, $pos_last = 0, $split_key_configs = null) {
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

    public function monthlyStatus($ym, $process_ids) {
        $rows = \App\ZenonMonthlyStatus::month($ym)
                ->join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->where(function($query) use($process_ids) {
                    foreach ($process_ids as $id) {
                        $query->orWhere('zenon_data_monthly_process_status.id', '=', $id);
                    }
                })
                ->orderBy('zenon_format_id', 'asc')
        ;

        return $rows;
    }

    private function makeErrorLog($table_config, $error_message) {
        return
                [
                    'timestamp'       => date('Y-m-d H:i:s'),
                    'csv_file_name'   => $table_config->csv_file_name,
                    'zenon_data_name' => $table_config->zenon_data_name,
                    'zenon_format_id' => $table_config->zenon_format_id,
                    'reason'          => $error_message,
        ];
    }

    public function uploadToDatabase($monthly_state, $csv_file_object, $monthly_id): array {
        $bulk    = [];
        $types   = [];
        $keys    = [];
        $configs = \App\ZenonTable::format($monthly_state->zenon_format_id)->select(['column_name', 'column_type',])->get();
        $this->debugMemory('uploadToDatabase - init');
        foreach ($configs as $c) {
            $types[$c->column_name] = $c->column_type;
            $keys[]                 = $c->column_name;
        }
        unset($configs);
        if ($this->isArrayEmpty($keys))
        {
            $this->setPreErrorToMonthlyStatus($monthly_state->id, 'テーブル設定が取り込まれていないようです。');
            return $this->makeErrorLog($monthly_state, 'テーブル設定が取り込まれていないようです。MySQL側 全オンテーブル設定から取込処理を行ってください。');
        }

        $split_key_configs = [
            'split_foreign_key_1' => $monthly_state->split_foreign_key_1,
            'split_foreign_key_2' => $monthly_state->split_foreign_key_2,
            'split_foreign_key_3' => $monthly_state->split_foreign_key_3,
            'split_foreign_key_4' => $monthly_state->split_foreign_key_4,
        ];

        $account_convert_param = [
            'account_column_name' => $monthly_state->account_column_name,
            'subject_column_name' => $monthly_state->subject_column_name,
        ];
        $this->debugMemory('uploadToDatabase - beforeGetTableObject');
        try {
            $table = $this->getTableObject('mysql_zenon', $monthly_state->table_name);
        } catch (\Exception $e) {
            $this->setPreErrorToMonthlyStatus($monthly_state->id, $e->getMessage());
            return $this->makeErrorLog($monthly_state, $e->getMessage());
        }
        $this->debugMemory('uploadToDatabase - afterGetTableObject');

        $this->setPreStartToMonthlyStatus($monthly_state->id);

        $line_number = 0;
        foreach ($csv_file_object as /* $line_number => */ $raw_line) {
            $line = $this->lineEncode($raw_line);
            if ($this->isArrayEmpty($line))
            {
                continue;
            }
            $line_number++;
            $tmp_bulk = $this->setRow($line)
                    ->setKeyToRow($keys)
                    ->convertRow($types, true)
                    ->splitRow($monthly_state->is_split, $monthly_state->first_column_position, $monthly_state->last_column_position, $split_key_configs)
                    ->setMonthlyIdToRow(true, $monthly_id)
                    ->setConvertedAccountToRow($monthly_state->is_account_convert, $account_convert_param)
                    ->setTimeStamp(date('Y-m-d H:i:s'))
                    ->getRow()
            ;
            unset($line);
            // MySQLのバージョンによってはプリペアドステートメントが65536までに制限されているため、動的にしきい値を設ける
            if ($line_number > 0 && (count($bulk) * count($tmp_bulk) + count($tmp_bulk)) > 65000)
            {
                $this->debugMemory('uploadToDatabase - beforeBulkInsert');
                $table->insert($bulk);
                $this->setExecutedRowCountToMonthlyStatus($monthly_state->id, $line_number);
                unset($bulk);
            }
            $bulk[] = $tmp_bulk;
            unset($tmp_bulk);
        }
        // 端数分をここでINSERT
        if (count($bulk) !== 0)
        {
            $table->insert($bulk);
            $this->setExecutedRowCountToMonthlyStatus($monthly_state->id, $line_number);
        }
        unset($table);
        $this->setPostEndToMonthlyStatus($monthly_state->id);
        return [];
    }

    private function getTableObject($connection, $table_name) {
        if (empty($table_name) || empty($connection))
        {
            throw new \Exception("コネクションもしくはテーブル名が指定されていないようです。");
        }
        try {
            $res = \DB::connection($connection)->table($table_name);
        } catch (\Exception $e) {
            throw new \Exception("ベーステーブルが存在しないようです。（テーブル名：{$table_name}）");
        }
        return $res;
    }

}
