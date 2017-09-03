<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportZenonDataService;

class ImportCsv extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        try {
            $obj  = new ImportZenonDataService();
            $json = $obj->getJsonFile(config_path() . '/import_config.json');

            echo $this->e("+=======================================================") . PHP_EOL;
            echo $this->e("|") . PHP_EOL;
            echo $this->e("|  設定ファイル：" . config_path() . '/import_config.json') . PHP_EOL;
            echo $this->e("|  CSV累積先 　：{$json['csv_folder_path']}") . PHP_EOL;
            echo $this->e("|") . PHP_EOL;
            echo $this->e("+=======================================================") . PHP_EOL;

            $monthly_id = $this->inputMonth();

            $past_month = date('n月t日', strtotime($monthly_id . '01'));
            $file_path  = $json['csv_folder_path'] . "/{$monthly_id}";
            $this->question($this->e("CSVファイル累積先：{$file_path}"));
            if (!file_exists($file_path))
            {
                throw new \Exception("CSVファイル累積先が存在しないようです。（ファイルパス：{$file_path}）");
            }
            if (\App\ZenonType::get() == null)
            {
                throw new \Exception("全オン側還元CSVファイル設定が登録されていません。");
            }
            if (\App\ZenonTable::get() == null)
            {
                throw new \Exception("MySQL側 全オンテーブル設定が登録されていません。");
            }
            if (\App\ZenonStatus::where('monthly_id', '=', $monthly_id) == null)
            {
                throw new \Exception("CSVファイルが解凍されていないようです。先に解凍処理を行ってください。");
            }

            $msg = "月別ID：{$monthly_id}（" . date("n月j日", strtotime($monthly_id . '01')) . "～{$past_month}時点データ）この内容で処理してよろしいですか？";
            $this->exitConfirm($this->e($msg));

