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
                \App\Division::firstOrCreate(["division_id" => '1', 'division_name' => 'test']);
                \App\WorkType::firstOrCreate(["work_type_id" => '1', "work_type_name" => "テスト用","work_start_time"=>"05:00:00","work_end_time"=>"07:12:32"]);
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
        \App\RosterUser::firstOrCreate(['user_id' => $this->admin_user->id, "is_administrator" => '0', "is_chief" => '1', "is_proxy" => '0', "is_proxy_active" => '0', "work_type_id" => '1']);
        \App\RosterUser::firstOrCreate(['user_id' => $this->normal_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0', "work_type_id" => '1']);
        \App\RosterUser::firstOrCreate(['user_id' => $this->proxy_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '1', "work_type_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->admin_user->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->normal_user->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->proxy_user->id, "division_id" => '1']);
        \App\ControlDivision::firstOrCreate(['user_id' => $this->admin_user->id, "division_id" => '1']);
        \App\Rest::firstOrCreate(["rest_reason_id"=>1,"rest_reason_name"=>"テスト用理由"]);
    }
     /**
     * @tests
     */
    public function 正常系一般ユーザーが勤務予定データを更新できる() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712"]);
        }

        $unentry_plan = \App\Roster::where('id', 1)->first();
        $this->actingAs($this->normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/plan/edit/201712/' . $roster[0]->id, array_merge($this->calender_plan_post_dummy_data, ["plan_rest_reason_id" => "", '_token' => csrf_token()]))
                ->assertRedirectedTo('/app/roster/calendar/201712')
        ;
        $entry_plan   = \App\Roster::where('id', $roster[0]->id)->first();
        $this->assertEquals(0, $unentry_plan->is_plan_entry);
        $this->assertEquals(1, $entry_plan->is_plan_entry);
    }

    /**
     * @tests
     */
    public function 正常系一般ユーザーが勤務実績データを更新できる() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1,]);
        }
        $unentry_plan = \App\Roster::where('id', $roster[0]->id)->first();
        $this->actingAs($this->normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/actual/edit/201712/' . $roster[0]->id, array_merge($this->calender_actual_post_dummy_data, ['_token' => csrf_token()]))
                ->assertRedirectedTo('/app/roster/calendar/201712')
        ;
        $entry_plan   = \App\Roster::where('id', $roster[0]->id)->first();
        $this->assertEquals(0, $unentry_plan->is_actual_entry);
        $this->assertEquals(1, $entry_plan->is_actual_entry);
    }

    /**
     * @tests
     */
    public function 正常系一般ユーザーが勤務予定データを削除できる() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        $unentry_plan = \App\Roster::where('id', $roster[0]->id)->first();
        $this->actingAs($this->normal_user)
                ->visit('/app/roster/calendar/delete/' . $roster[0]->id)
        ;
        $entry_plan   = \App\Roster::where('id', $roster[0]->id)->first();
        $this->assertEquals(1, $unentry_plan->is_plan_entry);
        $this->assertEquals(0, $entry_plan->is_plan_entry);
    }
    
    
   //異常系
    /**
     * @tests
     */
    public function 異常系日付以外のデータがセットされるとエラー() {
       \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712"]);
        }

        $a=$this->actingAs($this->normal_user)
                ->visit('/app/roster/calendar/201712')
                ->post('/app/roster/calendar/plan/edit/201712dg/' . $roster[0]->id, array_merge($this->calender_plan_post_dummy_data, ["plan_rest_reason_id" => "", '_token' => csrf_token()]))
               ->assertSessionHas("warn_message", "日付以外のデータが入力されました。")
        ;
        
        
    }
    
    /**
     * @tests
     */
    public function 異常系勤務予定データを削除時存在しないIDを選ぶとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        
        $this->actingAs($this->normal_user)
                ->visit('/app/roster/calendar/delete/' ."999")
                ->see("予定データが見つかりませんでした。")
               
        ;
      
    }
    /**
     * @tests
     */
    public function 異常系一般ユーザーが勤務実績データを更新時存在しないIDを選択するとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1,]);
        }
        $unentry_plan = \App\Roster::where('id', $roster[0]->id)->first();
        $this->actingAs($this->normal_user)
                ->post('/app/roster/calendar/actual/edit/201712/' ."999", array_merge($this->calender_actual_post_dummy_data, ['_token' => csrf_token()]))
                ->assertSessionHas("warn_message", "予定データが見つかりませんでした。")
        ;
       
    }
    /**
     * @tests
     */
    public function 異常系カレンダー表示時日付以外のデータがセットされるとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster = [];
        for ($i = 1; $i <= 31; $i++) {
            $roster[] = \App\Roster::create(['user_id' => $this->normal_user->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1]);
        }
        
        $this->actingAs($this->normal_user)
                ->visit('/app/roster/calendar/2050sugoitakai12')
                ->see("日付以外のデータがセットされました。")
               
        ;
      
    }
}
