<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateUser extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'テスト用ユーザーの作成を行う（スーパーユーザー・一般ユーザー・勤怠一般ユーザー・勤怠管理ユーザー・勤怠責任者ユーザー・勤怠代理ユーザー）。';

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
        \App\Division::create(['division_id' => 1, 'division_name' => 'sample_division',]);
        \App\WorkType::create(['work_type_id' => 1, 'work_type_name' => 'sample_work_type', 'work_start_time' => '09:00:00', 'work_end_time' => '17:00:00']);

        $user_template = [
            'first_name'         => 'ユーザー',
            'first_name_kana'    => 'ゆーざー',
            'password'           => bcrypt('password'),
            'unencrypt_password' => 'password',
        ];

        $user         = array_merge($user_template, ['last_name' => '一般', 'last_name_kana' => 'いっぱん', 'email' => 'user@sample.com',]);
        $super        = array_merge($user_template, ['last_name' => 'スーパー', 'last_name_kana' => 'すーぱー', 'email' => 'super@sample.com', 'is_super_user' => (int) true]);
        $roster_user  = array_merge($user_template, ['last_name' => '勤怠一般', 'last_name_kana' => 'きんたいいっぱん', 'email' => 'roster_user@sample.com',]);
        $roster_admin = array_merge($user_template, ['last_name' => '勤怠管理', 'last_name_kana' => 'きんたいかんり', 'email' => 'roster_admin@sample.com',]);
        $roster_chief = array_merge($user_template, ['last_name' => '勤怠責任者', 'last_name_kana' => 'きんたいせきにんしゃ', 'email' => 'roster_chief@sample.com',]);

        try {
            $m_user         = factory(\App\User::class)->create($user);
            $m_super        = factory(\App\User::class)->create($super);
            $m_roster_user  = factory(\App\User::class)->create($roster_user);
            $m_roster_admin = factory(\App\User::class)->create($roster_admin);
            $m_roster_chief = factory(\App\User::class)->create($roster_chief);
        } catch (\Exception $exc) {
            $this->error('[ERROR] エラーが発生したため処理を中断します ： ' . $exc->getMessage());
            exit();
        }

        $roster_user_option  = ['user_id' => $m_roster_user->id, 'staff_number' => 3301, 'work_type_id' => 1,];
        $roster_admin_option = ['user_id' => $m_roster_admin->id, 'is_administrator' => (int) true];
        $roster_chief_option = ['user_id' => $m_roster_chief->id, 'staff_number' => 3303, 'work_type_id' => 1, 'is_chief' => (int) true,];

        \App\SinrenUser::create(['user_id' => $m_roster_user->id, 'division_id' => 1,]);
        \App\RosterUser::create($roster_user_option);
        \App\SinrenUser::create(['user_id' => $m_roster_admin->id, 'division_id' => 1,]);
        \App\RosterUser::create($roster_admin_option);
        \App\SinrenUser::create(['user_id' => $m_roster_chief->id, 'division_id' => 1,]);
        \App\RosterUser::create($roster_chief_option);
        \App\ControlDivision::create(['user_id' => $m_roster_chief, 'division_id' => 1]);
        $this->info('ユーザーは正常に作成されました。');
    }

}
