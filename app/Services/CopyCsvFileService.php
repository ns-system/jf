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

    public function accumulationFileCreation($monthly_id, $accumulation_dir_path) {
        $serial                                   = strtotime($monthly_id . '01');
        $last_day                                 = date('t', $serial);
        $befoer_monthly_id                        = date("Ym", strtotime($monthly_id . '01 -1 month'));
        $monthlydata_accumulation_dir_path        = $accumulation_dir_path . "/monthly/" . $monthly_id;
        $daylydata_accumulation_dir_path          = $accumulation_dir_path . "/daily/" . $monthly_id;
        $etceteradata_accumulation_dir_path       = $accumulation_dir_path . "/etcetera/" . $monthly_id;
        $befoer_monthlydata_accumulation_dir_path = $accumulation_dir_path . "/monthly/" . $befoer_monthly_id;
        if (!(file_exists($accumulation_dir_path)))
        {
            mkdir($accumulation_dir_path, 0777, FALSE);
        }
        if (!(file_exists($monthlydata_accumulation_dir_path)))
        {
            mkdir($monthlydata_accumulation_dir_path, 0777, FALSE);
        }
        if (!(file_exists($befoer_monthlydata_accumulation_dir_path)))
        {
            mkdir($befoer_monthlydata_accumulation_dir_path, 0777, FALSE);
        }
        if (!(file_exists($daylydata_accumulation_dir_path)))
        {
            mkdir($daylydata_accumulation_dir_path, 0777, FALSE);
        }
        if (!(file_exists($etceteradata_accumulation_dir_path)))
        {
            mkdir($etceteradata_accumulation_dir_path, 0777, FALSE);
            for ($i = 1; $i <= $last_day; $i++) {
                $day = sprintf('%02d', (int) $i);
                mkdir($daylydata_accumulation_dir_path . "/" . $day, 0777, FALSE);
            }
        }

        return $last_day;
    }

    public function copyCsvFile($monthly_id, $usb_path, $accumulation_dir_path) {
        $file_path_list    = glob($usb_path . "/*.csv");
        $file_list         = [];
        $befoer_monthly_id = date("Ym", strtotime($monthly_id . '01 -1 month'));
        foreach ($file_path_list as $file_path) {
            $tmpAry      = explode("/", $file_path);
            $tmpNum      = count($tmpAry) - 1;
            $file_list[] = $tmpAry[$tmpNum];
        }
        foreach ($file_list as $file_name) {
            if (mb_substr($file_name, 8, 1) == "D")
            {
                $day = mb_substr($file_name, 20, 2);
                copy($usb_path . "/" . $file_name, $accumulation_dir_path . "/daily/" . $monthly_id . "/" . $day . "/" . $file_name);
            } elseif (mb_substr($file_name, 8, 1) == "M")
            {
                copy($usb_path . "/" . $file_name, $accumulation_dir_path . "/monthly/" . $befoer_monthly_id . "/" . $file_name);
            } else
            {
                copy($usb_path . "/" . $file_name, $accumulation_dir_path . "/" . $monthly_id . "/" . $file_name);
            }
        }
        return $file_list;
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
                } else
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

    public function tableTemplateCreation($monthly_id) {
        $zenon_data_csv_files = \App\ZenonCsv::all();
        \DB::connection('mysql_suisin')->transaction(function() use($zenon_data_csv_files, $monthly_id) {
            foreach ($zenon_data_csv_files as $zenon_data_csv_file) {

                $process_status = \App\ZenonMonthlyStatus::firstOrNew(['monthly_id' => $monthly_id, 'zenon_data_csv_file_id' => $zenon_data_csv_file->id]);
                $process_status->save();
            }
        });
    }

    public function registrationCsvFileToDb($file_list, $monthly_id) {

        \DB::connection('mysql_suisin')->transaction(function() use($file_list, $monthly_id) {
            $zenon_data_csv_files = \App\ZenonMonthlyStatus::join("zenon_data_csv_files", "zenon_data_monthly_process_status.zenon_data_csv_file_id", "=", "zenon_data_csv_files.id")
                    ->where("monthly_id", $monthly_id)
                    ->get();
            $not_exsit_file_list  = array();
            foreach ($file_list as $file) {
                $is_exsit = 0;
                if ($file['cycle'] == "M")
                {
                    $last_month = date("Ym", strtotime($file['csv_file_set_on'] . "-1 month"));
                 
                    if ($last_month == $monthly_id)
                    {
                        foreach ($zenon_data_csv_files as $zenon_data_csv_file) {
                            if ($zenon_data_csv_file->identifier == $file["identifier"])
                            {
                                $is_exsit = 1;
                                \App\ZenonMonthlyStatus::where("id", $zenon_data_csv_file->id)->update(
                                        ["csv_file_name" => $file["csv_file_name"]], ["csv_file_set_on" => $file["csv_file_set_on"]], ["identifier" => $file["identifier"]],
                                        // ["kb_size" => $file["kb_size"]],
                                        ["file_create_time" => $file["file_create_time"]]
                                );
                            }
                        }
                        if ($is_exsit == 0)
                        {
                            $not_exsit_file_list[] = $file["identifier"];
                        }
                    }
                }
            }
            return $not_exsit_file_list;
        });
    }

}
