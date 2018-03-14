<?php

namespace App\Jobs\Suisin;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
//use App\Services\Traits\MemoryCheckable;
use App\Services\Traits\ErrorMailSendable;

//use App\Services\TableEditService;

class TruncateMaster extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        ErrorMailSendable
    ;

    protected $table;
    protected $connection;
    protected $email;
    protected $table_jp;

    public function __construct($connection, $table, $email, $table_jp) {
        $this->connection = $connection;
        $this->table      = $table;
        $this->email      = $email;
        $this->table_jp   = $table_jp;
    }

    public function failed() {
        // ジョブが失敗した時に呼び出される…
        echo "[failed : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

    public function handle() {
        echo "==== TruncateMaster ====" . PHP_EOL;
        echo "[start : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
        $email = $this->email;
        try {
            $results = $this->truncateTable();
            \Mail::send('emails.table_delete', ['results' => $results], function($message) use($email) {
                $message->to($email)
                        ->subject("マスタファイルの削除が完了しました")
                ;
            });
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            $this->sendErrorMessage($e, $email);
            exit();
        }

        echo "  -- メール送信先：{$email}" . PHP_EOL;
        echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

    private function truncateTable() {
        $db    = \DB::connection($this->connection);
        $model = $db->table($this->table);
        $email = $this->email;
//        $email = 'n.teshima@jf-nssinren.or.jp';
        $cnt   = $model->count();
        $db->beginTransaction();
        try {
            $model->truncate();
            $db->commit();
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            $this->sendErrorMessage($e, $email);
            $db->rollBack();
            exit();
        }

        $results = [
            [
                'table_name' => $this->table,
                'jp_name'    => $this->table_jp,
                'monthly_id' => '',
                'count'      => $cnt,
            ]
        ];
        return $results;
    }

}
