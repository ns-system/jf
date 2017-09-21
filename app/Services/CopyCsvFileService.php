<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

namespace App\Services;

/**
 * Description of ImportConfigService
 *
 * @author r-kawanishi
 */
use DatabaseMigrations;

class CopyCsvFileService
{

    use \App\Services\Traits\JsonUsable;

    protected $monthly_id;
    protected $directory_path;
    protected $directorys = [
        'temp'    => 'temp',
        'log'     => '',
        'monthly' => 'monthly',
        'daily'   => 'daily',
        'ignore'  => 'ignore',
    ];

    public function setMonthlyId($monthly_id) {
        $this->monthly_id = $monthly_id;
        return $this;
    }

    public function setDirectoryPath($directory_path) {
        $this->directory_path = $directory_path;
        if (!file_exists($this->directory_path))
        {
            throw new \Exception("存在しないファイルパスが指定されました。（マウント先：{$this->directory_path}）");
        }
//        $this->not_exist_json_output_path = $this->directory_path . "/log/notexist.json";
        foreach ($this->directorys as $d) {
            $tmp_dir = $this->directory_path . '/' . $d;
            if (!file_exists($tmp_dir))
            {
                throw new \Exception("格納先ファイルパスが存在しません。（格納先ファイル：{$tmp_dir}）");
            }
        }
//        if (!file_exists($this->not_exist_json_output_path))
//        {
//            throw new \Exception("LogFile出力時に存在しないファイルパスが指定されました。（ログファイル出力先：{$this->not_exist_json_output_path}）");
//        }
        return $this;
    }

    private function createDirectory($path) {
        if (!file_exists($path))
        {
//            mkdir($path, 0777, FALSE);
            exec("sudo mkdir -m=777 {$path}");
            return true;
        }
        return false;
    }

    public function copyCsvFile() {
        $monthly_id            = $this->monthly_id;
        $temp_file_path        = $this->directory_path . "/" . $this->directorys['temp'];
        $accumulation_dir_path = $this->directory_path;
        $file_lists            = $this->getCsvFileList($temp_file_path);

        foreach ($file_lists as $i => $f) {
            $src       = $temp_file_path . '/' . $f['csv_file_name'];
            $daily_dir = $accumulation_dir_path . "/" . $this->directorys['daily'] . "/" . $f['monthly_id'];
            $this->createDirectory($daily_dir);
            $this->createDirectory($f["destination"]);
            $dest      = $f["destination"] . "/" . $f["csv_file_name"];
            exec("sudo cp -f -p {$src} {$dest}");
        }

        return $this;
    }

    public function getCsvFileList($directory_path) {
        if (!file_exists($directory_path))
        {
            throw new \Exception("存在しないファイルパスが指定されました。（指定先：{$directory_path}）");
        }
        $tmp_lists = scandir($directory_path);
        $lists     = [];
        foreach ($tmp_lists as $t) {
            $f = pathinfo($t);
            if (!empty($f['extension']) && $f['extension'] == 'csv')
            {
                $date      = null;
                $file_path = $directory_path . '/' . $t;
                $date_text = mb_substr($t, 14, 8);
                $monthly   = null;
                $daily     = null;
                $path      = $this->directory_path;
//                echo $date_text;
                if (!strptime($date_text, '%Y%m%d') && mb_strlen($date_text) !== 8 || !empty(strptime($date_text, '%Y%m%d')["unparsed"]))
                {
                    continue;
                }
                else
                {
                    $date    = date('Y-m-d', strtotime($date_text));
                    $monthly = date('Ym', strtotime($date_text));
                    $daily   = date('d', strtotime($date_text));
                }


                if (mb_substr($t, 8, 1) == 'D')
                {
                    $path .= "/{$this->directorys['daily']}/{$monthly}/{$daily}";
                }
                elseif (mb_substr($t, 8, 1) == 'M')
                {
                    $path .= "/{$this->directorys['monthly']}/{$monthly}";
                }
                else
                {
                    $path .= "/{$this->directorys['ignore']}/{$monthly}";
                }
                $lists[] = [
                    'destination'      => $path,
                    'csv_file_name'    => $t,
                    'monthly_id'       => $monthly,
                    'cycle'            => mb_substr($t, 8, 1),
                    'csv_file_set_on'  => $date,
                    'identifier'       => mb_substr($t, 8, 5),
                    'kb_size'          => round(filesize($file_path) / 1024),
                    'file_create_time' => filemtime($file_path),
                ];
            }
        }
        array_multisort(array_column($lists, 'identifier'), $lists);

        return $lists;
    }

