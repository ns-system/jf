<?php

namespace App\Services;

//use App\Http\Requests;
use \App\Services\Traits\JsonUsable;

class DailyAndWeeklyFileService
{

    use JsonUsable;

    protected $zenon_path;

    public function __construct() {
        $json             = $this->getJsonFile(config_path(), 'import_config.json');
        $this->zenon_path = $json['csv_folder_path'];
    }

    public function getFileList($daily_or_weekly, $monthly_id) {
        if ($daily_or_weekly === 'daily')
        {
            $this->getDailyList($this->zenon_path . '/' . $daily_or_weekly . '/' . $monthly_id);
        }
        elseif ($daily_or_weekly === 'weekly')
        {
            
        }
        else
        {
            throw new \Exception("存在しないファイル区分が指定されたようです。（区分：{$daily_or_weekly}）");
        }
    }

    public function getDailyList($daily_path) {
        $date_list = $this->getFiles($daily_path);
        $file_list = [];
        foreach ($date_list as $date) {
//            $tmp              = scandir($daily_path . '/' . $date);
            $file_list[$date] = $this->getFiles($daily_path . '/' . $date);
        }
        return ['date_list' => $date_list, 'file_list' => $file_list];
    }

    public function getFiles($file_path) {
        if (!file_exists($file_path))
        {
            throw new \Exception("ファイルパスが見つかりませんでした。（ファイルパス：'{$file_path}'）");
        }
        $tmp_list = scandir($file_path);
        $list     = array_diff($tmp_list, ['.', '..']);
        return $list;
    }

}
