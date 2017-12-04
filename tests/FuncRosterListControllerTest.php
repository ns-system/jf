<?php

use App\Services\Traits;

class FuncRosterListControllerTest extends TestCase
{

    use Traits\CsvUsable;

    protected static $init = false;
    protected $chief;
    protected $nomaluser1;
    protected $nomaluser2;
    protected $nomaluser3;

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
        $this->chief      = factory(\App\User::class)->create(['first_name' => 'akatuki', "last_name" => "kongou", "name" => "kongou akatuki"]);
        $this->nomaluser1 = factory(\App\User::class)->create(['first_name' => 'hibiki', "last_name" => "hiei", "name" => "hiei hibiki"]);
        $this->nomaluser2 = factory(\App\User::class)->create(['first_name' => 'ikazuti', "last_name" => "kirisima", "name" => "kirisima ikazuti"]);
        $this->nomaluser3 = factory(\App\User::class)->create(['first_name' => 'inazuma', "last_name" => "haruna", "name" => "haruna inazuma"]);
        \App\RosterUser::firstOrCreate([
            'user_id'          => $this->chief->id,
            "is_administrator" => '0',
            "is_chief"         => '1',
            "is_proxy"         => '0',
            "is_proxy_active"  => '0',
            "work_type_id"     => '1',
            "staff_number"     => $this->chief->id]);
        \App\RosterUser::firstOrCreate([
            'user_id'          => $this->nomaluser1->id,
            "is_administrator" => '0',
            "is_chief"         => '0',
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

        \App\SinrenUser::firstOrCreate(['user_id' => $this->chief->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->nomaluser1->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->nomaluser2->id, "division_id" => '1']);
        \App\SinrenUser::firstOrCreate(['user_id' => $this->nomaluser3->id, "division_id" => '2']);
        \App\ControlDivision::firstOrCreate(['user_id' => $this->nomaluser1->id, "division_id" => '1']);
        \App\Rest::firstOrCreate(["rest_reason_id" => 1, "rest_reason_name" => "テスト用理由"]);
        \App\Rest::firstOrCreate(["rest_reason_id" => 2, "rest_reason_name" => "テスト用理由2"]);
        \App\Division::firstOrCreate(["division_id" => '1', 'division_name' => 'test1']);
        \App\Division::firstOrCreate(["division_id" => '2', 'division_name' => 'test2']);
        \App\WorkType::firstOrCreate(["work_type_id" => '1', "work_type_name" => "テスト用"]);
        \App\WorkType::firstOrCreate(["work_type_id" => '2', "work_type_name" => "テスト用その二"]);
    }

    private function createRosterSample() {
        \App\Roster::truncate();
        \App\Roster::create([
            'user_id'           => $this->chief->id,
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
            'user_id'           => $this->nomaluser1->id,
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
            'user_id'           => $this->nomaluser2->id,
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
            'user_id'           => $this->nomaluser3->id,
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
    public function 正常系管理者が自分の部署の勤務データが表示できる() {
        $this->createRosterSample();
        $this->actingAs($this->chief)
                ->visit('/app/roster/division/check')
                ->seePageIs('/app/roster/division/home/1')
                ->see("2017年12月")
                ->visit('/app/roster/division/list/1/201712')
                ->see($this->chief->first_name)
                ->see($this->chief->last_name)
                ->see($this->nomaluser1->first_name)
                ->see($this->nomaluser1->last_name)
                ->see($this->nomaluser2->first_name)
                ->see($this->nomaluser2->last_name)
                ->dontsee($this->nomaluser3->first_name)
                ->dontsee($this->nomaluser3->last_name)
        ;
    }

    /**
     * @tests
     */
    public function 正常系管理者が自分の部署の勤務データが存在しないときxxxx年xx月が表示されない() {
        $this->actingAs($this->chief)
                ->visit('/app/roster/division/check')
                ->seePageIs('/app/roster/division/home/1')
                ->dontsee("年")
        ;
    }
/**
     * @tests
     */
    public function 正常系信連ユーザーが登録されていないユーザーが部署の勤務データを見ようとするとユーザー情報登録に遷移する() {
        \App\SinrenUser::truncate();
        $this->actingAs($this->chief)
                ->visit('/app/roster/division/check')
                ->seePageIs(route('app::roster::user::show', ['id' => $this->chief->id]))
                
        ;
    }
    /**
     * @tests
     */
    public function 異常系管理者が他のの部署の勤務データをみようとするとエラー() {
        $this->createRosterSample();
        $this->actingAs($this->chief)
                ->visit('/app/roster/division/home/2')
                ->seePageIs(route('index'))
                ->see("許可されていない部署を閲覧しようとしました。")
                ->visit('/app/roster/division/list/2/201712')
                ->seePageIs(route('index'))
                ->see("許可されていない部署を閲覧しようとしました。")
        ;
    }
    
}
