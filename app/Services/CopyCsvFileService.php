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

    public function setMonthlyId($monthly_id) {
        $this->monthly_id = $monthly_id;
        return $this;
    }

    public function setDirectoryPath($directory_path) {
        $this->directory_path = $directory_path;
        return $this;
    }

    private function createDirectory($path) {
        if (!file_exists($path))
        {
            mkdir($path, 0777, FALSE);
            return true;
        }
        return false;
    }

    public function accumulationFileCreation(/* $monthly_id, $accumulation_dir_path */) {
        $monthly_id            = $this->monthly_id;
        $accumulation_dir_path = $this->directory_path;
        $serial                = strtotime($monthly_id . '01');
        $last_day              = date('t', $serial);
        $before_last_day       = date("t", strtotime($monthly_id . '01 -1 month'));
        $before_monthly_id     = date("Ym", strtotime($monthly_id . '01 -1 month')); // Spell miss

        $monthly_data_accumulation_dir_path        = $accumulation_dir_path . "/monthly/" . $monthly_id;
        $daily_data_accumulation_dir_path          = $accumulation_dir_path . "/daily/" . $monthly_id;
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

        $this->createDirectory($etcetera_data_accumulation_dir_path);


//        if (!(file_exists($accumulation_dir_path)))
//        {
//            mkdir($accumulation_dir_path, 0777, FALSE);
//        }
//        if (!(file_exists($monthly_data_accumulation_dir_path)))
//        {
//            mkdir($monthly_data_accumulation_dir_path, 0777, FALSE);
//        }
//        if (!(file_exists($before_monthly_data_accumulation_dir_path)))
//        {
//            mkdir($before_monthly_data_accumulation_dir_path, 0777, FALSE);
//        }

//        if (!(file_exists($daily_data_accumulation_dir_path)))
//        {
//            mkdir($daily_data_accumulation_dir_path, 0777, FALSE);
//            for ($i = 1; $i <= $last_day; $i++) {
//                $day = sprintf('%02d', (int) $i);
//                if (!file_exists($daily_data_accumulation_dir_path . "/" . $day))
//                {
//                    mkdir($daily_data_accumulation_dir_path . "/" . $day, 0777, FALSE);
//                }
//            }
//        }
//        if (!(file_exists($before_daily_data_accumulation_dir_path)))
//        {
//            mkdir($before_daily_data_accumulation_dir_path, 0777, FALSE);
//            for ($i = 1; $i <= $last_day; $i++) {
//                $day = sprintf('%02d', (int) $i);
//                if (!file_exists($daily_data_accumulation_dir_path . "/" . $day))
//                {
//                    mkdir($daily_data_accumulation_dir_path . "/" . $day, 0777, FALSE);
//                }
//            }
//        }

//        if (!(file_exists($etcetera_data_accumulation_dir_path)))
//        {
//            mkdir($etcetera_data_accumulation_dir_path, 0777, FALSE);
//        }
        // last_day使わないならチェーンメソッドにしようぜ。
        return $this;
//        return $last_day;
    }

    public function copyCsvFile(/* $monthly_id, $usb_path, $accumulation_dir_path */) {
        $monthly_id            = $this->monthly_id;
        $usb_path              = $this->directory_path . '/temp'; // TODO: tosite->kawanishi USBじゃないよね．．．
//        $usb_path              = $this->directory_path;
        $accumulation_dir_path = $this->directory_path;
//        $file_path_list        = glob($usb_path . "/*.csv");
//        $file_list             = [];
        $file_lists            = $this->getCsvFileList($usb_path);

        foreach ($file_lists as $f) {
            $src = $usb_path . '/' . $f['csv_file_name'];
//            $src = $usb_path . '/temp/' . $f['csv_file_name']; // こっちでも可

            if ($f['cycle'] == 'D')
            {
                $day  = date('d', strtotime($f['csv_file_set_on']));
                $dist = $accumulation_dir_path . "/daily/" . $monthly_id . "/" . $day . "/" . $f['csv_file_name'];
                copy($src, $dist);
            }
            elseif ($f['cycle'] == 'M')
            {
                $monthly_path = date('Ym', strtotime($f['csv_file_set_on'] . ' -1 month'));
                $dist         = $accumulation_dir_path . "/monthly/" . $monthly_path . "/" . $f['csv_file_name'];
                copy($src, $dist);
            }
            else
            {
                $dist = $accumulation_dir_path . "/exclude_files/" . $monthly_path . "/" . $f['csv_file_name'];
                copy($src, $dist);
            }
        }
        return $this;
//        $befoer_monthly_id     = date("Ym", strtotime($monthly_id . '01 -1 month'));
//        foreach ($file_path_list as $file_path) {
//            $tmpAry      = explode("/", $file_path);
//            $tmpNum      = count($tmpAry) - 1;
//            $file_list[] = $tmpAry[$tmpNum];
//        }
//        foreach ($file_list as $file_name) {
//            if (mb_substr($file_name, 8, 1) == "D")
//            {
////                $day = mb_substr($file_name, 20, 2);
//                $day = date('d', $file_name['']);
//                copy($usb_path . "/" . $file_name, $accumulation_dir_path . "/daily/" . $monthly_id . "/" . $day . "/" . $file_name);
//            }
//            elseif (mb_substr($file_name, 8, 1) == "M")
//            {
//                copy($usb_path . "/" . $file_name, $accumulation_dir_path . "/monthly/" . $befoer_monthly_id . "/" . $file_name);
//            }
//            else
//            {
//                copy($usb_path . "/" . $file_name, $accumulation_dir_path . "/" . $monthly_id . "/" . $file_name);
//            }
//        }
//        return $file_list;
    }

    public function getCsvFileList($directory_path) {
        if (!file_exists($directory_path))
        {
            throw new \Exception('存在しないファイルパスが指定されました。');
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

    public function tableTemplateCreation(/* $monthly_id */) {
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

    public function registrationCsvFileToDatabase(/* $file_list, $monthly_id */) {

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
//        var_dump($file_lists);

        \DB::connection('mysql_suisin')->transaction(function() use($file_lists, $monthly_id) {
            $rows = \App\ZenonMonthlyStatus::month($monthly_id)
                    ->join("zenon_data_csv_files", "zenon_data_monthly_process_status.zenon_data_csv_file_id", "=", "zenon_data_csv_files.id")
                    ->get()
            ;
            foreach ($rows as $r) {
                if (isset($file_lists[$r->identifier]))
                {
                    $f = $file_lists[$r->identifier];

                    $r->csv_file_name   = $f['csv_file_name'];
                    $r->csv_file_set_on = $f['csv_file_set_on'];
                    $r->is_exist        = (int) true;
                    $r->file_kb_size    = $f['kb_size'];
                    $r->save();
                }
            }
//            $not_exsit_file_list  = array();
//            $not_exist_file_list  = [];
//            foreach ($file_lists as $file) {
////                $is_exsit = 0;
//                $is_exist = 0;
//                if ($file['cycle'] == "M")
//                {
//                    $last_month = date("Ym", strtotime($file['csv_file_set_on'] . "-1 month"));
//
//                    if ($last_month == $monthly_id)
//                    {
//                        foreach ($zenon_data_csv_files as $zenon_data_csv_file) {
//                            if ($zenon_data_csv_file->identifier == $file["identifier"])
//                            {
//                                $is_exsit = 1;
//                                \App\ZenonMonthlyStatus::where("id", $zenon_data_csv_file->id)->update(
//                                        ["csv_file_name" => $file["csv_file_name"]], ["csv_file_set_on" => $file["csv_file_set_on"]], ["identifier" => $file["identifier"]],
//                                        // ["kb_size" => $file["kb_size"]],
//                                        ["file_create_time" => $file["file_create_time"]]
//                                );
//                            }
//                        }
//                        if ($is_exsit == 0)
//                        {
//                            $not_exsit_file_list[] = $file["identifier"];
//                        }
//                    }
//                }
//            }
//            return $not_exsit_file_list;
        });
    }

}
