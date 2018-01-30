<?php

namespace App\Jobs\Suisin;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Traits\MemoryCheckable;
use App\Services\Traits\ErrorMailSendable;
use App\Services\TableEditService;

class MasterUpload extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        MemoryCheckable,
        ErrorMailSendable
    ;

    protected $system;
    protected $category;
    protected $input;
    protected $email;
    protected $is_email_send;
    protected $file_name;
    protected $configs;

    public function __construct($system, $category, $input, $configs, $email, $file_name, $is_email_send = true) {
        $this->system        = $system;
        $this->category      = $category;
        $this->input         = $input;
        $this->email         = $email;
        $this->is_email_send = $is_email_send;
        $this->file_name     = $file_name;
        $this->configs       = $configs;
        $this->editRows();
    }

    public function failed() {
        // ジョブが失敗した時に呼び出される…
        echo "[failed : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

    /**
     * 更新フラグが立っていないものを除外
     */
    private function editRows() {
        $raw_rows = $this->input;
        $rows     = [];
        foreach ($raw_rows as $key => $raw_row) {
            $row = [];
            foreach ($raw_row as $key => $raw_col) {
                foreach ($this->configs['table_columns'] as $cfg) {
                    if ($cfg[1] === $key && $cfg[0] === 1)
                    {
                        $row[$key] = $raw_col;
                    }
                }
            }
            $rows[] = $row;
        }
        $this->input = $rows;
    }

    public function handle() {
        $email = $this->email;
//        $email = 'n.teshima@jf-nssinren.or.jp';
        echo "==== MasterUpload ====" . PHP_EOL;
        echo "[start : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;

        try {
            $service = new TableEditService();
            $service->setHtmlPageGenerateConfigs("App\Services\\{$this->system}CsvConfigService", $this->category);
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->sendErrorMessage($e, $email);
            exit();
        }

        try {
            \DB::connection('mysql_master')->beginTransaction();
            \DB::connection('mysql_suisin')->beginTransaction();
            $cnt = $service->uploadToDatabase($this->input, 'mysql_zenon');
            \DB::connection('mysql_master')->commit();
            \DB::connection('mysql_suisin')->commit();
        } catch (\Exception $e) {
            \DB::connection('mysql_master')->rollback();
            \DB::connection('mysql_suisin')->rollback();
            echo $e->getMessage();
            $this->sendErrorMessage($e, $email);
            exit();
        }
        $results = [
            'table'     => $service->getHtmlPageGenerateParameter()['title'],
            'file_name' => (!empty($this->file_name)) ? $this->file_name : '',
            'counts'    => (!empty($cnt)) ? $cnt : ['insert_count' => 0, 'update_count' => 0],
        ];
        try {
            if ($this->is_email_send)
            {
                \Mail::send('emails.master_import', ['results' => $results], function($message) use($email) {
                    $message->to($email)
                            ->subject("マスタファイルの更新が完了しました")
                    ;
                });
                echo "  -- メール送信先：{$email}" . PHP_EOL;
            }
        } catch (\Exception $exc) {
            echo $exc->getMessage();
        }
        echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

}
