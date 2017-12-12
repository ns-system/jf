<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers;

class UnitIndexControllerTest extends TestCase
{

    use \App\Services\Traits\Testing\DbDisconnectable;

    const MAX_MONTH_CNT = 5;
    const FIRST_YEAR_ID = 2000;

    protected static $init = false;
    protected static $chief;
    protected static $user_1;
    protected static $user_2;
    protected static $user_3;
    protected static $division;

    public function setUp() {
        parent::setUp();

        if (static::$init)
        {
            return true;
        }
        static::$init = true;
        \App\Roster::truncate();
        \App\User::truncate();
        \App\ControlDivision::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        \App\SinrenUser::truncate();
        \App\Holiday::truncate();

        static::$chief    = factory(\App\User::class)->create();
        static::$user_1   = factory(\App\User::class)->create();
        static::$user_2   = factory(\App\User::class)->create();
        static::$user_3   = factory(\App\User::class)->create();
        $div_1            = \App\SinrenDivision::create(['division_id' => 1, 'division_name' => 'division_1']);
        $div_2            = \App\SinrenDivision::create(['division_id' => 2, 'division_name' => 'division_2']);
        \App\Holiday::insert([
            ['holiday' => '2000-02-01', 'holiday_name' => 'holiday_1'],
            ['holiday' => '2000-02-02', 'holiday_name' => 'holiday_2'],
            ['holiday' => '2000-02-03', 'holiday_name' => 'holiday_3'],
            ['holiday' => '2000-02-04', 'holiday_name' => 'holiday_4'],
            ['holiday' => '2000-02-07', 'holiday_name' => 'holiday_5'],
            ['holiday' => '2000-02-08', 'holiday_name' => 'holiday_6'],
            ['holiday' => '2000-02-09', 'holiday_name' => 'holiday_7'],
            ['holiday' => '2000-02-10', 'holiday_name' => 'holiday_8'],
        ]);
        static::$division = $div_1;

        \App\SinrenUser::insert([
            ['user_id' => static::$chief->id, 'division_id' => $div_1->division_id],
            ['user_id' => static::$user_1->id, 'division_id' => $div_1->division_id],
            ['user_id' => static::$user_2->id, 'division_id' => $div_1->division_id],
            ['user_id' => static::$user_3->id, 'division_id' => $div_2->division_id],
        ]);
        \App\ControlDivision::insert(['user_id' => static::$chief->id, 'division_id' => $div_1->division_id]);

        for ($i = 1; $i <= self::MAX_MONTH_CNT; $i++) {
            $month_id = self::FIRST_YEAR_ID . sprintf('%02d', $i);
            $end_day  = (int) date('t', strtotime($month_id . '01'));
            for ($d = 1; $d <= $end_day; $d++) {
                $this->createRosters($month_id, $d);
            }
        }
    }

    public function tearDown() {
        $this->disconnect();
        parent::tearDown();
    }