            $is_month_exist = \App\Month::where('monthly_id', '=', $monthly_id)->exists();
            if (!$is_month_exist)
            {
                $month_model               = new \App\Month();
                $month_model->monthly_id   = $monthly_id;
                $month_model->displayed_on = date('y-m-d', strtotime($monthly_id . '01'));
                $month_model->save();
            }
        } catch (\Exception $e) {
            $this->error($this->e($e->getMessage()));
            echo $e->getTraceAsString();
            exit();
        }

        $this->info($this->e("［処理1：事前データチェック］.......開始 " . date("H:i:s")));
        try {
            $csv_configs = new \App\ZenonStatus();
            $csv_configs = $csv_configs->joinZenonCsv($monthly_id)->where('is_exist', '=', 1)->get();
            $tables      = [];
            foreach ($csv_configs as $i => $csv_conf) {
                $this->line($this->e($csv_conf->zenon_data_name));

                $csv_file           = $obj->setCsvFile($file_path . '/' . $csv_conf->csv_file_name)->getCsvFile();
                $max_count          = $obj->getMaxRow();
                $p                  = ceil($max_count / 100);
                $tables[$i]['csv']  = $csv_file;
                $tables[$i]['per']  = (int) $p;
                $tables[$i]['conf'] = $csv_conf;

                $this->csvCheck($csv_file, $csv_conf, $tables, $i);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            echo $e->getTraceAsString();
            exit();
        }
        $this->info($this->e("［処理1：事前データチェック］.......終了 " . date("H:i:s")));


        $this->info($this->e("［処理2：データセットアップ］.......開始 " . date("H:i:s")));
        try {
            foreach ($tables as $table) {
                $conf = $table['conf'];
//                var_dump($conf);
//                $per  = $table['per'];
//                $csv  = $table['csv'];

                $db  = \DB::connection('mysql_zenon')->table($conf->table_name);
                $cnt = $this->getCount($db, $conf->is_cumulative, $monthly_id);

                $msg = "[{$conf->zenon_data_name}] '{$monthly_id}'中のデータ件数が0ではありませんが、処理を続行してよろしいですか？（データ件数：{$cnt}）";
                if ($cnt !== 0 && !$this->confirm($this->e($msg)))
                {
                    continue;
                }
                $this->comment($this->e("開始：" . date('H:i:s')));
                $this->insertToDataBase($obj, $table, $monthly_id);
                $this->updateStatus($conf);
                $this->comment($this->e("終了：" . date('H:i:s')));
//                exit();
            }
        } catch (\Exception $e) {
            $this->error($this->e($e->getMessage()));
            echo $e->getTraceAsString();
            exit();
        }

        $this->info($this->e("［処理2：データセットアップ］.......終了 " . date("H:i:s")));

        $this->info($this->e("［処理3：委託者マスタ生成］.........開始 " . date("H:i:s")));
        try {
            $sql        = "consignor_code," .
                    " consignor_name," .
                    " COUNT(*) as total_count," .
                    " MAX(scheduled_transfer_payment_on) as reference_last_traded_on," .
                    " MAX(last_traded_on) as last_traded_on"
            ;
            $consignors = \App\Jifuri::where(['monthly_id' => $monthly_id])
                    ->select(\DB::raw($sql))
                    ->groupBy('consignor_code')
                    ->get()
            ;
            $max        = $consignors->count();
            $per        = ceil($max / 100);

            \DB::beginTransaction();
            $bar = $this->output->createProgressBar(100);
            foreach ($consignors as $i => $cns) {
                $keys      = ['consignor_code' => $cns->consignor_code];
                $table     = \App\Consignor::firstOrNew($keys);
                $last_date = $this->getLastTraded($cns);

                if ($i % $per === 0)
                {
                    $bar->advance();
                }

                $table->consignor_code           = $cns->consignor_code;
                $table->consignor_name           = $cns->consignor_name;
                $table->total_count              = $cns->total_count;
                $table->reference_last_traded_on = $last_date;
                $table->save();
            }
            $bar->finish();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            $this->error($this->e($e->getMessage()));
            echo $e->getTraceAsString();
            exit();
        }
        echo "\n";
        $this->info($this->e("［処理3：委託者マスタ生成］.........開始 " . date("H:i:s")));
    }

    private function getCount($db, $is_cumulative, $monthly_id) {
        if ($is_cumulative == true)
        {
            $db->where(['monthly_id' => $monthly_id]);
        }
        return $db->count();
    }

    private function getLastTraded($row) {
        $date = $row->reference_last_traded_on;
        if (!$date)
        {
            return $row->last_traded_on;
        }
        return $date;
    }

    private function inputMonth() {
        $month = \App\Month::where(['is_current' => (int) true,])->first();
        $month = $month === null ? 'nothing' : $month->monthly_id;

        $msg = "月別IDを次のフォーマットに沿って入力してください[yyyymm]（現在公開されているID：{$month}）";
        while (1) {
//            $ans = $this->ask(mb_convert_encoding($msg, 'sjis', 'utf8'));
            $ans = $this->ask($this->e($msg));
            $yd  = date('Ym', strtotime($ans . '01'));
            if (mb_strlen($ans) === 6 && $yd == true && (int) $ans !== 0)
            {
                break;
            }
            else
            {
                $this->error($this->e("データ形式に誤りがありました。再度入力してください。\n"));
            }
        }
//        return $yd;
        return $ans;
    }

    private function exitConfirm($msg) {
        if (!$this->confirm($msg))
        {
            $this->error($this->e('処理はキャンセルされました。'));
            exit();
        }
    }

    private function csvCheck($csv_file, $csv_conf, $tables, $i) {
        $bar = $this->output->createProgressBar(100);
        foreach ($csv_file as $j => $row) {
            if ($row === [null])
            {
                continue;
            }
            if ($j % $tables[$i]['per'] === 0)
            {
                $bar->advance();
            }
            if (count($row) != $csv_conf->column_length)
            {
                throw new \Exception('カラム長が一致しませんでした。（' . count($row) . ' <=>' . $csv_conf->column_length . '）');
            }
        }
        $bar->finish();
        echo "\n";
    }

    /**
     * insertToDatabase
     * @param type $obj
     * @param type $table
     * @param type $monthly_id
     * @throws \Exception
     */
    private function insertToDataBase($obj, $table, $monthly_id) {

        $conf = $table['conf'];
        $per  = $table['per'];
        $csv  = $table['csv'];

        $this->line($this->e($conf->zenon_data_name));
        $table_column_obj = \App\ZenonTable::where(['zenon_format_id' => $conf->zenon_format_id])->get(['column_name', 'column_type']);

        $table_columns = [];
        $table_types   = [];

        foreach ($table_column_obj as $t) {
            $table_columns[] = $t->column_name;
            $table_types[]   = $t->column_type;
        }

        try {
            \DB::connection('mysql_zenon')->beginTransaction();
            $db  = \DB::connection('mysql_zenon')->table($conf->table_name);
            $bar = $this->output->createProgressBar(100);

            $bulk_rows    = [];
            $bulk_counter = 0;
            foreach ($csv as $i => $row) {
                if ($row === [null])
                {
                    continue;
                }
//                echo $i;

                if ($i % $per == 0)
                {
                    $bar->advance();
                }

                $tmp_bulk    = $obj->setCsvSplitRow($row, $table_columns, $table_types, $conf, $monthly_id);
                $cnt         = count($tmp_bulk);
                $bulk_rows[] = $tmp_bulk;
                // MySQLのバージョンによってはプリペアドステートメントが65536までに制限されているため、動的にしきい値を設ける
                if ($i !== 0 && ($cnt + $bulk_counter) > 65000)
                {
//                    echo "= {$bulk_counter} =";
//                    var_dump($bulk_rows);
                    $db->insert($bulk_rows);
                    $bulk_counter = 0;
                    $bulk_rows    = [];
                }
                else
                {
                    $bulk_counter += $cnt;
                }
            }
            if ($bulk_rows !== [null])
            {
                $db->insert($bulk_rows);
            }
            \DB::connection('mysql_zenon')->commit();
            $bar->finish();
            echo "\n";
        } catch (\Exception $ex) {
            \DB::connection('mysql_zenon')->rollback();
            $msg = "{$conf->table_name}処理中にエラーが発生しました（行番号：{$i}）\n" . $ex->getMessage();
            throw new \Exception($msg);
        }
    }

    private function updateStatus($config) {
        $status            = \App\ZenonStatus::find($config->id);
        $status->is_import = (int) true;
        $status->save();
    }

    private function e($buf) {
//        return mb_convert_encoding($buf, 'sjis', 'utf8');
        return $buf;
    }

}
