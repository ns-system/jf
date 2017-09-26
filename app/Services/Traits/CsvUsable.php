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
//        $file->setFlags(\SplFileObject::READ_CSV + \SplFileObject::DROP_NEW_LINE + \SplFileObject::READ_AHEAD + \SplFileObject::SKIP_EMPTY);
        // SplFileObject::SKIP_EMPTY フラグをセットすると行中にNULLが入っていた場合、そこで止まってしまうため除外
        // 実例：[1, 2, 3, null, 5, 6] => [1, 2, 3]
        $file->setFlags(\SplFileObject::READ_CSV /* | \SplFileObject::DROP_NEW_LINE | \SplFileObject::READ_AHEAD  | \SplFileObject::SKIP_EMPTY */);
//        if (empty($file))
//        {
//            throw new \Exception("CSVファイルが指定されていないようです。");
//        }
        $this->csv_file = $file;
        return $this;
    }

    public function setCsvFileObjectFromRequest($request_csv_file, $is_header_exist = true, $column_length = null) {
        $file_name = $request_csv_file->getClientOriginalName();
        if ($request_csv_file->getClientOriginalExtension() != 'csv')
        {
            throw new \Exception("拡張子が違うようです。（ファイルパス：{$file_name}）");
        }
        $this->setCsvFileObject($request_csv_file);
        if (!empty($column_length))
        {
            $this->checkCsvFileLength($column_length, $is_header_exist);
        }
        return $this;
    }

    public function getCsvFileObject($path = '') {
        if (!empty($path))
        {
            $this->setCsvFileObject($path);
        }
        return $this->csv_file;
    }

    public function isArrayEmpty($array) {
        if (!is_array($array))
        {
            if (empty($array))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        foreach ($array as $item) {
            if (!$this->isArrayEmpty($item))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Method      : getCsvFileArray
     * Description : CSVオブジェクトを配列に変換する
     * @param int  $line_length     : 配列長チェックをしたい場合、配列長を指定。
     * @param bool $is_header_exist : ヘッダーファイルがある場合、true指定。１行目を読み飛ばす。
     * @return $array               : 変換後の配列を返す。
     * @throws \Exception           : 行数が一致しなかった場合、エラーをスロー。
     */
    public function checkCsvFileLength($line_length, $is_header_exist = false) {
        if (empty($this->csv_file))
        {
            throw new \Exception("CSVファイルが指定されていないようです。");
        }
        foreach ($this->csv_file as $line_cnt => $raw_line) {
            $line = $this->lineEncode($raw_line);

            // ヘッダー行の指定があった場合、もしくは行が空の場合スキップ
            // この場合ラインカウンタを1行減らす
            if (($is_header_exist && $line_cnt === 0) || $this->isArrayEmpty($line))
            {
                $line_cnt--;
                continue;
            }
            $cnt = count($line);
            if ($line_length !== $cnt)
            {
//                var_dump($line);
                throw new \Exception("行数：" . ($line_cnt + 1) . "行目のCSVのカラム数が一致しませんでした。（想定：{$line_length}，実際：" . ($cnt) . "）");
            }
        }
        $this->csv_lines = $line_cnt + 1;
        return $this;
    }

    public function lineEncode($line) {
//        return mb_convert_encoding($line, 'UTF-8', 'sjis-win,sjis,JIS,EUC-JP');
//        $encode = mb_detect_encoding($str);
//        if (is_array($line))
//        {
//            mb_convert_variables('UTF-8', 'SJIS-WIN', $line);
//            var_dump($line);
//            return $line;
//        }
        mb_convert_variables('UTF-8', 'SJIS-win', $line);
        return $line;
//        return $param;
//
//        
//        
//        return mb_convert_encoding($line, 'UTF-8', 'SJIS-WIN,SJIS,JIS,EUC-JP');
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