    private function createRosters($month_id, $day) {
        $entered_on = $month_id . sprintf('%02d', $day);
        $template   = [
//            'user_id'          => '',
            'is_plan_entry'    => false,
            'is_actual_entry'  => false,
            'is_plan_accept'   => false,
            'is_plan_reject'   => false,
            'is_actual_accept' => false,
            'is_actual_reject' => false,
            'entered_on'       => $entered_on,
            'month_id'         => $month_id,
        ];
        $rosters    = $template;
        switch (($month_id + $day) % 9) {
            case 0:
                break;
            case 1:
                $rosters['is_plan_entry']    = true;
                break;
            case 2:
                $rosters['is_plan_entry']    = true;
                $rosters['is_plan_reject']   = true;
                break;
            case 3:
                $rosters['is_plan_entry']    = true;
                $rosters['is_plan_reject']   = true;
                break;
            case 4:
                $rosters['is_plan_entry']    = true;
                $rosters['is_actual_entry']  = true;
                break;
            case 5:
                $rosters['is_plan_entry']    = true;
                $rosters['is_actual_entry']  = true;
                $rosters['is_plan_reject']   = true;
                break;
            case 6:
                $rosters['is_plan_entry']    = true;
                $rosters['is_actual_entry']  = true;
                $rosters['is_plan_accept']   = true;
                break;
            case 7:
                $rosters['is_plan_entry']    = true;
                $rosters['is_actual_entry']  = true;
                $rosters['is_plan_accept']   = true;
                $rosters['is_actual_reject'] = true;
                break;
            case 8:
                $rosters['is_plan_entry']    = true;
                $rosters['is_actual_entry']  = true;
                $rosters['is_plan_accept']   = true;
                $rosters['is_actual_accept'] = true;
                break;
        }
        $user1_rosters = array_merge($rosters, ['user_id' => static::$user_1->id]);
        $user2_rosters = array_merge($rosters, ['user_id' => static::$user_2->id]);
        $user3_rosters = array_merge($rosters, ['user_id' => static::$user_3->id]);
        \App\Roster::insert([$user1_rosters, $user2_rosters, $user3_rosters]);
//        \App\Roster::create($user1_rosters);
//        \App\Roster::create($user2_rosters);
//        \App\Roster::create($user3_rosters);
    }

    private function setReflection($class, $function_name) {
        $s = new \ReflectionMethod($class, $function_name);
        $s->setAccessible(true);
        return $s;
    }

    /**
     * @tests
     */
    public function 正常系_責任者_４ヶ月前までの勤務未承認データが取得できる() {
        $s    = new Controllers\IndexController();
        $ref  = $this->setReflection($s, 'getRosterChiefNotice');
        $cnts = $ref->invoke($s, static::$chief->id);

        $result = [];
        foreach ($cnts as $cnt) {
            $result[$cnt->month_id] = [
                'total'         => $cnt->total,
                'division_id'   => $cnt->division_id,
                'division_name' => $cnt->division_name,
            ];
        }

        $expect = [
            200005 => [
                'total'         => 46,
                'division_id'   => static::$division->division_id,
                'division_name' => static::$division->division_name,
            ],
            200004 => [
                'total'         => 44,
                'division_id'   => static::$division->division_id,
                'division_name' => static::$division->division_name,
            ],
            200003 => [
                'total'         => 46,
                'division_id'   => static::$division->division_id,
                'division_name' => static::$division->division_name,
            ],
            200002 => [
                'total'         => 46,
                'division_id'   => static::$division->division_id,
                'division_name' => static::$division->division_name,
            ],
        ];

        $this->assertEquals($expect, $result);
    }

    /**
     * @tests
     */
    public function 正常系_一般ユーザー_４ヶ月前までの勤務未承認データが取得できる() {
        $s    = new Controllers\IndexController();
        $ref  = $this->setReflection($s, 'getRosterUserNotice');
        $cnts = $ref->invoke($s, static::$user_1->id, '2000-05-15');

        $result = [];
        foreach ($cnts as $cnt) {
            $result[$cnt->month_id] = [
                'plan_total'   => $cnt->plan_total,
                'actual_total' => $cnt->actual_total,
                'user_id'      => $cnt->user_id,
            ];
        }
//        dd($result);

        $expect = [
            200005 => [
                'plan_total'   => 2,
                'actual_total' => 6,
                'user_id'      => static::$user_1->id,
            ],
            200004 => [
                'plan_total'   => 3,
                'actual_total' => 9,
                'user_id'      => static::$user_1->id,
            ],
            200003 => [
                'plan_total'   => 3,
                'actual_total' => 10,
                'user_id'      => static::$user_1->id,
            ],
            200002 => [
                'plan_total'   => 2,
                'actual_total' => 7,
                'user_id'      => static::$user_1->id,
            ],
        ];

        $this->assertEquals($expect, $result);
    }

}
