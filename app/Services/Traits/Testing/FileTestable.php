<?php

namespace App\Services\Traits\Testing;

use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FileTestable
{

    public function addToken(array $input_parameter = null) {
        \Session::start();
        if (empty($input_parameter) || !array_key_exists('_token', $input_parameter))
        {
            $input_parameter['_token'] = csrf_token();
        }
        return $input_parameter;
    }

//    public function makeResponse(string $method, string $redirect_url, array $input_parameter = null) {
//        return $this->call($method, $redirect_url, $this->getInputs($input_parameter), [], []);
//    }

    public function createUploadFile(string $file_path, string $file_name, string $mime_type) {
        if (mb_substr($file_path, -1) !== '/')
        {
            $file_path .= '/';
        }
        $full_path = $file_path . $file_name;
        if (!file_exists($full_path))
        {
            throw new \Exception("ファイルパス（{$full_path}）が見つかりません。");
        }
        return new UploadedFile($full_path, $file_name, $mime_type, filesize($full_path), null /* error code */, true /* テスト時にTrue：細かいチェックが省略される */);
    }

    public function createCsvFile(string $file_name, array $csv_lines = []) {
        $full_path = storage_path() . '/tests/' . $file_name;
        $this->unlinkFile($full_path);

        if (!touch($full_path))
        {
            $this->fail('ファイルの作成に失敗しました。');
        }
        if (empty($csv_lines))
        {
            return true;
        }
        $file = fopen($full_path, 'w');
        foreach ($csv_lines as $csv_line) {
            $raw_line = implode(',', $csv_line);
            $line     = mb_convert_encoding($raw_line, 'sjis-win', 'utf8');
            fwrite($file, $line . "\n");
        }
        fclose($file);
        return true;
    }

    public function unlinkFile($file_path) {
//        chmod($file_path, 0777);
//
//        $file = fopen($file_path, 'r');
//        fclose($file);
//        gc_collect_cycles();

        if (file_exists($file_path))
        {
            exec("rm -rf {$file_path}");
//            try {
//                unlink($file_path);
//            } catch (\Exception $exc) {
//                var_dump($exc->getMessage());
//                echo $exc->getTraceAsString();
//            }
        }
    }

}
