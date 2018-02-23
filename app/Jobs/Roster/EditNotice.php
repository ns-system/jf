<?php

namespace App\Jobs\Roster;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ImportZenonDataService;
use \App\Services\Traits\MemoryCheckable;

class EditNotice extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        MemoryCheckable
    ;

    protected $results;

    public function __construct($results) {
        $this->results = $results;
    }

    public function failed() {
        // ジョブが失敗した時に呼び出される…
        echo "[failed : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

    public function handle() {
        echo "==== EditNotice ====" . PHP_EOL;
        echo "[start : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;


        try {
            $users = \App\User::join('roster_db.roster_users as RS', 'users.id', '=', 'RS.user_id')
                    ->where('RS.is_administrator', '=', true)
                    ->get()
            ;
        } catch (\Exception $exc) {
            echo $exc->getMessage();
        }


        try {
            foreach ($users as $user) {
                $email = $user->email;
//                $email = 'n.teshima@jf-nssinren.or.jp';
                \Mail::send('emails.roster.chief_edit', ['res' => $this->results], function($message) use($email) {
                    $message->to($email)
                            ->subject("ユーザー情報が変更されました。")
                    ;
                    echo "  -- メール送信先：{$email}" . PHP_EOL;
                });
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->sendErrorMessage($e, $email);
            exit();
        }

        echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

}
