<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DummyDatabase extends Command
{

//    use \App\Services\Traits\JsonUsable;
//    use \App\Console\Commands\Traits\DatabaseNameUsable;

    protected $signature = 'db:dummy';

//    protected $description = 'データベースそのものを作成。引数指定で作成するデータベースの指定が可能。設定ファイルはconfig/database.phpを使用。（driver="mysql"のものに限る）';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Make Database Connection
        if (env('APP_ENV') == 'product') {
            $this->error('本番環境で実行することはできません。');
            return;
        }

        $this->info('== ユーザーテーブル処理開始 ==');
        $users = \App\User::get();
        foreach ($users as $i => $user) {
            $user->first_name         = "ユーザー{$i}";
            $user->first_name_kana    = "ゆーざー{$i}";
            $user->last_name          = 'サンプル';
            $user->last_name_kana     = 'さんぷる';
            $user->unencrypt_password = 'password';
            $user->password           = bcrypt('password');
            $user->email              = "user{$i}@example.com";
            $user->save();
        }

        $this->info('== 部署処理開始 ==');
        $divisions = \App\Division::get();
        foreach ($divisions as $i => $division) {
            $division->division_name = "部署{$i}";
            $division->save();
        }
        $this->info('== 勤怠ユーザー処理開始 ==');
        $roster_users = \App\RosterUser::get();
        foreach ($roster_users as $i => $roster_user) {
            $roster_user->staff_number = 1000 + $i;
            $roster_user->save();
        }

        $this->info('== 勤怠データ処理開始 ==');
        $rosters = \App\Roster::whereRaw("plan_overtime_reason != ''")->orWhereRaw("actual_overtime_reason != ''")->get();
//        dd($rosters);
        foreach ($rosters as $i => $roster) {
            if (!empty($roster->plan_overtime_reason)) $roster->plan_overtime_reason = "予定残業理由{$i}";
            if (!empty($roster->actual_overtime_reason)) $roster->actual_overtime_reason = "実績残業理由{$i}";
            $roster->save();
        }

        $this->info("処理は正常に終了しました。");
    }

}
