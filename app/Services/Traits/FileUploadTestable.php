<?php

namespace App\Services\Traits;

use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FileUploadTestable
{

    public function getInputs(array $input_parameter = null) {
        if (empty($input_parameter) || !array_key_exists('_token', $input_parameter))
        {
            $input_parameter['_token'] = csrf_token();
        }
        return $input_parameter;
    }

//    public function makeResponse(string $method, string $redirect_url, array $input_parameter = null) {
//        return $this->call($method, $redirect_url, $this->getInputs($input_parameter), [], []);
//    }

    public function createUploadFile(string $file_path, string $file_name, string $mine_type) {
        if (mb_substr($file_path, -1) !== '/')
        {
            $file_path .= '/';
        }
        $full_path = $file_path . $file_name;
        if (!file_exists($full_path))
        {
            throw new \Exception("ファイルパス（{$full_path}）が見つかりません。");
        }
        return new UploadedFile($full_path, $file_name, $mine_type, filesize($full_path), null /* error code */, true /* テスト時にTrue：細かいチェックが省略される */);
    }

}
