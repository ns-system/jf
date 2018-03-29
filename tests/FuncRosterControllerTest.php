<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FuncRosterControllerTest
 *
 * @author r-kawanishi
 */
class FuncRosterControllerTest extends TestCase
{

    use \App\Services\Traits\Testing\DbDisconnectable;

    protected static $init                     = false;
    protected static $super_user;
    protected static $admin_user;
    protected static $normal_user;
    protected static $proxy_user;
    protected $calender_plan_post_dummy_data   = [
        "plan_start_hour"      => 9,
        "plan_start_time"      => 00,
        "plan_end_hour"        => 17,
        "plan_end_time"        => 00,
        "plan_overtime_reason" => ""
    ];
    protected $calender_actual_post_dummy_data = [
        "actual_start_hour"      => 9,
        "actual_start_time"      => 00,
        "actual_end_hour"        => 17,
        "actual_end_time"        => 00,
        "actual_rest_reason_id"  => "0",
        "actual_work_type_id"    => 1,
        "actual_overtime_reason" => ""
    ];

    public function setUp() {
        parent::setUp();

        if (!static::$init)
        {
            try {

                \Artisan::call('db:reset', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('db:create', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('migrate');
                \App\Division::firstOrCreate(["division_id" => '1', 'division_name' => 'test']);
                \App\WorkType::firstOrCreate(["work_type_id" => '1', "work_type_name" => "テスト用", "work_start_time" => "09:00:00", "work_end_time" => "17:00:00"]);
                \App\WorkType::firstOrCreate(["work_type_id" => '2', "work_type_name" => "テスト用2", "work_start_time" => "010:20:00", "work_end_time" => "18:00:00"]);
                \App\Holiday::firstOrCreate(["holiday" => '2017-12-23', "holiday_name" => "勤労感謝の日"]);
                static::$super_user  = factory(\App\User::class)->create(['is_super_user' => '1']);
                static::$admin_user  = factory(\App\User::class)->create();
                static::$normal_user = factory(\App\User::class)->create();
                static::$proxy_user  = factory(\App\User::class)->create();
                \App\RosterUser::firstOrCreate(['user_id' => static::$admin_user->id, "is_administrator" => '0', "is_chief" => '1', "is_proxy" => '0', "is_proxy_active" => '0', "work_type_id" => '1']);
                \App\RosterUser::firstOrCreate(['user_id' => static::$normal_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0', "work_type_id" => '1']);
                \App\RosterUser::firstOrCreate(['user_id' => static::$proxy_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '1', "work_type_id" => '1']);
                \App\SinrenUser::firstOrCreate(['user_id' => static::$admin_user->id, "division_id" => '1']);
                \App\SinrenUser::firstOrCreate(['user_id' => static::$normal_user->id, "division_id" => '1']);
                \App\SinrenUser::firstOrCreate(['user_id' => static::$proxy_user->id, "division_id" => '1']);
                \App\ControlDivision::firstOrCreate(['user_id' => static::$admin_user->id, "division_id" => '1']);
                \App\Rest::firstOrCreate(["rest_reason_id" => 1, "rest_reason_name" => "テスト用理由"]);
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
            static::$init = true;
        }
//        \App\User::truncate();
//        \App\RosterUser::truncate();
//        \App\SinrenUser::truncate();
//        \App\ControlDivision::truncate();
//        \App\Rest::truncate();
    }

    public function tearDown() {
        $this->disconnect();
        parent::tearDown();
    }

    /**
     * @tests
     */
    public function 正常系_一般ユーザーが勤務予定データを更新できる() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712"]);
        }

        $unentry_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/plan/edit/201712/' . $roster[0]->id, array_merge($this->calender_plan_post_dummy_data, ["plan_rest_reason_id" => "", '_token' => csrf_token()]))
                ->assertRedirectedTo('/app/roster/calendar/201712')
        ;

        $entry_plan = \App\Roster::where('id', $roster[0]->id)->first();
        $this->assertEquals(0, $unentry_plan->is_plan_entry);
        $this->assertEquals(1, $entry_plan->is_plan_entry);
        unset($roster);
       
    }

    /**
     * @tests
     */
    public function 異常系_一般ユーザーが勤務予定データを更新する際に終了時間より開始時間のほうが遅いとエラーになる() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "actual_work_type_id" => 2, "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1,]);
        }
        $dummy = [
            '_token'          => csrf_token(),
            "plan_start_hour" => 23,
            "plan_start_time" => 30,
            "plan_end_hour"   => 8,
            "plan_end_time"   => 30,
        ];
        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/plan/edit/201712/' . $roster[0]->id, array_merge($this->calender_plan_post_dummy_data, $dummy))
                ->assertSessionHas("warn_message", "開始時間 < 終了時間となるように入力してください。")
        ;
        unset($roster);
    }

    /**
     * @tests
     */
    public function 正常系_一般ユーザーが勤務実績データを更新できる() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "actual_work_type_id" => 2, "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1,]);
        }
        $unentry_plan = \App\Roster::where('id', $roster[0]->id)->first();
        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/actual/edit/201712/' . $roster[0]->id, array_merge($this->calender_actual_post_dummy_data, ['_token' => csrf_token()]))
                ->assertRedirectedTo('/app/roster/calendar/201712')
        ;
        $entry_plan   = \App\Roster::where('id', $roster[0]->id)->first();
        $this->assertEquals(0, $unentry_plan->is_actual_entry);
        $this->assertEquals(1, $entry_plan->is_actual_entry);
        unset($roster);
    }

    /**
     * @tests
     */
    public function 異常系_一般ユーザーが勤務実績データを更新する際に終了時間より開始時間のほうが遅いとエラーになる() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "actual_work_type_id" => 2, "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1,]);
        }
        $dummy = [
            '_token'            => csrf_token(),
            "actual_start_hour" => 23,
            "actual_start_time" => 30,
            "actual_end_hour"   => 8,
            "actual_end_time"   => 30,
        ];
        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/actual/edit/201712/' . $roster[0]->id, array_merge($this->calender_actual_post_dummy_data, $dummy))
                ->assertSessionHas("warn_message", "開始時間 < 終了時間となるように入力してください。")
        ;
        unset($roster);
    }

    /**
     * @tests
     */
    public function 正常系_一般ユーザーが勤務実績データを休みに更新できる() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "actual_work_type_id" => 2, "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1,]);
        }
        $unentry_plan = \App\Roster::where('id', $roster[0]->id)->first();
        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/actual/edit/201712/' . $roster[0]->id, array_merge($this->calender_actual_post_dummy_data, ['_token' => csrf_token(), "actual_rest_reason_id" => 1]))
                ->assertRedirectedTo('/app/roster/calendar/201712')
        ;
        $entry_plan   = \App\Roster::where('id', $roster[0]->id)->first();
        $this->assertEquals(0, $unentry_plan->is_actual_entry);
        $this->assertEquals(1, $entry_plan->is_actual_entry);
        unset($roster);
    }

    /**
     * @tests
     */
    public function 正常系_一般ユーザーが勤務予定データを削除できる() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        $unentry_plan = \App\Roster::where('id', $roster[0]->id)->first();
        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/delete/' . $roster[0]->id)
        ;
        $entry_plan   = \App\Roster::where('id', $roster[0]->id)->first();
        $this->assertEquals(1, $unentry_plan->is_plan_entry);
        $this->assertEquals(0, $entry_plan->is_plan_entry);
        unset($roster);
    }

    /**
     * @tests
     */
    public function 正常系_登録した勤務予定実績データが正しく表示される() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712"]);
        }

        $unentry_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/plan/edit/201712/' . $roster[0]->id, array_merge($this->calender_plan_post_dummy_data, ["plan_rest_reason_id" => "", '_token' => csrf_token()]))
                ->assertRedirectedTo('/app/roster/calendar/201712')
        ;
        

        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/actual/edit/201712/' . $roster[0]->id, array_merge([
                    "actual_start_hour"      => 10,
                    "actual_start_time"      => 00,
                    "actual_end_hour"        => 18,
                    "actual_end_time"        => 30,
                    "actual_rest_reason_id"  => "0",
                    "actual_work_type_id"    => 1,
                    "actual_overtime_reason" => "a"
                                ], ['_token' => csrf_token()]))
                ->assertRedirectedTo('/app/roster/calendar/201712')
        ;
        

        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/201712')
                ->see("class=\"small\" id=\"plan_overtime_end_time_1\">9:00 ～ 17:00")
                ->see("class=\"small\" id=\"actual_overtime_end_time_1\">10:00 ～ 18:30")
        ;

        unset($roster);
    }

    //異常系_
    /**
     * @tests
     */
    public function 異常系_日付以外のデータがセットされるとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712"]);
        }

        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/plan/edit/201712dg/' . $roster[0]->id, array_merge($this->calender_plan_post_dummy_data, ["plan_rest_reason_id" => "", '_token' => csrf_token()]))
                ->assertSessionHas("warn_message", "日付以外のデータが入力されました。")
        ;
        unset($roster);
    }

    /**
     * @tests
     */
    public function 異常系_勤務予定データを削除時存在しないIDを選ぶとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }

        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/delete/' . "999")
                ->see("No query results for model [App\Roster].")
        ;
        unset($roster);
    }

    /**
     * @tests
     */
    public function 異常系_一般ユーザーが勤務予定データを更新時存在しないIDを選択するとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 0,]);
        }
        $unentry_plan = \App\Roster::where('id', $roster[0]->id)->first();
        $this->actingAs(static::$normal_user)
                ->post('/app/roster/calendar/plan/edit/201712/' . "999", array_merge($this->calender_plan_post_dummy_data, ['_token' => csrf_token()]))
                ->assertSessionHas("warn_message", "No query results for model [App\Roster].")
        ;
        unset($roster);
    }

    /**
     * @tests
     */
    public function 異常系_一般ユーザーが勤務実績データを更新時存在しないIDを選択するとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1,]);
        }
        $unentry_plan = \App\Roster::where('id', $roster[0]->id)->first();
        $this->actingAs(static::$normal_user)
                ->post('/app/roster/calendar/actual/edit/201712/' . "999", array_merge($this->calender_actual_post_dummy_data, ['_token' => csrf_token()]))
                ->assertSessionHas("warn_message", "No query results for model [App\Roster].")
        ;
        unset($roster);
    }

    /**
     * @tests
     */
    public function 異常系_カレンダー表示時日付以外のデータがセットされるとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }

        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/2050sugoitakai12')
                ->see("日付以外のデータがセットされました。")
        ;
        unset($roster);
    }

    /**
     * @tests
     */
    public function 異常系_承認済み勤務予定データを削除しようとするとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => static::$normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1, "is_actual_accept" => 1]);
        }

        $this->actingAs(static::$normal_user)
                ->visit('/app/roster/calendar/delete/' . "1")
                ->see("データはすでに承認されているため、削除できません。")
        ;
        unset($roster);
    }
}
