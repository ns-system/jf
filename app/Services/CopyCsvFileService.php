<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

/**
 * Description of ImportConfigService
 *
 * @author r-kawanishi
 */
class CopyCsvFileService
{

    protected $monthly_id;
    protected $directory_path;
    protected $not_exist_json_output_path;
    public function setMonthlyId($monthly_id) {
        $this->monthly_id = $monthly_id;
        return $this;
    }

    public function setDirectoryPath($directory_path) {
        $this->directory_path = $directory_path;
        $this->not_exist_json_output_path = $this->directory_path . "/log/notexist.json";
           if (!file_exists($this->not_exist_json_output_path))
        {
            throw new \Exception("LogFile出力時に存在しないファイルパスが指定されました。（ログファイル出力先：{$this->not_exist_json_output_path}）");
        }
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

    public function accumulationFileCreation() {
        $monthly_id                                = $this->monthly_id;
        $accumulation_dir_path                     = $this->directory_path;
        $serial                                    = strtotime($monthly_id . '01');
        $last_day                                  = date('t', $serial);
        $before_last_day                           = date("t", strtotime($monthly_id . '01 -1 month'));
        $before_monthly_id                         = date("Ym", strtotime($monthly_id . '01 -1 month')); // Spell miss
        $after_monthly_id                          = date("Ym", strtotime($monthly_id . '01 +1 month'));
        $after_last_day                            = date("t", strtotime($monthly_id . '01 +1 month'));
        $monthly_data_accumulation_dir_path        = $accumulation_dir_path . "/monthly/" . $monthly_id;
        $daily_data_accumulation_dir_path          = $accumulation_dir_path . "/daily/" . $monthly_id;
        $after_daily_data_accumulation_dir_path    = $accumulation_dir_path . "/daily/" . $after_monthly_id;
        $before_daily_data_accumulation_dir_path   = $accumulation_dir_path . "/daily/" . $before_monthly_id;
        $etcetera_data_accumulation_dir_path       = $accumulation_dir_path . "/exclude_files/" . $monthly_id; // TODO: toste->kawanishi 実サーバーに合わせてね
        $before_monthly_data_accumulation_dir_path = $accumulation_dir_path . "/monthly/" . $before_monthly_id;



        $this->createDirectory($accumulation_dir_path);

        $this->createDirectory($monthly_data_accumulation_dir_path);
        $this->createDirectory($before_monthly_data_accumulation_dir_path);

        $this->createDirectory($daily_data_accumulation_dir_path);
        for ($i = 1; $i <= $last_day; $i++) {
            $this->createDirectory($daily_data_accumulation_dir_path . "/" . sprintf('%02d', $i));
        }

        $this->createDirectory($before_daily_data_accumulation_dir_path);
        for ($i = 1; $i <= $before_last_day; $i++) {
            $this->createDirectory($before_daily_data_accumulation_dir_path . "/" . sprintf('%02d', $i));
        }
        $this->createDirectory($after_daily_data_accumulation_dir_path);
        for ($i = 1; $i <= $after_last_day; $i++) {
            $this->createDirectory($after_daily_data_accumulation_dir_path . "/" . sprintf('%02d', $i));
        }

        $this->createDirectory($etcetera_data_accumulation_dir_path);



        // last_day使わないならチェーンメソッドにしようぜ。
        return $this;
//        return $last_day;
    }

    public function copyCsvFile() {
        $monthly_id            = $this->monthly_id;
        $temp_file_path        = $this->directory_path . '/temp'; // TODO: tosite->kawanishi USBじゃないよね．．．kawanishi->toite temp_fileに変えました
        $accumulation_dir_path = $this->directory_path;
        $file_lists            = $this->getCsvFileList($temp_file_path);

        foreach ($file_lists as $f) {
            $src = $temp_file_path . '/' . $f['csv_file_name'];
//            $src = $temp_file_path . '/temp/' . $f['csv_file_name']; // こっちでも可


            if ($f['cycle'] == 'D')
            {
                $day          = date('d', strtotime($f['csv_file_set_on']));
                $monthly_path = date('Ym', strtotime($f['csv_file_set_on']));
                $dest         = $accumulation_dir_path . "/daily/" . $monthly_path . "/" . $day . "/" . $f['csv_file_name'];
//                copy($src, $dist);
            }
            elseif ($f['cycle'] == 'M')
            {
                $monthly_path = date('Ym', strtotime($f['csv_file_set_on'] . ' -1 month'));
                $dest         = $accumulation_dir_path . "/monthly/" . $monthly_path . "/" . $f['csv_file_name'];
//                copy($src, $dist);
            }
            else
            {
                $dest = $accumulation_dir_path . "/exclude_files/" . $monthly_id . "/" . $f['csv_file_name'];
            }
            exec("sudo cp -f -p {$src} {$dest}");
//            copy($src, $dist);
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
                if (!strptime($date_text, '%Y%m%d'))
                {
                    continue;
                }
                else
                {
                    $date = date('Y-m-d', strtotime($date_text));
                }


                $lists[] = [
                    'csv_file_name'    => $t,
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
        $tmp_file_lists    = $this->getCsvFileList($this->directory_path . '/temp');
//        var_dump($tmp_file_lists);
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


        \DB::connection('mysql_suisin')->transaction(function() use($file_lists, $monthly_id) {
            $not_exist_file_list = [];
            $rows                = \App\ZenonMonthlyStatus::month($monthly_id)
                    ->join("zenon_data_csv_files", "zenon_data_monthly_process_status.zenon_data_csv_file_id", "=", "zenon_data_csv_files.id")
                    ->get()
            ;
//            foreach ($rows as $r) {
//                if (isset($file_lists[$r->identifier]))
//                {
//                    $f = $file_lists[$r->identifier];
//                    $r->csv_file_name   = $f['csv_file_name'];
//                    $r->csv_file_set_on = $f['csv_file_set_on'];
//                    $r->is_exist        = (int) true;
//                    $r->file_kb_size    = $f['kb_size'];
//                    $r->save();
//                }
//            }
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
            
            $this->outputForJsonFile($not_exist_file_list);
        });

        return $this;
    }

    public function tempFileErase() {

        $tmp_file_lists = glob($this->directory_path . '/temp/*');
        foreach ($tmp_file_lists as $l) {
            unlink($l);
        }
    }

    public function outputForJsonFile($array) {
        $data = json_encode($array);
        $json_file = fopen($this->not_exist_json_output_path, "w+b");
        fwrite($json_file, $data);
        fclose($json_file);
    }
 public function inputCheck($monthly_id,$accumulation_dir_path)
    {
         if (!file_exists($accumulation_dir_path))
        {
            //おかしかったらエラー処理
            throw new \Exception("累積先ディレクトリが存在しないようです。（想定：{$accumulation_dir_path}）");
        }
        if (!strptime($monthly_id, '%Y%m'))
        {
            //おかしかったらエラー処理
            throw new \Exception("月別IDに誤りがあるようです。（投入された値：{$monthly_id}）");
        }
    }
}
