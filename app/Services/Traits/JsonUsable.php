<?php

namespace App\Services;

trait JsonUsable
{

    protected $file_path;

    public function setFilePath($path, $name) {
        if (!file_exists($path))
        {
            throw new \Exception("ファイルパスが存在しません。（ファイルパス：{$path}）");
        }

        if (mb_substr($path, -1) != '/')
        {
            $path .= '/';
        }
        $full_path = $path . $name;

        $ext = pathinfo($full_path, PATHINFO_EXTENSION);
        if (empty($ext))
        {
            throw new \Exception("指定されたパスはファイル形式ではありません。（ファイルパス：{$full_path}）");
        }
        if ($ext != 'json')
        {
            throw new \Exception("拡張子が.json以外です。（ファイルパス：{$full_path}）");
        }
        $this->file_path = $full_path;
        return $this;
    }

    public function getJsonFile($path = '', $name = '') {
        if (!empty($path) && !empty($name))
        {
            $this->setFilePath($path, $name);
        }
        $json_path = $this->file_path;

        $tmp   = file_get_contents($json_path);
        $json  = mb_convert_encoding($tmp, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $array = json_decode($json, true);

//        if ($array === null)
//        {
//            throw new \Exception("ファイル内に設定ファイルが記述されていないようです。（ファイルパス：{$json_path}）");
//        }
        return $array;
    }

    public function outputForJsonFile($export_array, $path = '', $name = '') {
        if (!empty($path) && !empty($name))
        {
            $this->setFilePath($path, $name);
        }
//        $json_path = $this->file_path;

        $json_output_path = $this->file_path;
        $plane_text       = null;
        $existing_data    = [];
        if (file_exists($json_output_path))
        {
            $plane_text    = file_get_contents($json_output_path);
            $existing_data = $this->getJsonFile();
        }
        else
        {
            exec(escapeshellcmd("sudo touch {$json_output_path}"));
            exec(escapeshellcmd("sudo chmod 777 {$json_output_path}"));
        }
        if ((!empty($plane_text)) && empty($existing_data))
        {
            throw new \Exception("Jsonファイル読み込み時にエラーが発生しました。（ファイルパス：{$json_output_path}）");
        }

        foreach ($export_array as $key => $data) {
            if (is_array($data))
            {
                $existing_data[] = $data;
            }
            else
            {
                $existing_data[$key] = $data;
            }
        }

        $json_file = fopen($json_output_path, "w+b");
        fwrite($json_file, json_encode($existing_data));
        fclose($json_file);
    }

}
