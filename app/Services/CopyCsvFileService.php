<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//なぜサービスでテストケースを利用しようと思ったのか。セキュリティ的にあうあう。
//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

namespace App\Services;

use \App\Services\Traits\JsonUsable;
use \App\Services\Traits\DateUsable;

/**
 * Description of ImportConfigService
 *
 * @author r-kawanishi
 */
//use DatabaseMigrations;

class CopyCsvFileService
{

    use JsonUsable,
        DateUsable;

    protected $monthly_id;
    protected $directory_path;
    protected $ignore_file;
    protected $directorys = [
        'temp'    => 'temp',
        'log'     => '',
        'monthly' => 'monthly',
        'daily'   => 'daily',
        'ignore'  => 'ignore',
        'weekly'  => 'weekly',
        'times'   => 'times' /** 随時ファイルを指す* */
    ];

    /**
     * 月別IDをセットするメソッド。
     * @param mixed $monthly_id : String型 or Integer型。年＋月の6桁。
     * @return      $this       : チェーンメソッド。
     * @throws      \Exception  : DataUsableトレイトのisDate関数で判定。日付でなければエラー。
     */
    public function setMonthlyId($monthly_id) {
        if (!$this->isDate($monthly_id))
        {
            throw new \Exception("月別IDの指定が誤っているようです。（値：{$monthly_id}）");
        }
        $this->monthly_id = $monthly_id;
        return $this;
    }

    /**
     * ベースとなるパス（ファイルサーバー上の共有フォルダを想定）をセットするメソッド。
     * @param string $base_path : ベースとなるパスを指定。
     * @return $this            : チェーンメソッド。
     * @throws \Exception       : ベースパスあるいはベースパス内累積ディレクトリがなければエラー。
     */
    public function setDirectoryPath(string $base_path) {
        if (!file_exists($base_path))
        {
            throw new \Exception("存在しないファイルパスが指定されました。（マウント先：{$base_path}）");
        }
        foreach ($this->directorys as $d) {
            $tmp_dir = $base_path . '/' . $d;

            if (!file_exists($tmp_dir))
            {
                throw new \Exception("格納先ファイルパスが存在しません。（格納先ファイル：{$tmp_dir}）");
            }
        }
        $this->directory_path = $base_path;
        return $this;
    }

    public function getIgnoreList () {
        return $this->ignore_file;
    }
    private function createDirectory($path) {
        if (!file_exists($path))
        {
            exec("mkdir -m 777 -p {$path}");
//            exec("sudo mkdir -m=777 {$path}");
            return true;
        }
        return false;
    }

//データベース登録時に吐いてたイグノアログをコピー時吐くように
    public function copyCsvFile() {
//        $monthly_id            = $this->monthly_id;
        $temp_file_path        = $this->directory_path . "/" . $this->directorys['temp'];
        $accumulation_dir_path = $this->directory_path;
//        $is_csv_file_exist     = false;
        $not_monhtly_file      = [];
        try {
            $file_lists        = $this->getCsvFileList($temp_file_path, null, true);
            $is_csv_file_exist = TRUE;
        } catch (\Exception $ex) {
            $is_csv_file_exist = FALSE;
        }

        if ($is_csv_file_exist === TRUE)
        {
            foreach ($file_lists as $f) {
                $src       = $temp_file_path . '/' . $f['csv_file_name'];
                // $daily_dir = $accumulation_dir_path . "/" . $this->directorys['daily'] . "/" . $f['monthly_id'];
                // $this->createDirectory($daily_dir);
                // var_dump(file_exists($f['destination']));
                $this->createDirectory($f["destination"]);
                $dest      = $f["destination"] . "/" . $f["csv_file_name"];
                if ($f["cycle"] != 'M')
                {
                    $not_monhtly_file[] = $f;
                }
                exec("cp -f -p {$src} {$dest}");
//            exec("sudo cp -f -p {$src} {$dest}");
            }
        }
        $this->ignore_file = $not_monhtly_file;
        return $this;
    }

