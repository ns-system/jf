<?php

namespace App\Services;

use \App\Services\Traits\JsonUsable;
use \App\Services\Traits\CsvUsable;

class ImportZenonDataService_1
{

    use JsonUsable,
        CsvUsable;

    public function getCsvSplitRow($csv_file_object, $table_columns, $table_types, $table_config, $month_id) {
        mb_convert_variables('UTF-8', 'SJIS-WIN', $csv_file_object);
        $convert_row = [];
        foreach ($row as $i => $buf) {
            $convert_row[] = $this->convertValue($buf, $table_types[$i]);
        }
        $convert_row       = array_combine($table_columns, $convert_row);
        $convert_row['id'] = null;

        if ($table_config->is_cumulative == true)
        {
            $convert_row['monthly_id'] = $month_id;
        }
        if ($table_config->is_account_convert == true)
        {
            $p = [
                'account' => $table_config->account_column_name,
                'subject' => $table_config->subject_column_name,
            ];
            if ($p['account'] == '' || $p['account'] == '')
            {
                throw new \Exception("subject_column_nameもしくはaccount_column_nameの値が不正です。");
            }
            $convert_row['key_account_number'] = $this->convertAccount($p, $convert_row);
        }

        $convert_row['created_at'] = date("Y-m-d H:i:s");
        $convert_row['updated_at'] = date("Y-m-d H:i:s");
        if (!$table_config->is_split)
        {
            return $convert_row;
        }

        // 切り落とし処理
        $first     = $table_config->first_column_position;
        $last      = $table_config->last_column_position;
        $slice_row = array_slice($convert_row, $first, $last, true);
        for ($i = 1; $i <= 4; $i++) {
            $k  = 'split_foreign_key_' . $i;
            $fk = $table_config->$k;
            if ($fk != null)
            {
                $slice_row[$fk] = $convert_row[$fk];
            }
        }
        if ($table_config->is_cumulative == true)
        {
            $slice_row['monthly_id'] = $convert_row['monthly_id'];
        }
        if ($table_config->is_account_convert == true)
        {
            $slice_row['key_account_number'] = $convert_row['key_account_number'];
        }
        $slice_row['created_at'] = $convert_row['created_at'];
        $slice_row['updated_at'] = date("Y-m-d H:i:s");
        return $slice_row;
    }

    private function convertAccount($param, $row) {
//        echo "sfdfsd";
        $ac_key = $param['account'];
        $sb_key = $param['subject'];
        switch ($row[$sb_key]) {
            case 1:
            case 2:
            case 8:
            case 9:
            case 11:
                if (mb_strlen($row[$ac_key]) <= 3)
                {
                    throw new \Exception("口座番号が短すぎるようです（科目：{$row[$sb_key]}， 口座番号：{$row[$ac_key]}）");
                }
                $buf = trim($row[$ac_key]);
                return mb_substr($buf, 0, /* mb_strlen($row[$sb_key]) - 3 */ -3);
            default:
                return $row[$ac_key];
        }
    }

    private function convertValue($buf, $attr) {
        switch ($attr) {
            case 'char':
                return $this->splitSpace($buf);

            case 'integer':
            case 'bigInteger':
                if (!is_numeric($buf) && $buf != ' ' && $buf != '')
                {
                    throw new \Exception("数値型に変換できませんでした（値：[{$buf}]）");
                }
                return (int) $buf;
            case 'float':
            case 'double':
                if (!is_numeric($buf) && $buf != ' ' && $buf != '')
                {
                    throw new \Exception("数値型に変換できませんでした（値：[{$buf}]）");
                }
                return (double) $buf;
            case 'date':
                return $this->convertDate($buf);
            case 'boolean':
                return (bool) $buf;
            Default:
                return $buf;
        }
    }

    private function splitSpace($buf) {
        $return = preg_replace("/^[\s　]*(.*?)[\s　]*$/u", "$1", $buf);
        return $return;
    }

    private function convertDate($buf) {
        if ($buf == '00000000')
        {
            return null;
        }
        return $buf;
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

    public function uploadToDatabase($r, $csv, $ym) {
        $table            = \DB::connection('mysql_zenon')->table($r->table_name);
        $table_column_obj = \App\ZenonTable::where(['zenon_format_id' => $r->zenon_format_id])->get(['column_name', 'column_type']);
        $table_columns    = [];
        $table_types      = [];

        foreach ($table_column_obj as $t) {
            $table_columns[] = $t->column_name;
            $table_types[]   = $t->column_type;
        }
        $bulk_rows    = [];
        $bulk_counter = 0;

        $executed_rows = 0;
        foreach ($csv as $i => $row) {
            if ($row === [null])
            {
                continue;
            }
            else
            {
                $executed_rows++;
            }

            $tmp_bulk    = $this->setCsvSplitRow($row, $table_columns, $table_types, $r, $ym);
            $cnt         = count($tmp_bulk);
            $bulk_rows[] = $tmp_bulk;
            // MySQLのバージョンによってはプリペアドステートメントが65536までに制限されているため、動的にしきい値を設ける
            if ($i !== 0 && ($cnt + $bulk_counter) > 65000)
            {
                $r->executed_row_count = $executed_rows;
                $r->save();

                $table->insert($bulk_rows);
                $bulk_counter = 0;
                $bulk_rows    = [];
            }
            else
            {
                $bulk_counter += $cnt;
            }
        }
        if ($bulk_rows !== [null])
        {
            $r->executed_row_count = $executed_rows;
            $r->save();
            $table->insert($bulk_rows);
        }
    }

    public function getLastTraded($row) {
        $date = $row->reference_last_traded_on;
        if (empty($date) || $date == '0000-00-00')
        {
            return $row->last_traded_on;
        }
        return $date;
    }

    public function convertTypes($types, $rows) {
        $tmp_rows = [];
        foreach ($rows as $i => $row) {
            foreach ($row as $key => $column) {

                if (!array_key_exists($key, $types))
                {
//                    echo "notexist {$key}<br>";
                    $tmp_rows[$i][$key] = $column;
                    continue;
                }

                if ($types[$key] === 'integer')
                {
                    $tmp_rows[$i][$key] = (int) $column;
                }
                elseif ($types[$key] === 'float')
                {
                    $tmp_rows[$i][$key] = (float) $column;
                }
                elseif ($types[$key] === 'time')
                {
                    if ($column == 'NULL' || $column == 'null')
                    {
                        $tmp_rows[$i][$key] = null;
                    }
                    else
                    {
                        $tmp_rows[$i][$key] = date("H:i:s", strtotime($column));
                    }
                }
                elseif ($types[$key] === 'boolean')
                {
                    $tmp_rows[$i][$key] = (bool) $column;
                }
                else
                {
                    $tmp_rows[$i][$key] = $column;
                }
            }
        }
        return $tmp_rows;
    }

}
