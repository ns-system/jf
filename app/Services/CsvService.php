<?php

namespace App\Services;

class CsvService
{

    protected $file_name;
    protected $csv_rows;
    protected $csv_line_count = 0;

    public function getFileName() {
        return $this->file_name;
    }

    public function getCsvRows() {
        return $this->csv_rows;
    }

    public function getCsvLineCount() {
        return $this->csv_line_count;
    }

    /**
     * @param type $export_datas
     * @param string $file_name
     * @param type $file_header
     * @return type
     */
    public function exportCsv($export_datas, $file_name, $file_header = []) {
        $this->file_name = $file_name;
        $file_name       = 'attachment; filename=' . $file_name;
        if ($file_header !== [null])
        {
            array_unshift($export_datas, $file_header);
        }
        $stream = fopen('php://temp', 'r+b');
        foreach ($export_datas as $export_data) {
            fputcsv($stream, $export_data);
        }
        rewind($stream);
        $csv     = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));
        $csv     = mb_convert_encoding($csv, "SJIS-win", "UTF-8");
        $headers = array(
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => $file_name,
        );
        return \Response::make($csv, 200, $headers);
    }

    public function convertCsvFileToArray($csv_file, $column_count = null, $is_exist_header = true) {
        $this->file_name = $csv_file->getClientOriginalName();
        $csv_line_count               = 0;
        if ($csv_file->getClientOriginalExtension() != 'csv')
        {
            throw new \Exception('拡張子はcsv以外取り込みできません。');
        }
        $csv_obj   = new \SplFileObject($csv_file, 'r');
        $csv_obj->setFlags(\SplFileObject::READ_CSV);
        $csv_datas = [];
        foreach ($csv_obj as $line_no => $line) {
            $row = [];
            if (
                    $line === [null] ||
                    ($is_exist_header === true && $line_no == 0)
            )
            {
                continue;
            }
            foreach ($line as $i => $buf) {
                $row[] = mb_convert_encoding($buf, 'UTF-8', 'sjis-win, sjis, JIS, EUC-JP');
            }
            if ($column_count !== null && $i + 1 !== $column_count)
            {
                throw new \Exception("行数：{$line_no}行目のCSVのカラム数が一致しませんでした。（想定：{$column_count}，実際：" . ($i + 1) . "）");
            }
            $csv_line_count++;
            $csv_datas[] = $row;
        }

        $this->csv_line_count = $csv_line_count;
        $this->csv_rows       = $csv_datas;
        return $this;
//        return $csv_datas;
    }

    /**
     * @param type $csv_id
     * @param type $column_count
     * @param type $is_exist_header
     * @return type
     * @throws Exception
     */
    public function importCsv(/* $csv_id */$csv_file, $column_count, $is_exist_header = true) {

        throw new \Exception('これじゃない');
//        var_dump($file);
//        $this->file_name = $file->getClientOriginalName();
//        if ($file->getClientOriginalExtension() != 'csv')
//        {
//            throw new \Exception('拡張子はcsv以外取り込みできません。');
//        }
//
//        $csv_obj = new \SplFileObject($file, 'r');
//        $csv_obj->setFlags(\SplFileObject::READ_CSV);
//
//        $csv_datas = [];
//        foreach ($csv_obj as $line_no => $line) {
//            $row = [];
//            if (
//                    $line === [null] ||
////                    $row === [] ||
////                    empty($row) === true ||
//                    ($is_exist_header === true && $line_no == 0)
//            )
//            {
//                continue;
//            }
//            foreach ($line as $i => $buf) {
//                $row[] = mb_convert_encoding($buf, 'UTF-8', 'sjis-win, sjis, JIS, EUC-JP');
//            }
////            var_dump($i.'<->'.$column_count);
//            if ($i + 1 !== $column_count)
//            {
//                throw new \Exception("行数：{$line_no}行目のCSVのカラム数が一致しませんでした。（想定：{$column_count}，実際：" . ($i + 1) . "）");
//            }
//            $csv_datas[] = $row;
//        }
////        var_dump($csv_datas);
//        return $csv_datas;
    }

