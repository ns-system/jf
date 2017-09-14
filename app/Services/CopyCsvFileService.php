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

    public function getJsonFile($json_path) {
        if (!file_exists($json_path))
        {
            throw new \Exception("ファイルパスが存在しません。（ファイルパス：{$json_path}）");
        }
        $tmp   = file_get_contents($json_path);
        $json  = mb_convert_encoding($tmp, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $array = json_decode($json, true);

        if ($array === null)
        {
            throw new \Exception("ファイル内に設定ファイルが記述されていないようです。（ファイルパス：{$json_path}）");
        }
        return $array;
    }

    public function accumulationFileCreation($monthly_id, $accumulation_dir_path) {
        $serial                             = strtotime($monthly_id . '01');
        $last_day                           = date('t', $serial);
        $monthlydata_accumulation_dir_path  = $accumulation_dir_path . "/monthly/".$monthly_id;
        $daylydata_accumulation_dir_path    = $accumulation_dir_path . "/daily/".$monthly_id;
        $etceteradata_accumulation_dir_path = $accumulation_dir_path . "/etcetera/".$monthly_id;
        
        if (!(file_exists($accumulation_dir_path)))
        {
            mkdir($accumulation_dir_path, 0777, FALSE);
        }
        if (!(file_exists($monthlydata_accumulation_dir_path)))
        {
            mkdir($monthlydata_accumulation_dir_path, 0777, FALSE);
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

    public function copyCsvFile($monthly_id,$usb_path, $accumulation_dir_path) {
        $file_path_list = glob($usb_path . "/*.csv");
        $file_list      = [];
        
        foreach ($file_path_list as $file_path) {
            $tmpAry      = explode("/", $file_path);
            $tmpNum      = count($tmpAry) - 1;
            $file_list[] = $tmpAry[$tmpNum];
        }
        foreach ($file_list as $file_name) {
            if (mb_substr($file_name, 8, 1) == "D")
            {
                $day=mb_substr($file_name,20,2);
                var_dump($day);
                copy($usb_path . "/" . $file_name, $accumulation_dir_path . "/daily/" .$monthly_id."/". $file_name);
            } elseif (mb_substr($file_name, 8, 1) == "M")
            {
                copy($usb_path . "/" . $file_name, $accumulation_dir_path . "/monthly/" .$monthly_id."/". $file_name);
            } else
            {
                copy($usb_path . "/" . $file_name, $accumulation_dir_path . "/" .$monthly_id."/". $file_name);
            }
        }
        return $file_list;
    }
    public function registrationCsvFileToDb($file_list){
        
    }
}