    /**
     * 指定したファイルパス内のファイルリストを返すメソッド。
     * @param mixed $option_directory_path  : 調べたいディレクトリパスを渡す。渡さなかった場合、ベースパス内一時ファイル保存先を見に行く。
     * @param mixed $option_base_path       : destinationを生成するためだけに使用。null指定するためには型をmixedにするしかなかったのよ...。
     * @param bool $is_log_export           : 処理ログを出力したいときにtrue指定。
     * @return array                        : 配列で返す。
     *                                            destination      : (string)  コピー先。
     *                                            csv_file_name    : (string)  CSVファイル名。
     *                                            monthly_id       : (string)  月別ID。
     *                                            cycle            : (string)  サイクル一文字。例：M/D/Tなど
     *                                            csv_file_set_on  : (string)  ファイルがセットされた日。ファイル名から取得している。Y-m-dの形。
     *                                            identifier       : (string)  識別子。ファイル名から取得。例：M0003など
     *                                            kb_size          : (double)  キロバイトサイズ。
     *                                            file_create_time : (integer) ファイルが作成された日。デートシリアル型。
     * @throws \Exception                   : ファイルパスが見つからなかった場合にエラー。
     * 警告 : ちょっと臭い。なぜなら、destinationを生成するためだけにbase_pathが必要だからだ。
     *        その部分だけ別クラス化してもいいかも。あるいはファイル操作トレイトを別で作るか。
     */
    public function getCsvFileList($option_directory_path = '', $option_base_path = '', bool $is_log_export = false): array {
        $directory_path = (empty($option_directory_path)) ? $this->directory_path . '/' . $this->directorys['temp'] : $option_directory_path;
        $base_path      = (empty($option_base_path)) ? $this->directory_path : $this->setDirectoryPath($option_base_path)->directory_path;
        if (!file_exists($directory_path))
        {
            throw new \Exception("ファイルパスが存在しません。（ファイルパス：{$directory_path}）");
        }
        $tmp_lists               = scandir($directory_path);
        $lists                   = [];
        $not_execution_file_list = [];

        foreach ($tmp_lists as $t) {
            $file_info = pathinfo($t);
            $file_path = $directory_path . '/' . $t;
            $path      = $directory_path;
//            $base_path = $this->directory_path;
            if (empty($file_info['extension']) || $file_info['extension'] !== 'csv')
            {
                $not_execution_file_list[] = [$file_path, 'The extension is not csv.'];
                continue;
            }
            $date_text = mb_substr($t, 14, 8);
            if (!$date_text || !$this->isDate($date_text))
            {
                $not_execution_file_list[] = [$file_path, 'File name does not contain date type.'];
                continue;
            }
            $date    = date('Y-m-d', strtotime($date_text));
            $monthly = date('Ym', strtotime($date_text));
            $daily   = date('d', strtotime($date_text));

            if (mb_substr($t, 8, 1) == 'D')
            {
                $path = "{$base_path}/{$this->directorys['daily']}/{$monthly}/{$daily}";
            }
            elseif (mb_substr($t, 8, 1) == 'M')
            {
                // 月初に還元されたデータは前月末扱いとなるので、月から-1する
                $before_month = date('Ym', strtotime($date_text . "-1 month"));
                $path         = "{$base_path}/{$this->directorys['monthly']}/{$before_month}";
            }
            elseif (mb_substr($t, 8, 1) == 'W')
            {
                $path = "{$base_path}/{$this->directorys['weekly']}/{$monthly}";
            } elseif (mb_substr($t, 8, 1) == 'T')
            {
                $path = "{$base_path}/{$this->directorys['times']}/{$monthly}";
            } else
            {
                $path = "{$base_path}/{$this->directorys['ignore']}/{$monthly}";
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
        array_multisort(array_column($lists, 'identifier'), $lists);
        if ($is_log_export)
        {
            $this->outputForJsonFile($not_execution_file_list, storage_path() . '/jsonlogs', date('Ym') . '_not_execution_file_list.json');
        }
        return $lists;
    }

    public function tableTemplateCreation($option_csv_template_object = null, $opt_monthly_id = '') {
        $monthly_id          = (empty($opt_monthly_id)) ? $this->monthly_id : $opt_monthly_id;
        $csv_template_object = (empty($option_csv_template_object)) ? \App\ZenonCsv::all() : $option_csv_template_object;
        \DB::connection('mysql_suisin')->transaction(function() use($csv_template_object, $monthly_id) {
            foreach ($csv_template_object as $zenon_data_csv_file) {
                $process_status = \App\ZenonMonthlyStatus::firstOrCreate/* New -> Createへ */(['monthly_id' => $monthly_id, 'zenon_data_csv_file_id' => $zenon_data_csv_file->id]);
                $process_status->save();
            }
        });
        return $this;
    }

    public function registrationCsvFileToDatabase() {
        $file_lists = [];
        // $date     = date('Y-m-d', strtotime($this->monthly_id . '01'));
        // $monthly_id = date('Ym', strtotime($date . '-1 month'));
        $monthly_id = $this->monthly_id;
//        $tmp_file_lists            = $this->getCsvFileList($this->directory_path . "/" . $this->directorys['temp'] . "/");
//        $target_monyhly_file_lists = $this->getCsvFileList($this->directory_path . "/" . $this->directorys['monthly'] . "/" . $monthly_id . "/");
        $path       = $this->directory_path . "/" . $this->directorys['monthly'] . "/" . $monthly_id . "/";
        $target_monyhly_file_lists = $this->getCsvFileList($path);
        foreach ($target_monyhly_file_lists as $l) {
            $file_lists[$l['identifier']] = $l;
        }

        $csv_file_masters = \App\ZenonCsv::Where(function($query) use ($file_lists) {
                    foreach ($file_lists as $f) {
                        $query->orWhere('identifier', '=', $f['identifier']);
                    }
                })
                ->select(['id', 'identifier'])
                ->get()
        ;

//        $not_exist_file_list_json_name = $monthly_id . "_not_exist_file_list" . ".json";

        $not_exist_file_list = \DB::connection('mysql_suisin')->transaction(function() use($file_lists, $monthly_id, $csv_file_masters) {
            $not_exist_file_list = $file_lists;
            foreach ($csv_file_masters as $mst) {
                $monthly_status = \App\ZenonMonthlyStatus::month($monthly_id)->where('zenon_data_csv_file_id', '=', $mst->id)->first();
                $file           = $file_lists[$mst->identifier];

//                }
                // identifier+monthly_idを指定してオブジェクトが生成できた場合
                //     -> DBに存在しているため、全てのファイルリストから生成できたCSVファイルデータを取り除く
                //        残ったものがnot_exist_file_listとなる
                unset($not_exist_file_list[$mst->identifier]);
                $monthly_status->is_exist        = true;
                $monthly_status->csv_file_name   = $file['csv_file_name'];
                $monthly_status->file_path       = $file['destination'];
                $monthly_status->csv_file_set_on = $file['csv_file_set_on'];
                $monthly_status->is_exist        = (int) true;
                $monthly_status->file_kb_size    = $file['kb_size'];
                $monthly_status->save();
//                var_dump($monthly_status->id . ' - ' . $monthly_status->zenon_data_csv_file_id . ' - ' . $monthly_status->csv_file_name);
            }
            return $not_exist_file_list;
        });
        return [
            'ignore'    => $this->ignore_file,
            'not_exist' => $not_exist_file_list,
        ];
    }

    public function tempFileErase() {

        $tmp_file_lists = glob($this->directory_path . "/" . $this->directorys['temp'] . "/*");
        foreach ($tmp_file_lists as $l) {
            exec("rm -rf {$l}");
//            exec("sudo rm -rf {$l}");
        }
    }

}
