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

    public function setCsvFile($file_path) {
//        var_dump($file_path);
//        \Log::info('IS_ERROR?:'.$file_path);
        if (!file_exists($file_path))
        {
            throw new \Exception("ファイルが存在しないようです。（ファイルパス：{$file_path}）");
        }

        $file           = new \SplFileObject($file_path, 'r');
        $file->setFlags(\SplFileObject::READ_CSV);
        $this->csv_file = $file;
        return $this;
    }

    public function getCsvFile() {
        return $this->csv_file;
    }

    public function convertCsvFileToArray($csv_file, $column_count = null, $is_exist_header = true) {
        $this->file_name = $csv_file->getClientOriginalName();
        $csv_line_count  = 0;
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