    public function tableTemplateCreation() {
        $monthly_id           = $this->monthly_id;
        $zenon_data_csv_files = \App\ZenonCsv::all();
        \DB::connection('mysql_suisin')->transaction(function() use($zenon_data_csv_files, $monthly_id) {
            foreach ($zenon_data_csv_files as $zenon_data_csv_file) {

                $process_status = \App\ZenonMonthlyStatus::firstOrNew(['monthly_id' => $monthly_id, 'zenon_data_csv_file_id' => $zenon_data_csv_file->id]);
                $process_status->save();
            }
        });
        return $this;
    }

    public function registrationCsvFileToDatabase() {

        $file_lists        = [];
        $ignore_file_lists = [];
        $monthly_id        = $this->monthly_id;
        $tmp_file_lists    = $this->getCsvFileList($this->directory_path . "/" . $this->directorys['temp']);
        $json_output_path  = storage_path() . "/jsonlogs";
        foreach ($tmp_file_lists as $l) {

            if ($l['cycle'] == 'M' && date('Ym', strtotime($l['csv_file_set_on'] . ' -1 month')) == $monthly_id)
            {
                $file_lists[$l['identifier']] = $l;
            }
            else
            {
                $ignore_file_lists[] = $l;
            }
        }
        $ignore_file_list_json_file_name = $monthly_id . "_ignore_file_list" . ".json";
        $this->outputForJsonFile($ignore_file_lists, $json_output_path, $ignore_file_list_json_file_name);

        \DB::connection('mysql_suisin')->transaction(function() use($file_lists, $monthly_id, $json_output_path) {
            $not_exist_file_list = [];
            $rows                = \App\ZenonMonthlyStatus::month($monthly_id)
                    ->join("zenon_data_csv_files", "zenon_data_monthly_process_status.zenon_data_csv_file_id", "=", "zenon_data_csv_files.id")
                    ->get()
            ;
            //手元にあるがDB上にないファイルを出力できるように
            foreach ($file_lists as $file) {
                $is_exist = 0;
                foreach ($rows as $r) {
                    if ($file["identifier"] == $r->identifier)
                    {
                        $is_exist           = 1;
                        $r->csv_file_name   = $file['csv_file_name'];
                        $r->csv_file_set_on = $file['csv_file_set_on'];
                        $r->is_exist        = (int) true;
                        $r->file_kb_size    = $file['kb_size'];
                        $r->save();
                    }
                }
                if ($is_exist == 0)
                {
                    $not_exist_file_list[] = $file;
                }
            }

            $not_exist_file_list_json_file_name = $monthly_id . "_not_exist_file_list" . ".json";
            $this->outputForJsonFile($not_exist_file_list, $json_output_path, $not_exist_file_list_json_file_name);
        });

        return $this;
    }

    public function tempFileErase() {

        $tmp_file_lists = glob($this->directory_path . "/" . $this->directorys['temp'] . "/*");
        foreach ($tmp_file_lists as $l) {
            exec("sudo rm -rf {$l}");
        }
    }

    public function inputCheck($monthly_id, $accumulation_dir_path) {
        if (!file_exists($accumulation_dir_path))
        {
            //おかしかったらエラー処理
            throw new \Exception("累積先ディレクトリが存在しないようです。（想定：{$accumulation_dir_path}）");
        }
        if (!strptime($monthly_id, '%Y%m') || mb_strlen($monthly_id) !== 6 || nonEmptyArray(strptime($monthly_id, '%Y%m')["unparsed"]))
        {
            //おかしかったらエラー処理
            throw new \Exception("月別IDに誤りがあるようです。（投入された値：{$monthly_id}）");
        }
    }

}
