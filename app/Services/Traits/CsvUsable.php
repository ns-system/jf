<?php

namespace App\Services\Traits;

trait CsvUsable
{

    protected $file_name;
    protected $file_path;
    protected $csv_lines;
    protected $csv_file;
    protected $csv_line_count = 0;

    public function getFileName() {
        return $this->file_name;
    }

    public function getCsvLines() {
        return $this->csv_lines;
    }

    public function setCsvFilePath($path) {
        if (!file_exists($path))
        {
            throw new \Exception("ファイルが存在しないようです。（ファイルパス：{$path}）");
        }
        $ext  = pathinfo($path, PATHINFO_EXTENSION);
        $name = pathinfo($path, PATHINFO_BASENAME);
        if ($ext != 'csv')
        {
            throw new \Exception("CSVファイル以外が選択されました。（ファイルパス：{$path}）");
        }
        $this->file_path = $path;
        $this->file_name = $name;
        return $this;
    }

    public function setCsvFileObject($path = '') {
        if (!empty($path))
        {
            $this->setCsvFilePath($path);
        }
        $csv_path       = $this->file_path;
        $file           = new \SplFileObject($csv_path, 'r');
        $file->setFlags(\SplFileObject::READ_CSV + \SplFileObject::DROP_NEW_LINE + \SplFileObject::READ_AHEAD + \SplFileObject::SKIP_EMPTY);
        $this->csv_file = $file;
        return $this;
    }

    public function getCsvFileObject($path = '') {
        if (!empty($path))
        {
            $this->setCsvFileObject($path);
        }
        return $this->csv_file;
    }

    /**
     * Method      : getCsvFileArray
     * Description : CSVオブジェクトを配列に変換する
     * @param bool $is_header_exist : ヘッダーファイルがある場合、true指定。１行目を読み飛ばす。
     * @param int  $line_length     : 配列長チェックをしたい場合、配列長を指定。
     * @return $array               : 変換後の配列を返す。
     * @throws \Exception           : 行数が一致しなかった場合、エラーをスロー。
     */
    public function checkCsvFileLength($line_length) {
        if (empty($this->csv_file))
        {
            throw new \Exception("CSVファイルが指定されていません。");
        }
        foreach ($this->csv_file as $line_cnt => $line) {
            $cnt = count($line);
            if ($line_length !== $cnt)
            {
                throw new \Exception("行数：" . ($line_cnt + 1) . "行目のCSVのカラム数が一致しませんでした。（想定：{$line_length}，実際：" . ($cnt) . "）");
            }
        }
        $this->csv_lines = $line_cnt + 1;
        return $this;
    }

    public function lineEncode($line) {
        return mb_convert_encoding($line, 'UTF-8', 'sjis-win,sjis,JIS,EUC-JP');
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

}