//    private function setKeys($primary_key, $row) {
//        if (!is_array($primary_key))
//        {
//            return [$primary_key => $row[$primary_key][1]];
//        }
//        $conditions = [];
//        foreach ($primary_key as $pk) {
//            $conditions[$pk] = $row[$pk][1];
//        }
////        var_dump($conditions);
////        exit();
//        return $conditions;
//    }
//
//    public function reflectToDb($model, $is_insert_execute = true, $rows = []) {
//        if (count($rows) === 0 && $this->rows !== [null])
//        {
//            $rows = $this->rows;
//        }
//        $primary_key = $rows['key'];
//        $insert_cnt  = 0;
//        $update_cnt  = 0;
//
//        foreach ($rows['rows'] as $row) {
//            $tmp        = new $model;
//            $conditions = $this->setKeys($primary_key, $row);
////            $is_exist = $tmp->where($primary_key, $row[$primary_key][1])->exists();
////            $table    = $tmp->firstOrNew([$primary_key => $row[$primary_key][1]]);
//            $is_exist   = $tmp->where($conditions)->exists();
//            $table      = $tmp->firstOrNew($conditions);
//
//            $is_insert = false;
//            $is_update = false;
//            foreach ($row as $key => $column) {
//                if (!$is_exist)
//                {
//                    // データベースにデータが存在しなかった場合
//                    if ($is_insert_execute === true)
//                    {
//                        $table[$key] = $column[1];
//                        $is_insert   = true;
//                    }
//                }
//                else
//                {
//                    // データベースにデータが存在した場合
//                    if ($column[0] === 1)
//                    {
//                        $table[$key] = $column[1];
////                    echo $key."->".$column[1].' , ';
//                    }
//                    $is_update = true;
//                }
//            }
////            echo "<br>";
//            if ($is_insert === true)
//            {
//                $insert_cnt++;
//            }
//            elseif ($is_update === true)
//            {
//                $update_cnt++;
//            }
//
//            $table->save();
//        }
//        $this->insert_cnt = $insert_cnt;
//        $this->update_cnt = $update_cnt;
//        return $this;
//    }
//
//    public function getCount() {
//        return [
//            'insert_count' => $this->insert_cnt,
//            'update_count' => $this->update_cnt,
//        ];
//    }
//
//    public function swapColumnRow($array, $add_columns = []) {
//        $tmp_rows = [];
//        $i        = 0;
//        foreach ($array as $key => $row) {
//            if ($key === '_token')
//            {
//                continue;
//            }
////            echo $key;
////            var_dump($row);
//            $tmp_row = [];
//            foreach ($row as $i => $column) {
//                $tmp_rows[$i][$key] = $column;
//                if (count($add_columns) !== 0)
//                {
//                    foreach ($add_columns as $add_key => $buf) {
//                        $tmp_rows[$i][$add_key] = $buf;
//                    }
//                }
//            }
//        }
//        $this->rows = $tmp_rows;
//        return $this;
//    }

    public function convertTypes($types, $rows) {
//        var_dump($this->rows);
//        if (count($rows) === 0 && $this->rows !== [null])
//        {
//            $rows = $this->rows;
//        }
//        var_dump($rows);
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
//        var_dump($tmp_rows);
//        $this->rows = $tmp_rows;
        return $tmp_rows;
    }

//    public function setUpdateFlags($update_flags, $rows = []) {
//        if (count($rows) === 0 && $this->rows !== [null])
//        {
//            $rows = $this->rows;
//        }
//        $tmp_rows = [];
//        foreach ($rows as $i => $row) {
//            foreach ($row as $key => $column) {
//                if (!array_key_exists($key, $update_flags))
//                {
//                    $tmp_rows[$i][$key] = [0, $column];
//                    continue;
//                }
//                $tmp_rows[$i][$key] = [$update_flags[$key], $column];
//            }
//        }
//        $this->rows = $tmp_rows;
//        return $this;
//    }
//
//    public function setPrimaryKey($primary_key, $rows = []) {
//        if (count($rows) === 0 && $this->rows !== [null])
//        {
//            $rows = $this->rows;
//        }
//        $tmp_rows   = [
//            'key'  => $primary_key,
//            'rows' => $rows,
//        ];
//        $this->rows = $tmp_rows;
//        return $this;
//    }
//
//    public function getRows() {
//        return $this->rows;
//    }

}
