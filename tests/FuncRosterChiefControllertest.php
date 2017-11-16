<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class FuncRosterChiefControllertest extends TestCase
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
    }
    /**
     * @tests
     */
    public function 正常系責任者がユーザーを責任者代理_代理人機能無効状態にできる() {
        $roster_unchanged_user = \App\RosterUser::where('user_id', $this->normal_user->id)->first();
        $this->actingAs($this->admin_user)
                ->visit('/app/roster/chief/')
                ->see("責任者代理設定")
                ->post('/app/roster/chief/update', ['_token' => csrf_token(), 'id' => $roster_unchanged_user->id, 'proxy' => '1', 'active' => '0',])
                ->assertRedirectedTo('/app/roster/chief/')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $this->normal_user->id)->first();
        $this->assertEquals(0, $roster_unchanged_user->is_proxy);
        $this->assertEquals(0, $roster_unchanged_user->is_proxy_active);
        $this->assertEquals(1, $roster_changed_user->is_proxy);
        $this->assertEquals(0, $roster_changed_user->is_proxy_active);
    }

    /**
     * @tests
     */
    public function 正常系責任者がユーザーを責任者代理_代理人機能有効状態にできる() {
        $roster_unchanged_user = \App\RosterUser::where('user_id', $this->normal_user->id)->first();
        $this->actingAs($this->admin_user)
                ->visit('/app/roster/chief/')
                ->see("責任者代理設定")
                ->post('/app/roster/chief/update', ['_token' => csrf_token(), 'id' => $roster_unchanged_user->id, 'proxy' => '1', 'active' => '1',])
                ->assertRedirectedTo('/app/roster/chief/')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $this->normal_user->id)->first();
        $this->assertEquals(0, $roster_unchanged_user->is_proxy);
        $this->assertEquals(0, $roster_unchanged_user->is_proxy_active);
        $this->assertEquals(1, $roster_changed_user->is_proxy);
        $this->assertEquals(1, $roster_changed_user->is_proxy_active);
    }

    /**
     * @tests
     */
    public function 正常系責任者が責任者代理ユーザーを一般ユーザーにできる() {
        $roster_unchanged_user = \App\RosterUser::where('user_id', $this->proxy_user->id)->first();
        $this->actingAs($this->admin_user)
                ->visit('/app/roster/chief/')
                ->see("責任者代理設定")
                ->post('/app/roster/chief/update', ['_token' => csrf_token(), 'id' => $roster_unchanged_user->id, 'proxy' => '0', 'active' => '0',])
                ->assertRedirectedTo('/app/roster/chief/')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $this->normal_user->id)->first();
        $this->assertEquals(1, $roster_unchanged_user->is_proxy);
        $this->assertEquals(1, $roster_unchanged_user->is_proxy_active);
        $this->assertEquals(0, $roster_changed_user->is_proxy);
        $this->assertEquals(0, $roster_changed_user->is_proxy_active);
    }
/**
     * @tests
     */
    public function 異常系権限のないユーザーがユーザーを責任者代理_代理人機能無効状態にしようとするとエラー() {
        \Session::start();
        $roster_unchanged_user = \App\RosterUser::where('user_id', $this->normal_user->id)->first();
        $this->actingAs($this->normal_user)
                ->post('/app/roster/chief/update', ['_token' => csrf_token(), 'id' => $roster_unchanged_user->id, 'proxy' => '1', 'active' => '0',])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 異常系権限のないユーザーがユーザーを責任者代理_代理人機能有効状態にしようとするとエラー() {
        \Session::start();
        $roster_unchanged_user = \App\RosterUser::where('user_id', $this->normal_user->id)->first();
        $this->actingAs($this->normal_user)
                ->post('/app/roster/chief/update', ['_token' => csrf_token(), 'id' => $roster_unchanged_user->id, 'proxy' => '1', 'active' => '1',])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 異常系権限のないユーザーが責任者代理ユーザーを一般ユーザーにしようとするとエラー() {
        \Session::start();
        $roster_unchanged_user = \App\RosterUser::where('user_id', $this->proxy_user->id)->first();
        $this->actingAs($this->normal_user)
                ->post('/app/roster/chief/update', ['_token' => csrf_token(), 'id' => $roster_unchanged_user->id, 'proxy' => '0', 'active' => '0',])
                ->assertRedirectedTo('/permission_error')
        ;
    }

}