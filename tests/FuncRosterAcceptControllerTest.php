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

    use \App\Services\Traits\Testing\DbDisconnectable;

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
                \App\Division::create(["division_id" => '2', 'division_name' => 'test2']);
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

    public function tearDown() {
        $this->disconnect();
        parent::tearDown();
    }

    /**
     * @tests
     */
    public function 正常系_責任者が勤務予定データを承認できる() {

        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "plan" => [1 => 1], "id" => [1 => 1]])
                ->assertRedirectedTo(route_with_query('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id], ['status' => 'all']))
        ;
        $accept_plan   = \App\Roster::where('id', 1)->first();
        $this->assertEquals(0, $unaccept_plan->is_plan_accept);
        $this->assertEquals(1, $accept_plan->is_plan_accept);
    }

    /**
     * @tests
     */
    public function 正常系_責任者が勤務予定データを却下できる() {
        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "plan" => ['1' => 0], "id" => ['1' => 1], "plan_reject" => [1 => "却下"]])
                ->assertRedirectedTo(route_with_query('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id], ['status' => 'all']))
        ;

        $accept_plan = \App\Roster::where('id', 1)->first();
        $this->assertEquals(0, $unaccept_plan->is_plan_reject);
        $this->assertEquals(1, $accept_plan->is_plan_reject);
    }

    /**
     * @tests
     */
    public function 正常系_責任者が勤務実績データを承認できる() {
        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "actual" => [1 => 1], "id" => [1 => 1]])
                ->assertRedirectedTo(route_with_query('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id], ['status' => 'all']))
        ;
        $accept_plan   = \App\Roster::where('id', 1)->first();
        $this->assertEquals(0, $unaccept_plan->is_actual_accept);
        $this->assertEquals(1, $accept_plan->is_actual_accept);
    }

    /**
     * @tests
     */
    public function 正常系_責任者が勤務実績データを却下できる() {
        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "actual" => ['1' => 0], "id" => ['1' => 1], "actual_reject" => [1 => "却下"]])
                ->assertRedirectedTo(route_with_query('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id], ['status' => 'all']))
        ;

        $accept_plan = \App\Roster::where('id', 1)->first();
        $this->assertEquals(0, $unaccept_plan->is_actual_reject);
        $this->assertEquals(1, $accept_plan->is_actual_reject);
    }

    /**
     * @tests
     */
    public function 正常系_勤務実績データの未承認の物を表示できる() {
        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            if ($i == 15)
            {
                \App\Roster::create([
                    'user_id'           => $this->normal_user->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-" . $i,
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 1,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 0]);
            } else
            {
                \App\Roster::create([
                    'user_id'           => $this->normal_user->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-" . $i,
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 1,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 1]);
            }
        }
//        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route_with_query('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id], ['status' => 'part']))
                ->see("勤務データ承認")
                ->see("2017-12-15")
        ;
        for ($j = 1; $j <= 31; $j++) {
            $checkdate="2017\-12\-" . strval($j);
            if ($j != 15)
            {
                $this->actingAs($this->admin_user)
                        ->visit(route_with_query('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id], ['status' => 'part']))
                        ->dontsee(strval($checkdate) );
            }
        }
    }

    /**
     * @tests
     */
    public function 正常系_月毎のレコード件数がNULLの時空配列が返ってくる() {
        $class      = new \App\Http\Controllers\RosterAcceptController;
        $reflection = new \ReflectionClass($class);
        $method     = $reflection->getMethod("editKey");
        $method->setAccessible(true);
        $res        = $method->invoke($class, null);
        $this->assertEmpty($res);
    }

    /**
     * @tests
     */
    public function 正常系_勤務データIDの配列にnullが入っていた場合その部分はスキップして実行される() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $input   = ['_token' => csrf_token(), "actual" => ['1' => 1, "2" => 1], "id" => ['1' => null, "2" => 2]];
        $service = new App\Services\Roster\RosterAccept($this->admin_user->id);
        try {
            $service->updateRoster($input);
            $this->assertTrue(TRUE);
        } catch (\Exception $ex) {
            $this->fail("想定していない例外発生");
        }
    }

    /**
     * @tests
     */
    public function 正常系_勤務実績データの未承認の物だけが表示される() {
        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            if ($i % 2 == 0)
            {
                \App\Roster::create([
                    'user_id'           => $this->normal_user->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-" . $i,
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 0,
                    "is_actual_entry"   => 0,
                    "is_actual_accept"  => 0]);
            } else
            {
                \App\Roster::create([
                    'user_id'           => $this->normal_user->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-" . $i,
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 1,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 1]);
            }
        }
//        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route_with_query('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id], ['status' => 'part']))
                ->see("勤務データ承認")
                
        ;
        for ($j = 1; $j <= 31; $j++) {
             $checkdate="2017\-12\-" . strval($j);
            if ($j % 2 != 0)
            {
                
                $this->dontsee($checkdate);
            }
            if ($j % 2 == 0)
            {
             $this->dontsee($checkdate);
            }
        }
    }

    //異常系_

    /**
     * @tests
     */
    public function 異常系_権限のないユーザーが勤務予定データを承認しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        $this->actingAs($this->normal_user)
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "plan" => [1 => 1], "id" => [1 => 1]])
                ->assertRedirectedTo(route('permission_error'))
        ;
    }

    /**
     * @tests
     */
    public function 異常系_権限のないユーザーが勤務予定データを却下しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        $this->actingAs($this->normal_user)
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "plan" => ['1' => 0], "id" => ['1' => 1], "plan_reject" => [1 => "却下"]])
                ->assertRedirectedTo(route('permission_error'))
        ;
    }

    /**
     * @tests
     */
    public function 異常系_権限のないユーザーが勤務実績データを承認しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $this->actingAs($this->normal_user)
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "actual" => [1 => 1], "id" => [1 => 1]])
                ->assertRedirectedTo(route('permission_error'))
        ;
    }

    /**
     * @tests
     */
    public function 異常系_権限のないユーザーが勤務実績データを却下しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }

        $this->actingAs($this->normal_user)
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->admin_user->id]), ['_token' => csrf_token(), "actual" => ['1' => 0], "id" => ['1' => 1], "actual_reject" => [1 => "却下"]])
                ->assertRedirectedTo(route('permission_error'))
        ;
    }

    /**
     * @tests
     */
    public function 異常系_自分自身を承認しようとするとエラー() {
        \App\Roster::truncate();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->admin_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
//        $unaccept_plan = \App\Roster::where('id', 1)->first();
        $a = $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->admin_user->id]), ['_token' => csrf_token(), "actual" => ['1' => 1], "id" => ['1' => "1"]])
        ;
        $a->assertRedirectedTo(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]));
        $a->assertSessionHas("danger_message", "自分自身のデータを承認することはできません。");
    }

    /**
     * @tests
     */
    public function 異常系_自分の担当以外の部署の予定を承認しようとするとエラー() {
        \App\Roster::truncate();
        $user = factory(\App\User::class)->create();
        \App\RosterUser::create(['user_id' => $user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '1', "work_type_id" => '1']);
        \App\SinrenUser::create(['user_id' => $user->id, "division_id" => '2']);
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $a = $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $user->id]), ['_token' => csrf_token(), "actual" => ['1' => 1], "id" => ['1' => "1"]])
        ;
        $a->assertRedirectedTo(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]));
        $a->assertSessionHas("danger_message", "許可されていない部署のデータを承認しようとしました。");
    }

    /**
     * @tests
     */
    public function 異常系_ユーザーがいないとエラー() {
        \App\Roster::truncate();
        $user = factory(\App\User::class)->create();
        \App\RosterUser::create(['user_id' => $user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '1', "work_type_id" => '1']);
        \App\SinrenUser::create(['user_id' => $user->id, "division_id" => '2']);
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $a = $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => -1]), ['_token' => csrf_token(), "actual" => ['1' => 1], "id" => ['1' => "1"]])
        ;
        $a->assertRedirectedTo(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]));
        $a->assertSessionHas("danger_message", "勤怠管理ユーザーの登録が行われていないようです。");
    }

    /**
     * @tests
     */
    public function 異常系_予定が登録されていないのに承認しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 0, "is_plan_accept" => 0, "is_actual_entry" => 0]);
        }

        $a = $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "plan" => [1 => 1], "id" => ['1' => 1]])
        ;
        $a->assertRedirectedTo(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]));
        $a->assertSessionHas("danger_message", "2017-12-01の予定データが入力されていないようです。");
    }

    /**
     * @tests
     */
    public function 異常系_既に承認されている予定を承認しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1, "is_actual_accept" => 1]);
        }

        $a = $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "plan" => [1 => 1], "id" => ['1' => 1]])
        ;
        $a->assertRedirectedTo(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]));
        $a->assertSessionHas("danger_message", "すでに2017-12-01のデータは承認されています。");
    }

    /**
     * @tests
     */
    public function 異常系_予定がないのに実績を承認しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 0, "is_plan_accept" => 0, "is_actual_entry" => 0, "is_actual_accept" => 0]);
        }

        $a = $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "actual" => [1 => 1], "id" => ['1' => 1]])
        ;
        $a->assertRedirectedTo(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]));
        $a->assertSessionHas("danger_message", "2017-12-01の予定データが入力されていないようです。");
    }

    /**
     * @tests
     */
    public function 異常系_予定が承認されていないのに実績を承認しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 0, "is_actual_entry" => 0, "is_actual_accept" => 0]);
        }

        $a = $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "actual" => [1 => 1], "id" => ['1' => 1]])
        ;
        $a->assertRedirectedTo(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]));
        $a->assertSessionHas("danger_message", "2017-12-01の予定データが承認されていないようです。先に予定の承認を行ってください。");
    }

    /**
     * @tests
     */
    public function 異常系_実績が登録されていないのに実績を承認しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 0, "is_actual_accept" => 0]);
        }

        $a = $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "actual" => [1 => 1], "id" => ['1' => 1]])
        ;
        $a->assertRedirectedTo(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]));
        $a->assertSessionHas("danger_message", "2017-12-01の実績データが入力されていないようです。");
    }

    /**
     * @tests
     */
    public function 異常系_実績が承認されているのに実績を承認しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1, "is_actual_accept" => 1]);
        }

        $a = $this->actingAs($this->admin_user)
                ->visit(route('app::roster::accept::index'))
                ->see("2017年12月")
                ->visit(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]))
                ->see("勤務データ承認")
                ->post(route('app::roster::accept::calendar_accept', ['ym' => '201712', 'div' => 1, 'user_id' => $this->normal_user->id]), ['_token' => csrf_token(), "actual" => [1 => 1], "id" => ['1' => 1]])
        ;
        $a->assertRedirectedTo(route('app::roster::accept::calendar', ['ym' => '201712', 'div' => 1]));
        $a->assertSessionHas("danger_message", "すでに2017-12-01のデータは承認されています。");
    }

    /**
     * @tests
     */
    public function 異常系_勤務データIDがセットされていないとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $input   = ['_token' => csrf_token(), "actual" => ['1' => 0], "actual_reject" => [1 => "却下"]];
        $service = new App\Services\Roster\RosterAccept($this->admin_user->id);
        try {
            $service->updateRoster($input);
            $this->fail("例外がキャッチできてない");
        } catch (\Exception $ex) {
            $this->assertEquals($ex->getMessage(), "勤務データIDがセットされていないようです。");
        }
    }

    /**
     * @tests
     */
    public function 異常系_責任者の管轄部署が存在しないとエラー() {
        \App\Roster::truncate();
        \Session::start();
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        \App\ControlDivision::truncate();
        $input = ['_token' => csrf_token(), "actual" => ['1' => 0], "actual_reject" => [1 => "却下"]];

        try {
            $service = new App\Services\Roster\RosterAccept($this->admin_user->id);
            $service->updateRoster($input);
            $this->fail("例外がキャッチできてない");
        } catch (\Exception $ex) {
            $this->assertEquals($ex->getMessage(), "責任者の管轄部署が存在しません。");
        }
    }

}
