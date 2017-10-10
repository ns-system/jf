<?php

namespace App\Services\Traits;

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
//        else
//        {
//            exec(escapeshellcmd("sudo touch {$json_output_path}"));
//        }
        $plane_text = str_replace(']', '', str_replace('[', '', $plane_text));
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

        exec(escapeshellcmd("touch {$json_output_path}"));
        exec(escapeshellcmd("chmod 777 {$json_output_path}"));
//        exec(escapeshellcmd("sudo touch {$json_output_path}"));
//        exec(escapeshellcmd("sudo chmod 777 {$json_output_path}"));
        $json_file = fopen($json_output_path, "wb");
        fwrite($json_file, json_encode($existing_data));
        fclose($json_file);
    }

}
