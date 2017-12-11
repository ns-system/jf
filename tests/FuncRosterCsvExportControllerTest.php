<?php

use App\Services\Traits;

class FuncRosterCsvExportControllerTest extends TestCase
{

    use Traits\CsvUsable;

    protected static $init = false;
    protected $superuser;
    protected $nomaluser1;
    protected $nomaluser2;

    public function setUp() {
        parent::setUp();

        if (!static::$init)
        {
            static::$init = true;
            try {
                \Artisan::call('db:reset', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('db:create', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('migrate');
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        \App\User::truncate();
        \App\RosterUser::truncate();
        \App\SinrenUser::truncate();
        \App\Division::truncate();
        \App\ControlDivision::truncate();
        \App\WorkType::truncate();
        \App\Roster::truncate();
        \App\Rest::truncate();
        $this->superuser       = factory(\App\User::class)->create(['is_super_user' => '1']);
        $this->nomaluser1 = factory(\App\User::class)->create(['first_name' => 'akatuki',"last_name"=>"kongou","name"=>"kongou akatuki"]);
        $this->nomaluser2 = factory(\App\User::class)->create(['first_name' => 'hibiki',"last_name"=>"hiei","name"=>"hiei hibiki"]);
        $this->nomaluser3 = factory(\App\User::class)->create(['first_name' => 'ikazuti',"last_name"=>"kirisima","name"=>"kirisima ikazuti"]);
        $this->nomaluser4 = factory(\App\User::class)->create(['first_name' => 'inazuma',"last_name"=>"haruna","name"=>"haruna inazuma"]);
        \App\RosterUser::firstOrCreate([
            'user_id'          => $this->nomaluser1->id,
            "is_administrator" => '0',
            "is_chief"         => '1',
            "is_proxy"         => '0',
            "is_proxy_active"  => '0',
            "work_type_id"     => '1',
            "staff_number"     => $this->nomaluser1->id]);
        \App\RosterUser::firstOrCreate([
            'user_id'          => $this->nomaluser2->id,
            "is_administrator" => '0',
            "is_chief"         => '0',
            "is_proxy"         => '0',
            "is_proxy_active"  => '0',
            "work_type_id"     => '1',
            "staff_number"     => $this->nomaluser2->id]);
        \App\RosterUser::firstOrCreate([
            'user_id'          => $this->nomaluser3->id,
            "is_administrator" => '0',
            "is_chief"         => '0',
            "is_proxy"         => '0',
            "is_proxy_active"  => '0',
            "work_type_id"     => '1',
            "staff_number"     => $this->nomaluser3->id]);
        \App\RosterUser::firstOrCreate([
            'user_id'          => $this->nomaluser4->id,
            "is_administrator" => '0',
            "is_chief"         => '0',
            "is_proxy"         => '0',
            "is_proxy_active"  => '0',
            "work_type_id"     => '1',
            "staff_number"     => $this->nomaluser4->id]);
        \App\RosterUser::firstOrCreate([
            'user_id'          => $this->superuser->id,
            "is_administrator" => '0',
            "is_chief"         => '0',
            "is_proxy"         => '0',
            "is_proxy_active"  => '0',
            "work_type_id"     => '1',
            "staff_number"     => $this->superuser->id]);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->superuser->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->nomaluser1->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->nomaluser2->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->nomaluser3->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->nomaluser4->id, "division_id" => '2']);        
        \App\ControlDivision::firstOrCreate(['user_id' => $this->nomaluser1->id, "division_id" => '1']);
        \App\Rest::firstOrCreate(["rest_reason_id" => 1, "rest_reason_name" => "テスト用理由"]);
        \App\Rest::firstOrCreate(["rest_reason_id" => 2, "rest_reason_name" => "テスト用理由2"]);
        \App\Division::firstOrCreate(["division_id" => '1', 'division_name' => 'test1']);
        \App\Division::firstOrCreate(["division_id" => '2', 'division_name' => 'test2']);
        \App\WorkType::firstOrCreate(["work_type_id" => '1', "work_type_name" => "テスト用"]);
        \App\WorkType::firstOrCreate(["work_type_id" => '2', "work_type_name" => "テスト用その二"]);
    }
 private function createRosterSample(){
        \App\Roster::create([
                    'user_id'           => $this->nomaluser1->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-1",
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 1,
                    "is_plan_reject"    => 0,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 1,
                    "is_actual_reject"  => 0]);

        \App\Roster::create([
                    'user_id'           => $this->nomaluser2->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-10",
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 0,
                    "is_plan_reject"    => 0,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 0,
                    "is_actual_reject"  => 0]);

        \App\Roster::create([
                    'user_id'           => $this->nomaluser3->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-15",
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 0,
                    "is_plan_reject"    => 1,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 0,
                    "is_actual_reject"  => 1]);
        \App\Roster::create([
                    'user_id'           => $this->nomaluser4->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-31",
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 0,
                    "is_plan_reject"    => 1,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 0,
                    "is_actual_reject"  => 1]);

    }

    /**
     * @tests
     */
    public function 正常系勤怠管理システムCSV出力で予定出力ができる() {

        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->nomaluser1->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $this->actingAs($this->superuser)
                ->visit('admin/roster/csv')
                ->see('CSV出力 ')
                ->see("2017年12月")
                ->visit('/admin/roster/csv/list/201712')
                ->visit("/admin/roster/csv/export/201712/plan")
                ->assertResponseStatus(200)
        ;
    }

    /**
     * @tests
     */
    public function 正常系勤怠管理システムCSV出力で実績出力ができる() {
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->nomaluser1->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $this->actingAs($this->superuser)
                ->visit('admin/roster/csv')
                ->see('CSV出力 ')
                ->see("2017年12月")
                ->visit('/admin/roster/csv/export/201712/actual')
                ->assertResponseStatus(200)
        ;
    }
    /**
     * @tests
     */
    public function 正常系勤怠管理システムCSV出力で生データ出力ができる() {
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->nomaluser1->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $this->actingAs($this->superuser)
                ->visit('admin/roster/csv')
                ->see('CSV出力 ')
                ->see("2017年12月")
                ->visit('/admin/roster/csv/export/201712/all')
                ->assertResponseStatus(200)
        ;
    }

    /**
     * @tests
     */
    public function 正常系勤務データ修正_承認ができる() {
        \App\Roster::truncate();
        $roster         = \App\Roster::create([
                    'user_id'           => $this->nomaluser1->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-1",
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 0,
                    "is_plan_reject"    => 0,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 0,
                    "is_actual_reject"  => 0]);
        $this->actingAs($this->superuser)
                ->visit('/admin/roster/csv/edit/201712/1')
                ->see('勤務データ修正')
                ->post('/admin/roster/csv/update/201712', ['_token'                     => csrf_token(),
                    "id"                         => $roster->id,
                    "plan_work_type_id"          => "2",
                    "plan_rest_reason_id"        => "2",
                    "plan_overtime_start_time"   => "13:40",
                    "plan_overtime_end_time"     => "15:30",
                    "plan_overtime_reason"       => "予定残業理由",
                    "plan_accept"                => "1",
                    "actual_work_type_id"        => "2",
                    "actual_rest_reason_id"      => "2",
                    "actual_overtime_start_time" => "20:00",
                    "actual_overtime_end_time"   => "21:15",
                    "actual_overtime_reason"     => "実残業理由",
                    "actual_accept"              => "1"
                ])
                ->assertRedirectedTo('/admin/roster/csv/list/201712')
        ;
        $changed_roster = \App\Roster::find($roster->id);
        $this->assertEquals($changed_roster->id, $roster->id);
        $this->assertNotEquals($changed_roster->plan_work_type_id, $roster->plan_work_type_id);
        $this->assertNotEquals($changed_roster->plan_rest_reason_id, $roster->plan_rest_reason_id);
        $this->assertNotEquals($changed_roster->plan_overtime_reason, $roster->plan_overtime_reason);
        $this->assertNotEquals($changed_roster->plan_overtime_start_time, $roster->plan_overtime_start_time);
        $this->assertNotEquals($changed_roster->plan_overtime_end_time, $roster->plan_overtime_end_time);
        $this->assertNotEquals($changed_roster->actual_work_type_id, $roster->actual_work_type_id);
        $this->assertNotEquals($changed_roster->actual_rest_reason_id, $roster->actual_rest_reason_id);
        $this->assertNotEquals($changed_roster->actual_overtime_reason, $roster->actual_overtime_reason);
        $this->assertNotEquals($changed_roster->actual_overtime_start_time, $roster->actual_overtime_start_time);
        $this->assertNotEquals($changed_roster->actual_overtime_end_time, $roster->actual_overtime_end_time);
        $this->assertNotEquals($changed_roster->is_plan_accept, $roster->is_plan_accept);
        $this->assertNotEquals($changed_roster->is_actual_accept, $roster->is_actual_accept);

        $this->assertEquals($changed_roster->plan_work_type_id, "2");
        $this->assertEquals($changed_roster->plan_rest_reason_id, "2");
        $this->assertEquals($changed_roster->plan_overtime_reason, "予定残業理由");
        $this->assertEquals($changed_roster->plan_overtime_start_time, "13:40:00");
        $this->assertEquals($changed_roster->plan_overtime_end_time, "15:30:00");
        $this->assertEquals($changed_roster->actual_work_type_id, "2");
        $this->assertEquals($changed_roster->actual_rest_reason_id, "2");
        $this->assertEquals($changed_roster->actual_overtime_reason, "実残業理由");
        $this->assertEquals($changed_roster->actual_overtime_start_time, "20:00:00");
        $this->assertEquals($changed_roster->actual_overtime_end_time, "21:15:00");
        $this->assertEquals($changed_roster->is_plan_accept, "1");
        $this->assertEquals($changed_roster->is_actual_accept, "1");
    }

    /**
     * @tests
     */
    public function 正常系勤務データ修正_却下ができる() {
        \App\Roster::truncate();
        $roster         = \App\Roster::create([
                    'user_id'           => $this->nomaluser1->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-1",
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 1,
                    "is_plan_reject"    => 0,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 1,
                    "is_actual_reject"  => 0]);
        $this->actingAs($this->superuser)
                ->visit('/admin/roster/csv/edit/201712/1')
                ->see('勤務データ修正')
                ->post('/admin/roster/csv/update/201712', ['_token'                     => csrf_token(),
                    "id"                         => $roster->id,
                    "plan_work_type_id"          => "2",
                    "plan_rest_reason_id"        => "2",
                    "plan_overtime_start_time"   => "13:40",
                    "plan_overtime_end_time"     => "15:30",
                    "plan_overtime_reason"       => "予定残業理由",
                    "plan_accept"                => "0",
                    "actual_work_type_id"        => "2",
                    "actual_rest_reason_id"      => "2",
                    "actual_overtime_start_time" => "20:00",
                    "actual_overtime_end_time"   => "21:15",
                    "actual_overtime_reason"     => "実残業理由",
                    "actual_accept"              => "0"
                ])
                ->assertRedirectedTo('/admin/roster/csv/list/201712')
        ;
        $changed_roster = \App\Roster::find($roster->id);
        $this->assertEquals($changed_roster->id, $roster->id);
        $this->assertNotEquals($changed_roster->plan_work_type_id, $roster->plan_work_type_id);
        $this->assertNotEquals($changed_roster->plan_rest_reason_id, $roster->plan_rest_reason_id);
        $this->assertNotEquals($changed_roster->plan_overtime_reason, $roster->plan_overtime_reason);
        $this->assertNotEquals($changed_roster->plan_overtime_start_time, $roster->plan_overtime_start_time);
        $this->assertNotEquals($changed_roster->plan_overtime_end_time, $roster->plan_overtime_end_time);
        $this->assertNotEquals($changed_roster->actual_work_type_id, $roster->actual_work_type_id);
        $this->assertNotEquals($changed_roster->actual_rest_reason_id, $roster->actual_rest_reason_id);
        $this->assertNotEquals($changed_roster->actual_overtime_reason, $roster->actual_overtime_reason);
        $this->assertNotEquals($changed_roster->actual_overtime_start_time, $roster->actual_overtime_start_time);
        $this->assertNotEquals($changed_roster->actual_overtime_end_time, $roster->actual_overtime_end_time);
        $this->assertNotEquals($changed_roster->is_plan_accept, $roster->is_plan_accept);
        $this->assertNotEquals($changed_roster->is_actual_accept, $roster->is_actual_accept);

        $this->assertEquals($changed_roster->plan_work_type_id, "2");
        $this->assertEquals($changed_roster->plan_rest_reason_id, "2");
        $this->assertEquals($changed_roster->plan_overtime_reason, "予定残業理由");
        $this->assertEquals($changed_roster->plan_overtime_start_time, "13:40:00");
        $this->assertEquals($changed_roster->plan_overtime_end_time, "15:30:00");
        $this->assertEquals($changed_roster->actual_work_type_id, "2");
        $this->assertEquals($changed_roster->actual_rest_reason_id, "2");
        $this->assertEquals($changed_roster->actual_overtime_reason, "実残業理由");
        $this->assertEquals($changed_roster->actual_overtime_start_time, "20:00:00");
        $this->assertEquals($changed_roster->actual_overtime_end_time, "21:15:00");
        $this->assertEquals($changed_roster->is_plan_accept, "0");
        $this->assertEquals($changed_roster->is_actual_accept, "0");
    }

    /**
     * @tests
     */
    public function 正常系勤務データ修正_変更なしができる() {
        \App\Roster::truncate();
        $roster         = \App\Roster::create([
                    'user_id'           => $this->nomaluser1->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-1",
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 1,
                    "is_plan_reject"    => 0,
                    "is_actual_accept"  => 0,
                    "is_actual_reject"  => 1]);
        $this->actingAs($this->superuser)
                ->visit('/admin/roster/csv/edit/201712/1')
                ->see('勤務データ修正')
                ->post('/admin/roster/csv/update/201712', ['_token'                     => csrf_token(),
                    "id"                         => $roster->id,
                    "plan_work_type_id"          => "2",
                    "plan_rest_reason_id"        => "2",
                    "plan_overtime_start_time"   => "13:40",
                    "plan_overtime_end_time"     => "15:30",
                    "plan_overtime_reason"       => "予定残業理由",
                    "plan_accept"                => "-1",
                    "actual_work_type_id"        => "2",
                    "actual_rest_reason_id"      => "2",
                    "actual_overtime_start_time" => "20:00",
                    "actual_overtime_end_time"   => "21:15",
                    "actual_overtime_reason"     => "実残業理由",
                    "actual_accept"              => "-1"
                ])
                ->assertRedirectedTo('/admin/roster/csv/list/201712')
        ;
        $changed_roster = \App\Roster::find($roster->id);
        $this->assertEquals($changed_roster->id, $roster->id);
        $this->assertNotEquals($changed_roster->plan_work_type_id, $roster->plan_work_type_id);
        $this->assertNotEquals($changed_roster->plan_rest_reason_id, $roster->plan_rest_reason_id);
        $this->assertNotEquals($changed_roster->plan_overtime_reason, $roster->plan_overtime_reason);
        $this->assertNotEquals($changed_roster->plan_overtime_start_time, $roster->plan_overtime_start_time);
        $this->assertNotEquals($changed_roster->plan_overtime_end_time, $roster->plan_overtime_end_time);
        $this->assertNotEquals($changed_roster->actual_work_type_id, $roster->actual_work_type_id);
        $this->assertNotEquals($changed_roster->actual_rest_reason_id, $roster->actual_rest_reason_id);
        $this->assertNotEquals($changed_roster->actual_overtime_reason, $roster->actual_overtime_reason);
        $this->assertNotEquals($changed_roster->actual_overtime_start_time, $roster->actual_overtime_start_time);
        $this->assertNotEquals($changed_roster->actual_overtime_end_time, $roster->actual_overtime_end_time);


        $this->assertEquals($changed_roster->plan_work_type_id, "2");
        $this->assertEquals($changed_roster->plan_rest_reason_id, "2");
        $this->assertEquals($changed_roster->plan_overtime_reason, "予定残業理由");
        $this->assertEquals($changed_roster->plan_overtime_start_time, "13:40:00");
        $this->assertEquals($changed_roster->plan_overtime_end_time, "15:30:00");
        $this->assertEquals($changed_roster->actual_work_type_id, "2");
        $this->assertEquals($changed_roster->actual_rest_reason_id, "2");
        $this->assertEquals($changed_roster->actual_overtime_reason, "実残業理由");
        $this->assertEquals($changed_roster->actual_overtime_start_time, "20:00:00");
        $this->assertEquals($changed_roster->actual_overtime_end_time, "21:15:00");
        $this->assertEquals($changed_roster->is_plan_accept, $roster->is_plan_accept);
        $this->assertEquals($changed_roster->is_actual_accept, $roster->is_actual_accept);
    }
   
        

    /**
     * @tests
     */
    public function 正常系勤務データ修正_リセットができる() {
        \App\Roster::truncate();
        $roster         = \App\Roster::create([
                    'user_id'           => $this->nomaluser1->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-1",
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 1,
                    "is_plan_reject"    => 1,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 1,
                    "is_actual_reject"  => 1]);
        $this->actingAs($this->superuser)
                ->visit('/admin/roster/csv/edit/201712/1')
                ->see('勤務データ修正')
                ->post('/admin/roster/csv/update/201712', ['_token'                     => csrf_token(),
                    "id"                         => $roster->id,
                    "plan_work_type_id"          => "2",
                    "plan_rest_reason_id"        => "2",
                    "plan_overtime_start_time"   => "13:40",
                    "plan_overtime_end_time"     => "15:30",
                    "plan_overtime_reason"       => "予定残業理由",
                    "plan_accept"                => "2",
                    "actual_work_type_id"        => "2",
                    "actual_rest_reason_id"      => "2",
                    "actual_overtime_start_time" => "20:00",
                    "actual_overtime_end_time"   => "21:15",
                    "actual_overtime_reason"     => "実残業理由",
                    "actual_accept"              => "2"
                ])
                ->assertRedirectedTo('/admin/roster/csv/list/201712')
        ;
        $changed_roster = \App\Roster::find($roster->id);
        $this->assertEquals($changed_roster->id, $roster->id);
        $this->assertNotEquals($changed_roster->plan_work_type_id, $roster->plan_work_type_id);
        $this->assertNotEquals($changed_roster->plan_rest_reason_id, $roster->plan_rest_reason_id);
        $this->assertNotEquals($changed_roster->plan_overtime_reason, $roster->plan_overtime_reason);
        $this->assertNotEquals($changed_roster->plan_overtime_start_time, $roster->plan_overtime_start_time);
        $this->assertNotEquals($changed_roster->plan_overtime_end_time, $roster->plan_overtime_end_time);
        $this->assertNotEquals($changed_roster->actual_work_type_id, $roster->actual_work_type_id);
        $this->assertNotEquals($changed_roster->actual_rest_reason_id, $roster->actual_rest_reason_id);
        $this->assertNotEquals($changed_roster->actual_overtime_reason, $roster->actual_overtime_reason);
        $this->assertNotEquals($changed_roster->actual_overtime_start_time, $roster->actual_overtime_start_time);
        $this->assertNotEquals($changed_roster->actual_overtime_end_time, $roster->actual_overtime_end_time);
        $this->assertNotEquals($changed_roster->is_plan_accept, $roster->is_plan_accept);
        $this->assertNotEquals($changed_roster->is_actual_accept, $roster->is_actual_accept);
        $this->assertNotEquals($changed_roster->is_plan_reject, $roster->is_plan_reject);
        $this->assertNotEquals($changed_roster->is_actual_reject, $roster->is_actual_reject);

        $this->assertEquals($changed_roster->plan_work_type_id, "2");
        $this->assertEquals($changed_roster->plan_rest_reason_id, "2");
        $this->assertEquals($changed_roster->plan_overtime_reason, "予定残業理由");
        $this->assertEquals($changed_roster->plan_overtime_start_time, "13:40:00");
        $this->assertEquals($changed_roster->plan_overtime_end_time, "15:30:00");
        $this->assertEquals($changed_roster->actual_work_type_id, "2");
        $this->assertEquals($changed_roster->actual_rest_reason_id, "2");
        $this->assertEquals($changed_roster->actual_overtime_reason, "実残業理由");
        $this->assertEquals($changed_roster->actual_overtime_start_time, "20:00:00");
        $this->assertEquals($changed_roster->actual_overtime_end_time, "21:15:00");
        $this->assertEquals($changed_roster->is_plan_accept, "0");
        $this->assertEquals($changed_roster->is_actual_accept, "0");
        $this->assertEquals($changed_roster->is_plan_reject, "0");
        $this->assertEquals($changed_roster->is_actual_reject, "0");
    }

    
    
    /**
     * @tests
     */
    public function 正常系検索_予定未承認ができる() {
        \App\Roster::truncate();
        \Session::start();
          $this->createRosterSample();

        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=1&actual=0&name=&division=&min_date=&max_date=")
                ->see("検索が終了しました。")
                ->dontsee($this->nomaluser1->first_name)
                ->dontsee($this->nomaluser1->last_name)
                ->See($this->nomaluser2->last_name)
                ->See($this->nomaluser2->first_name)
                ->dontSee($this->nomaluser3->last_name)
                ->dontSee($this->nomaluser3->first_name);

        ;
    }
    /**
     * @tests
     */
    public function 正常系検索_予定承認ができる() {
        \App\Roster::truncate();
        \Session::start();
         $this->createRosterSample();

        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=2&actual=0&name=&division=&min_date=&max_date=")
                ->see("検索が終了しました。")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->dontSee($this->nomaluser2->last_name)
                ->dontSee($this->nomaluser2->first_name)
                ->dontSee($this->nomaluser3->last_name)
                ->dontSee($this->nomaluser3->first_name);

        ;
    }
    /**
     * @tests
     */
    public function 正常系検索_予定却下ができる() {
        \App\Roster::truncate();
        \Session::start();
         $this->createRosterSample();

        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=3&actual=0&name=&division=&min_date=&max_date=")
                ->see("検索が終了しました。")
                ->dontsee($this->nomaluser1->first_name)
                ->dontsee($this->nomaluser1->last_name)
                ->dontSee($this->nomaluser2->last_name)
                ->dontSee($this->nomaluser2->first_name)
                ->See($this->nomaluser3->last_name)
                ->See($this->nomaluser3->first_name);

        ;
    }
    
    
    
    
    
    /**
     * @tests
     */
    public function 正常系検索_実績未承認ができる() {
        \App\Roster::truncate();
        \Session::start();
          $this->createRosterSample();

        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=0&actual=1&name=&division=&min_date=&max_date=")
                ->see("検索が終了しました。")
                ->dontsee($this->nomaluser1->first_name)
                ->dontsee($this->nomaluser1->last_name)
                ->See($this->nomaluser2->last_name)
                ->See($this->nomaluser2->first_name)
                ->dontSee($this->nomaluser3->last_name)
                ->dontSee($this->nomaluser3->first_name);

        ;
    }
    /**
     * @tests
     */
    public function 正常系検索_実績承認ができる() {
        \App\Roster::truncate();
        \Session::start();
         $this->createRosterSample();
        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=0&actual=2&name=&division=&min_date=&max_date=")
                ->see("検索が終了しました。")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->dontSee($this->nomaluser2->last_name)
                ->dontSee($this->nomaluser2->first_name)
                ->dontSee($this->nomaluser3->last_name)
                ->dontSee($this->nomaluser3->first_name);

        ;
    }
    /**
     * @tests
     */
    public function 正常系検索_実績却下ができる() {
        \App\Roster::truncate();
        \Session::start();
         $this->createRosterSample();

        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=0&actual=3&name=&division=&min_date=&max_date=")
                ->see("検索が終了しました。")
                ->dontsee($this->nomaluser1->first_name)
                ->dontsee($this->nomaluser1->last_name)
                ->dontSee($this->nomaluser2->last_name)
                ->dontSee($this->nomaluser2->first_name)
                ->See($this->nomaluser3->last_name)
                ->See($this->nomaluser3->first_name);

        ;
    }
    
    
    /**
     * @tests
     */
    public function 正常系検索_ユーザー名_苗字ができる() {
        \App\Roster::truncate();
        \Session::start();
         $this->createRosterSample();

        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=0&actual=0&name=".$this->nomaluser1->last_name."&division=&min_date=&max_date=")
                ->see("検索が終了しました。")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->dontSee($this->nomaluser2->last_name)
                ->dontSee($this->nomaluser2->first_name)
                ->dontSee($this->nomaluser3->last_name)
                ->dontSee($this->nomaluser3->first_name);

        ;
    }
     /**
     * @tests
     */
    public function 正常系検索_部署指定ができる() {
        \App\Roster::truncate();
        \Session::start();
         $this->createRosterSample();


        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->see($this->nomaluser4->first_name)
                ->see($this->nomaluser4->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=0&actual=0&name=&division=2&min_date=&max_date=")
                ->see("検索が終了しました。")
                ->dontsee($this->nomaluser1->first_name)
                ->dontsee($this->nomaluser1->last_name)
                ->dontSee($this->nomaluser2->last_name)
                ->dontSee($this->nomaluser2->first_name)
                ->dontSee($this->nomaluser3->last_name)
                ->dontSee($this->nomaluser3->first_name)
                ->See($this->nomaluser4->last_name)
                ->See($this->nomaluser4->first_name)

        ;
    }

    
    
    /**
     * @tests
     */
    public function 正常系検索_年月指定ができる() {
        \App\Roster::truncate();
        \Session::start();
         $this->createRosterSample();


        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->see($this->nomaluser4->first_name)
                ->see($this->nomaluser4->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=0&actual=0&name=&division=&min_date=2017-12-10&max_date=2017-12-26")
                ->see("検索が終了しました。")
                ->dontsee($this->nomaluser1->first_name)
                ->dontsee($this->nomaluser1->last_name)
                ->See($this->nomaluser2->last_name)
                ->See($this->nomaluser2->first_name)
                ->See($this->nomaluser3->last_name)
                ->See($this->nomaluser3->first_name)
                ->dontSee($this->nomaluser4->last_name)
                ->dontSee($this->nomaluser4->first_name)
                

        ;
    }
    /**
     * @tests
     */
    public function 正常系検索で該当者がいない場合誰も表示されない() {
        \App\Roster::truncate();
        \Session::start();
         $this->createRosterSample();


        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->see($this->nomaluser4->first_name)
                ->see($this->nomaluser4->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=1&actual=0&name=&division=2&min_date=2017-12-10&max_date=2017-12-26")
                ->see("指定した条件ではデータが見つかりませんでした。")
                ->dontsee($this->nomaluser1->first_name)
                ->dontsee($this->nomaluser1->last_name)
                ->dontSee($this->nomaluser2->last_name)
                ->dontSee($this->nomaluser2->first_name)
                ->dontSee($this->nomaluser3->last_name)
                ->dontSee($this->nomaluser3->first_name)
                ->dontSee($this->nomaluser4->last_name)
                ->dontSee($this->nomaluser4->first_name)

        ;
    }
    
    
    
    //異常系
     /**
     * @tests
     */
    public function 異常系権限のないユーザーが勤怠管理システムCSV出力で予定出力するとエラー() {

        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->nomaluser1->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $this->actingAs($this->nomaluser1)
                ->visit("/admin/roster/csv/export/201712/plan")
                ->seePageIs("/permission_error")
        ;
    }

    /**
     * @tests
     */
    public function 異常系権限のないユーザーが勤怠管理システムCSV出力で実績出力するとエラー() {
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->nomaluser1->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $this->actingAs($this->nomaluser1) 
                ->visit('/admin/roster/csv/export/201712/actual')
                ->seePageIs("/permission_error")
        ;
    }
    /**
     * @tests
     */
    public function 異常系権限のないユーザーが勤怠管理システムCSV出力で生データ出力するとエラー() {
        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->nomaluser1->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $this->actingAs($this->nomaluser1)
                ->visit('/admin/roster/csv/export/201712/all')
                ->seePageIs("/permission_error")
        ;
    }
    
 /**
     * @tests
     */
    public function 異常系権限のないユーザーが勤務データ修正を行うとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster         = \App\Roster::create([
                    'user_id'           => $this->nomaluser1->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-1",
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 1,
                    "is_plan_reject"    => 1,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 1,
                    "is_actual_reject"  => 1]);
      $this->actingAs($this->nomaluser1)
                ->post('/admin/roster/csv/update/201712', [
                    '_token'                     => csrf_token(),
                    "id"                         => $roster->id,
                    "plan_work_type_id"          => "2",
                    "plan_rest_reason_id"        => "2",
                    "plan_overtime_start_time"   => "13:40",
                    "plan_overtime_end_time"     => "15:30",
                    "plan_overtime_reason"       => "予定残業理由",
                    "plan_accept"                => "2",
                    "actual_work_type_id"        => "2",
                    "actual_rest_reason_id"      => "2",
                    "actual_overtime_start_time" => "20:00",
                    "actual_overtime_end_time"   => "21:15",
                    "actual_overtime_reason"     => "実残業理由",
                    "actual_accept"              => "2"
                ])
                ->assertRedirectedTo('/permission_error')
        ;
    }
    
    
     /**
     * @tests
     */
    public function 異常系検索_予定に0から3以外の文字が入力されるとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $this->createRosterSample();

        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->see($this->nomaluser4->first_name)
                ->see($this->nomaluser4->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=5&actual=0&name=&division=&min_date=&max_date=")
                ->see("予定の書式が正しくありません。")
                

        ;
    }
     /**
     * @tests
     */
    public function 異常系検索_実績に0から3以外の文字が入力されるとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $this->createRosterSample();

        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->see($this->nomaluser4->first_name)
                ->see($this->nomaluser4->last_name)
                ->visit("/admin/roster/csv/search/201712?&actual=4&name=&division=2&min_date=&max_date=")
                ->see("実績の書式が正しくありません。")
                

        ;
    }
    /**
     * @tests
     */
    public function 異常系検索_存在しない部署が入力されるとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $this->createRosterSample();

        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->see($this->nomaluser4->first_name)
                ->see($this->nomaluser4->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=&actual=&name=&division=999&min_date=&max_date=")
                ->see("選択された部署は正しくありません。")
                

        ;
    }
    /**
     * @tests
     */
    public function 異常系検索_部署に文字列が入力されるとエラー() {
        \App\Roster::truncate();
        \Session::start();
        $this->createRosterSample();

        $this->actingAs($this->superuser)
                ->visit("/admin/roster/csv/search/201712")
                ->see("勤怠管理システム")
                ->see("CSV出力")
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->see($this->nomaluser3->first_name)
                ->see($this->nomaluser3->last_name)
                ->see($this->nomaluser4->first_name)
                ->see($this->nomaluser4->last_name)
                ->visit("/admin/roster/csv/search/201712?plan=&actual=&name=&division=99fgasd9&min_date=&max_date=")
                ->see("選択された部署は正しくありません。")
                

        ;
    }
    /**
     * @tests
     */
    public function 異常系勤怠管理システムCSV出力で想定されていないデータ名が指定されるとエラー() {

        for ($i = 1; $i <= 31; $i++) {
            \App\Roster::create(['user_id' => $this->nomaluser1->id, "plan_work_type_id" => "1", "entered_on" => "2017-12-" . $i, "month_id" => "201712", "is_plan_entry" => 1, "is_plan_accept" => 1, "is_actual_entry" => 1]);
        }
        $this->actingAs($this->superuser)
                ->visit('admin/roster/csv')
                ->see('CSV出力 ')
                ->see("2017年12月")
                ->visit('/admin/roster/csv/list/201712')
                ->visit("/admin/roster/csv/export/201712/unassumption")
               ->see('/admin/roster/csv/list/201712')
                ->see("予期しないデータが入力されたため、処理が中断されました。")
        ;
    }
    
 // エラーの内容が取得できないからとりあえず後回し  
    /**
     * @tests
     */
    public function 異常系異常な値がPOSTされた時エラー() {
        \App\Roster::truncate();
        \Session::start();
        $roster         = \App\Roster::create([
                    'user_id'           => $this->nomaluser1->id,
                    "plan_work_type_id" => "1",
                    "entered_on"        => "2017-12-1",
                    "month_id"          => "201712",
                    "is_plan_entry"     => 1,
                    "is_plan_accept"    => 1,
                    "is_plan_reject"    => 1,
                    "is_actual_entry"   => 1,
                    "is_actual_accept"  => 1,
                    "is_actual_reject"  => 1]);
      $a=$this->actingAs($this->superuser)
              ->visit('/admin/roster/csv/edit/201712/1')
                ->post('/admin/roster/csv/update/201712', [
                    '_token'                     => csrf_token(),
                    "id"                         => $roster->id,
                    "plan_work_type_id"          => "9",
                    "plan_rest_reason_id"        => "6",
                    "plan_overtime_start_time"   => "28:40",
                    "plan_overtime_end_time"     => "30:30",
                    "plan_overtime_reason"       => "予定残業理由",
                    "plan_accept"                => "5",
                    "actual_work_type_id"        => "5",
                    "actual_rest_reason_id"      => "6",
                    "actual_overtime_start_time" => "2:70",
                    "actual_overtime_end_time"   => "21:185",
                    "actual_overtime_reason"     => "実残業理由",
                    "actual_accept"              => "9"
                ]);
              $a->assertSessionHasErrors();
         $a->assertRedirectedTo('/admin/roster/csv/edit/201712/1') ;
    }
}
