<?php

namespace App\Jobs\Suisin;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ImportZenonDataService;
use \App\Services\Traits\MemoryCheckable;

class TableDelete extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        MemoryCheckable
    ;

    protected $table_ids;
    protected $email;
    protected $is_monthly_select;
    protected $is_email_send;

    public function __construct($table_ids, $email, $is_monthly_select = true, $is_email_send = true) {
        $this->table_ids         = $table_ids;
        $this->email             = $email;
        $this->is_monthly_select = $is_monthly_select;
        $this->is_email_send     = $is_email_send;
    }

    public function failed() {
        // ジョブが失敗した時に呼び出される…
        echo "[failed : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

    public function handle() {
        echo "==== TableDelete ====" . PHP_EOL;
        echo "[start : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;

        $results = [];
        foreach ($this->table_ids as $id) {
            $table = \App\ZenonMonthlyStatus::join('suisin_db.zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                    ->where('zenon_data_monthly_process_status.id', '=', $id)
                    ->first()
            ;
            if(empty($table) || !$table->exists())
            {
                continue;
            }
            $tmp   = \DB::connection('mysql_zenon')->transaction(function () use($table) {
                $db = \DB::connection('mysql_zenon')->table($table->table_name);
                if (!$this->is_monthly_select)
                {
                    $count = $db->count();
                    $db->truncate();
                }
                else
                {
                    $db    = $db->where('monthly_id', '=', $table->monthly_id);
                    $count = $db->count();
                    $db->delete();
                }
                echo "  -- {$table->table_name}" . PHP_EOL .
                "       件数：" . number_format($count) . "件" . PHP_EOL .
                "       月別：" . $table->monthly_id . PHP_EOL
                ;
                return [
                    'table_name' => $table->table_name,
                    'jp_name'    => $table->zenon_data_name,
                    'monthly_id' => $table->monthly_id,
                    'count'      => $count,
                ];
            });
            $results[] = $tmp;
        }
        // 月次処理ファイルの削除処理追加
        $ids = $this->table_ids;
        \DB::connection('mysql_zenon')->transaction(function () use($ids) {
            foreach ($ids as $id) {
                \App\ZenonMonthlyStatus::where('zenon_data_monthly_process_status.id', '=', $id)->delete();
            }
        });

        $email = $this->email;
//        $email = 'n.teshima@jf-nssinren.or.jp';
        if ($this->is_email_send)
        {
            \Mail::send('emails.table_delete', ['results' => $results], function($message) use($email) {
                $message->to($email)
                        ->subject("レコードの削除が完了しました")
                ;
            });
            echo "  -- メール送信先：{$email}" . PHP_EOL;
        }
        echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

}
