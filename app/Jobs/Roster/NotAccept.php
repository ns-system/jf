<?php

namespace App\Jobs\Roster;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ImportZenonDataService;
use \App\Services\Traits\MemoryCheckable;

class NotAccept extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        MemoryCheckable;

    protected $roster_id;

    public function __construct(int $roster_id)
    {
        $this->roster_id = $roster_id;
    }

    public function failed()
    {
        // ジョブが失敗した時に呼び出される…
        echo "[failed : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

    public function handle()
    {
        echo "==== AcceptEmail ====" . PHP_EOL;
        echo "[start : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;

        $params = [
            'rosters.id',
            'chief_users.email as chief_email',
            'concat(chief_users.last_name," ",chief_users.first_name) as chief_name',
            'users.email',
            'concat(users.last_name," ",users.first_name) as name',
            'rosters.entered_on',
            'rosters.month_id',
            'sinren_users.division_id',
            'sinren_divisions.division_name',
            'case' .
            '    when rosters.is_plan_entry = true and rosters.is_plan_reject = true then "却下" ' .
            '    when rosters.is_plan_entry = true and rosters.is_plan_accept = true then "承認済み" ' .
            '    when rosters.is_plan_entry then "未承認"' .
            '    else "未入力" ' .
            'end as plan',
            'case' .
            '    when rosters.is_actual_entry = true and rosters.is_actual_reject = true then "却下" ' .
            '    when rosters.is_actual_entry = true and rosters.is_actual_accept = true then "承認済み" ' .
            '    when rosters.is_actual_entry then "未承認"' .
            '    else "未入力" ' .
            'end as actual',
        ];
        $query  = \App\Roster::join('sinren_db.sinren_users', 'sinren_users.user_id', '=', 'rosters.user_id')
            ->join('sinren_db.control_divisions', 'control_divisions.division_id', '=', 'sinren_users.division_id')
            ->join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
            ->join('laravel_db.users as chief_users', 'chief_users.id', '=', 'control_divisions.user_id')
            ->join('roster_db.roster_users', 'chief_users.id', '=', 'roster_users.user_id')
            ->join('laravel_db.users', 'users.id', '=', 'rosters.user_id')
            ->where('rosters.id', $this->roster_id)
            ->where('users.retirement', false)
            ->whereRaw('(roster_users.is_chief = true or (roster_users.is_proxy = true and roster_users.is_proxy_active = true))');


        foreach ($params as $param) {
            $query->addSelect(\DB::raw($param));
        }
        $rows = $query->get();

        $emails = [];
        $names  = [];
        foreach ($rows as $key => $r) {
            if ($key === 0) {
                $emails['owner'] = $r->email;
                $names[]         = $r->name;
            }
            $emails[] = $r->chief_email;
            $names[]  = $r->chief_name;
        }

        \Log::debug([$emails, $names]);

        \Mail::send('emails.roster.not_accept', ['row' => $rows[0], 'names' => $names], function ($message) use ($emails) {
            foreach ($emails as $key => $email) {
                if ($key === 'owner') {
                    $message->to($email);
                } else {
                    $message->cc($email);
                }
                $message->subject('［総務課より］勤怠データを入力・承認してください');
            }
        });


//        try {
//            $users = \App\User::join('roster_db.roster_users as RS', 'users.id', '=', 'RS.user_id')
//                    ->where('RS.is_administrator', '=', true)
//                    ->get()
//            ;
//        } catch (\Exception $exc) {
//            echo $exc->getMessage();
//        }
//
//
//        try {
//            foreach ($users as $user) {
//                $email = $user->email;
////                $email = 'n.teshima@jf-nssinren.or.jp';
//                \Mail::send('emails.roster.chief_edit', ['res' => $this->results], function($message) use($email) {
//                    $message->to($email)
//                            ->subject("ユーザー情報が変更されました。")
//                    ;
//                    echo "  -- メール送信先：{$email}" . PHP_EOL;
//                });
//            }
//        } catch (\Exception $e) {
//            echo $e->getMessage();
//            $this->sendErrorMessage($e, $email);
//            exit();
//        }

        echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
    }

}
