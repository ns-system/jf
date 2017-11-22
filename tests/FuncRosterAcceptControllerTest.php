<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FuncRosterAcceptControllerTest
 *
 * @author r-kawanishi
 */
class FuncRosterAcceptControllerTest extends TestCase
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

                \Artisan::call('db:reset', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('db:create', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('migrate');
                \App\Division::create(["division_id" => '1', 'division_name' => 'test']);
                \App\WorkType::firstOrCreate(["work_type_id" => '1', "work_type_name" => "テスト用"]);
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
        \App\Rest::create(["rest_reason_id" => 1, "rest_reason_name" => "テスト用理由"]);
    }

    /**
     * @tests
     */
    public function 正常系責任者が勤務予定データを承認できる() {
        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->admin_user)
                ->visit('/app/roster/accept/')
                ->see("2017年12月")
                ->visit('/app/roster/accept/calendar/201712/1')
                ->see("予定データ承認")
                ->post('/app/roster/accept/calendar/edit', ['_token' => csrf_token(), "plan" => [1 => 1], "id" => [1 => 1]])
                ->assertRedirectedTo('/app/roster/accept/calendar/201712/1')
        ;
        $accept_plan   = \App\Roster::where('id', 1)->first();
        $this->assertEquals(0, $unaccept_plan->is_plan_accept);
        $this->assertEquals(1, $accept_plan->is_plan_accept);
    }

    /**
     * @tests
     */
    public function 正常系責任者が勤務予定データを却下できる() {
        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->admin_user)
                ->visit('/app/roster/accept/')
                ->see("2017年12月")
                ->visit('/app/roster/accept/calendar/201712/1')
                ->see("予定データ承認")
                ->post('/app/roster/accept/calendar/edit', ['_token' => csrf_token(), "plan" => ['1' => 0], "id" => ['1' => 1], "plan_reject" => [1 => "却下"]])
                ->assertRedirectedTo('/app/roster/accept/calendar/201712/1')
        ;

        $accept_plan = \App\Roster::where('id', 1)->first();
        $this->assertEquals(0, $unaccept_plan->is_plan_reject);
        $this->assertEquals(1, $accept_plan->is_plan_reject);
    }

    /**
     * @tests
     */
    public function 正常系責任者が勤務実績データを承認できる() {
        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->admin_user)
                ->visit('/app/roster/accept/')
                ->see("2017年12月")
                ->visit('/app/roster/accept/calendar/201712/1')
                ->see("予定データ承認")
                ->post('/app/roster/accept/calendar/edit', ['_token' => csrf_token(), "actual" => [1 => 1], "id" => [1 => 1]])
                ->assertRedirectedTo('/app/roster/accept/calendar/201712/1')
        ;
        $accept_plan   = \App\Roster::where('id', 1)->first();
        $this->assertEquals(0, $unaccept_plan->is_actual_accept);
        $this->assertEquals(1, $accept_plan->is_actual_accept);
    }

    /**
     * @tests
     */
    public function 正常系責任者が勤務実績データを却下できる() {
        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->admin_user)
                ->visit('/app/roster/accept/')
                ->see("2017年12月")
                ->visit('/app/roster/accept/calendar/201712/1')
                ->see("予定データ承認")
                ->post('/app/roster/accept/calendar/edit', ['_token' => csrf_token(), "actual" => ['1' => 0], "id" => ['1' => 1], "actual_reject" => [1 => "却下"]])
                ->assertRedirectedTo('/app/roster/accept/calendar/201712/1')
        ;

        $accept_plan = \App\Roster::where('id', 1)->first();
        $this->assertEquals(0, $unaccept_plan->is_actual_reject);
        $this->assertEquals(1, $accept_plan->is_actual_reject);
    }

    //異常系

    /**
     * @tests
     */
    public function 異常系権限のないユーザーが勤務予定データを作成しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $this->actingAs($this->normal_user)
                ->post('/app/roster/work_plan/list/edit/201712/' . $this->normal_user->id, ['_token' => csrf_token(), "work_type" => ['2017-12-01' => 1, '2017-12-02' => 1], "id" => ['1' => 1], "rest" => ['2017-12-01' => 0, '2017-12-02' => 0], "entered_on" => ['2017-12-01', '2017-12-02']])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 異常系権限のないユーザーが勤務予定データを承認しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        $this->actingAs($this->normal_user)
                ->post('/app/roster/accept/calendar/edit', ['_token' => csrf_token(), "plan" => [1 => 1], "id" => [1 => 1]])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 異常系権限のないユーザーが勤務予定データを却下しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        $this->actingAs($this->normal_user)
                ->post('/app/roster/accept/calendar/edit', ['_token' => csrf_token(), "plan" => ['1' => 0], "id" => ['1' => 1], "plan_reject" => [1 => "却下"]])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 異常系権限のないユーザーが勤務実績データを承認しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $this->actingAs($this->normal_user)
                ->post('/app/roster/accept/calendar/edit', ['_token' => csrf_token(), "actual" => [1 => 1], "id" => [1 => 1]])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 異常系権限のないユーザーが勤務実績データを却下しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }

        $this->actingAs($this->normal_user)
                ->post('/app/roster/accept/calendar/edit', ['_token' => csrf_token(), "actual" => ['1' => 0], "id" => ['1' => 1], "actual_reject" => [1 => "却下"]])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    

}
