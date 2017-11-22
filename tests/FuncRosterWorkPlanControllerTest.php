<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Services\Traits;

class FuncRosterWorkPlanControllerTest extends TestCase
{

    protected static $init                     = false;
    protected $super_user;
    protected $admin_user;
    protected $normal_user;
    protected $proxy_user;
    protected $calender_plan_post_dummy_data   = ["plan_start_hour" => 9, "plan_start_time" => 30, "plan_end_hour" => 17, "plan_end_time" => 30, "plan_overtime_reason" => ""];
    protected $calender_actual_post_dummy_data = ["actual_start_hour" => 9, "actual_start_time" => 30, "actual_end_hour" => 17, "actual_end_time" => 30, "acactual_rest_reason_id" => "0", "actual_work_type_id" => 1, "actual_overtime_reason" => ""];

    public function setUp() {
        parent::setUp();

        if (!static::$init)
        {
            try {

//                \Artisan::call('db:reset', ['--dbenv' => 'testing', '--hide' => 'true']);
//                \Artisan::call('db:create', ['--dbenv' => 'testing', '--hide' => 'true']);
//                \Artisan::call('migrate');
                \App\Division::create(["division_id" => '1', 'division_name' => 'test']);
                \App\WorkType::create(["work_type_id" => '1', "work_type_name" => "テスト用"]);
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
            static::$init = true;
        }
        \App\User::truncate();
        \App\RosterUser::truncate();
        \App\SinrenUser::truncate();
        \App\ControlDivision::truncate();
        \App\Rest::truncate();
        $this->super_user  = factory(\App\User::class)->create(['is_super_user' => '1']);
        $this->admin_user  = factory(\App\User::class)->create();
        $this->normal_user = factory(\App\User::class)->create();
        $this->proxy_user  = factory(\App\User::class)->create();
        \App\RosterUser::create(['user_id' => $this->admin_user->id, "is_administrator" => '0', "is_chief" => '1', "is_proxy" => '0', "is_proxy_active" => '0', "work_type_id" => '1']);
        \App\RosterUser::create(['user_id' => $this->normal_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0', "work_type_id" => '1']);
        \App\RosterUser::create(['user_id' => $this->proxy_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '1', "work_type_id" => '1']);
        \App\SinrenUser::create(['user_id' => $this->admin_user->id, "division_id" => '1']);
        \App\SinrenUser::create(['user_id' => $this->normal_user->id, "division_id" => '1']);
        \App\SinrenUser::create(['user_id' => $this->proxy_user->id, "division_id" => '1']);
        \App\ControlDivision::create(['user_id' => $this->admin_user->id, "division_id" => '1']);
        \App\Rest::create(["rest_reason_id"=>1,"rest_reason_name"=>"テスト用理由"]);
    }


    /**
     * @tests
     */
    public function 正常系責任者が勤務予定データを作成できる() {
        \App\Roster::truncate();
        $this->actingAs($this->admin_user)
                ->visit('/app/roster/work_plan')
                ->see("勤務予定データ作成")
                ->visit('/app/roster/work_plan/201712')
                ->see($this->normal_user->first_name)
                ->see($this->normal_user->last_name)
                ->see($this->proxy_user->first_name)
                ->see($this->proxy_user->last_name)
                ->visit('/app/roster/work_plan/list/201712/' . $this->normal_user->id)
                ->press('更新する')
                ->seePageIs('/app/roster/work_plan/201712')
                ->see("データを更新しました。")
                ->dontSee("成功：要修正")
        ;
    }

    /**
     * @tests
     */
    public function 異常系勤務予定データ作成時日付が入るべきところに日付以外が入るとエラー() {
        \App\Roster::truncate();
        $this->actingAs($this->admin_user)
                ->visit('/app/roster/work_plan')
                ->see("勤務予定データ作成")
                ->visit('/app/roster/work_plan/201712')
                ->see($this->normal_user->first_name)
                ->see($this->normal_user->last_name)
                ->see($this->proxy_user->first_name)
                ->see($this->proxy_user->last_name)
                ->visit('/app/roster/work_plan/list/201712/' . $this->normal_user->id)
                ->post('/app/roster/work_plan/list/edit/201712/'. $this->normal_user->id, ['_token' => csrf_token(), "entered_on" => ['1' => "2017-12-1a"], "work_type" => ['2017-12-01' => 1], "actual_reject" => ["2017-12-01" => "1"]])
                ->assertSessionHas("warn_message", "エラーがあったため処理を中断しました。")
                ;
    }
}
