<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Services\Roster\RosterAccept;

class UnitRosterAcceptTest extends TestCase
{

    const USER_CNT     = 5;
    const PLAN_MONTH   = 201701;
    const ACTUAL_MONTH = 201702;

    protected $user_1;
    protected $user_2;
    protected $another_div_user;
    protected $chief_user;
    protected $before;
    protected $after;
    protected $ids;

    private function setReflection($class, $function_name) {
        $s = new \ReflectionMethod($class, $function_name);
        $s->setAccessible(true);
        return $s;
    }

    public function setUp() {
        parent::setUp();
        \App\ControlDivision::truncate();
        \App\SinrenUser::truncate();
        \App\RosterUser::truncate();
        \App\Division::truncate();
        \App\User::truncate();
        \App\Roster::truncate();


        $users = [];
        for ($i = 0; $i < 4; $i++) {
            $user    = factory(\App\User::class)->create();
            \App\SinrenUser::create(['user_id' => $user->id, 'division_id' => $i + 1,]);
            \App\Division::create(['division_id' => $i + 1, 'division_name' => "division_{$i}"]);
            $users[] = $user;
        }

        $this->user_1           = $users[0];
        $this->user_2           = $users[1];
        $this->another_div_user = $users[2];
        $this->chief_user       = $users[3];

        \App\ControlDivision::create(['user_id' => $this->chief_user->id, 'division_id' => 1,]);
        \App\ControlDivision::create(['user_id' => $this->chief_user->id, 'division_id' => 2,]);
        \App\ControlDivision::create(['user_id' => $this->chief_user->id, 'division_id' => 4,]);
        \App\SinrenUser::create(['user_id' => $this->chief_user->id, 'division_id' => 1,]);
    }

    private function createRoster($plan_or_actual = 'plan') {
        $month     = ($plan_or_actual == 'plan') ? self::PLAN_MONTH : self::ACTUAL_MONTH;
        $is_actual = ($plan_or_actual == 'plan') ? false : true;
        $array_id  = [$this->user_1->id, $this->user_2->id, $this->another_div_user->id, $this->chief_user->id,];
        foreach ($array_id as $id) {
            $last_day = date('t', strtotime($month . '01'));
            for ($d = 1; $d <= $last_day; $d++) {
                $day   = $month . sprintf('%02d', $d);
                $array = [
                    'is_plan_entry'   => true,
                    'is_actual_entry' => $is_actual,
                    'is_plan_accept'  => $is_actual,
                    'user_id'         => $id,
                    'entered_on'      => date('Y-m-d', strtotime($day)),
                ];
                \App\Roster::create($array);
            }
        }
    }

    /**
     * @tests
     */
    public function 異常系_責任者IDが見つからない() {
        try {
            new RosterAccept(0);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals('No query results for model [App\User].', $e->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 異常系_勤務データが見つからない() {
        $s = new RosterAccept($this->chief_user->id);
        try {
            $s->updateRoster([]);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals('勤務データIDがセットされていないようです。', $e->getMessage());
        }
        try {
            $s->updateRoster(['id' => ['not found', 'not found',]]);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals('No query results for model [App\Roster].', $e->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 異常系_責任者の管轄部署がない() {
        \App\ControlDivision::truncate();
        try {
            new RosterAccept($this->chief_user->id);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals('責任者の管轄部署が存在しません。', $e->getMessage());
        }
    }

//    /**
//     * @tests
//     */
//    public function 異常系_許可されていない部署のデータを編集しようとする() {
//        \App\Roster::truncate();
//        $array  = ['user_id' => $this->another_div_user->id];
//        $roster = \App\Roster::create($array);
//        $s      = new RosterAccept($this->chief_user->id);
//        try {
//            $s->updateRoster(['id' => [$roster->id,]]);
//            $this->fail('エラー：例外が発生しませんでした。');
//        } catch (\Exception $e) {
//            $this->assertEquals('許可されていない部署のデータを承認しようとしました。', $e->getMessage());
//        }
//    }

//    /**
//     * @tests
//     */
//    public function 異常系_自分自身を更新しようとする() {
//        \App\Roster::truncate();
//        $array  = ['user_id' => $this->chief_user->id];
//        $roster = \App\Roster::create($array);
//        $s      = new RosterAccept($this->chief_user->id);
//        try {
//            $s->updateRoster(['id' => [$roster->id,]]);
//            $this->fail('エラー：例外が発生しませんでした。');
//        } catch (\Exception $e) {
//            $this->assertEquals('自分自身のデータを承認しようとしました。', $e->getMessage());
//        }
//    }

    /**
     * @tests
     */
    public function 正常系_予定データの更新に成功する() {
        \App\Roster::truncate();
        $this->createRoster('plan');
        $roster   = [
            \App\Roster::where(['entered_on' => self::PLAN_MONTH . '01', 'user_id' => $this->user_1->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::PLAN_MONTH . '02', 'user_id' => $this->user_1->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::PLAN_MONTH . '01', 'user_id' => $this->user_2->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::PLAN_MONTH . '02', 'user_id' => $this->user_2->id])->firstOrFail(),
        ];
        $array_id = [$roster[0]->id, $roster[1]->id, $roster[2]->id, $roster[3]->id];

        $inputs = [
            'id'          => $array_id,
            'plan'        => [$array_id[0] => 0, $array_id[1] => 1, $array_id[2] => 0, $array_id[3] => 1],
            'plan_reject' => [$array_id[0] => 'NG_1', $array_id[1] => '', $array_id[2] => 'NG_2', $array_id[3] => ''],
        ];

        $s   = new RosterAccept($this->chief_user->id);
        $s->updateRoster($inputs);
        $res = [
            \App\Roster::where(['entered_on' => self::PLAN_MONTH . '01', 'user_id' => $this->user_1->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::PLAN_MONTH . '02', 'user_id' => $this->user_1->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::PLAN_MONTH . '01', 'user_id' => $this->user_2->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::PLAN_MONTH . '02', 'user_id' => $this->user_2->id])->firstOrFail(),
        ];
        $this->assertEquals($res[0]->is_plan_accept, 0);
        $this->assertEquals($res[0]->is_plan_reject, 1);
        $this->assertEquals($res[0]->plan_accept_user_id, 0);
        $this->assertEquals($res[0]->plan_reject_user_id, $this->chief_user->id);
        $this->assertEquals($res[0]->reject_reason, 'NG_1');

        $this->assertEquals($res[1]->is_plan_accept, 1);
        $this->assertEquals($res[1]->is_plan_reject, 0);
        $this->assertEquals($res[1]->plan_accept_user_id, $this->chief_user->id);
        $this->assertEquals($res[1]->plan_reject_user_id, 0);
        $this->assertEquals($res[1]->reject_reason, '');

        $this->assertEquals($res[2]->is_plan_accept, 0);
        $this->assertEquals($res[2]->is_plan_reject, 1);
        $this->assertEquals($res[2]->plan_accept_user_id, 0);
        $this->assertEquals($res[2]->plan_reject_user_id, $this->chief_user->id);
        $this->assertEquals($res[2]->reject_reason, 'NG_2');

        $this->assertEquals($res[3]->is_plan_accept, 1);
        $this->assertEquals($res[3]->is_plan_reject, 0);
        $this->assertEquals($res[3]->plan_accept_user_id, $this->chief_user->id);
        $this->assertEquals($res[3]->plan_reject_user_id, 0);
        $this->assertEquals($res[3]->reject_reason, '');
    }

    /**
     * @tests
     */
    public function 正常系_実績データの更新に成功する() {
        \App\Roster::truncate();
        $this->createRoster('actual');
        $roster   = [
            \App\Roster::where(['entered_on' => self::ACTUAL_MONTH . '01', 'user_id' => $this->user_1->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::ACTUAL_MONTH . '02', 'user_id' => $this->user_1->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::ACTUAL_MONTH . '01', 'user_id' => $this->user_2->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::ACTUAL_MONTH . '02', 'user_id' => $this->user_2->id])->firstOrFail(),
        ];
        $array_id = [$roster[0]->id, $roster[1]->id, $roster[2]->id, $roster[3]->id];

        $inputs = [
            'id'            => $array_id,
            'actual'        => [$array_id[0] => 0, $array_id[1] => 1, $array_id[2] => 0, $array_id[3] => 1],
            'actual_reject' => [$array_id[0] => 'NG_1', $array_id[1] => '', $array_id[2] => 'NG_2', $array_id[3] => ''],
        ];

        $s   = new RosterAccept($this->chief_user->id);
        $s->updateRoster($inputs);
        $res = [
            \App\Roster::where(['entered_on' => self::ACTUAL_MONTH . '01', 'user_id' => $this->user_1->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::ACTUAL_MONTH . '02', 'user_id' => $this->user_1->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::ACTUAL_MONTH . '01', 'user_id' => $this->user_2->id])->firstOrFail(),
            \App\Roster::where(['entered_on' => self::ACTUAL_MONTH . '02', 'user_id' => $this->user_2->id])->firstOrFail(),
        ];
        $this->assertEquals($res[0]->is_actual_accept, 0);
        $this->assertEquals($res[0]->is_actual_reject, 1);
        $this->assertEquals($res[0]->actual_accept_user_id, 0);
        $this->assertEquals($res[0]->actual_reject_user_id, $this->chief_user->id);
        $this->assertEquals($res[0]->reject_reason, 'NG_1');

        $this->assertEquals($res[1]->is_actual_accept, 1);
        $this->assertEquals($res[1]->is_actual_reject, 0);
        $this->assertEquals($res[1]->actual_accept_user_id, $this->chief_user->id);
        $this->assertEquals($res[1]->actual_reject_user_id, 0);
        $this->assertEquals($res[1]->reject_reason, '');

        $this->assertEquals($res[2]->is_actual_accept, 0);
        $this->assertEquals($res[2]->is_actual_reject, 1);
        $this->assertEquals($res[2]->actual_accept_user_id, 0);
        $this->assertEquals($res[2]->actual_reject_user_id, $this->chief_user->id);
        $this->assertEquals($res[2]->reject_reason, 'NG_2');

        $this->assertEquals($res[3]->is_actual_accept, 1);
        $this->assertEquals($res[3]->is_actual_reject, 0);
        $this->assertEquals($res[3]->actual_accept_user_id, $this->chief_user->id);
        $this->assertEquals($res[3]->actual_reject_user_id, 0);
        $this->assertEquals($res[3]->reject_reason, '');
    }

    /**
     * @tests
     */
    public function 異常系_予定データ更新に失敗する() {
        \App\Roster::truncate();

        $array    = [
            ['is_plan_entry' => false, 'user_id' => 1, 'entered_on' => self::PLAN_MONTH . '01',],
            ['is_plan_entry' => true, 'is_plan_accept' => true, 'user_id' => 1, 'entered_on' => self::PLAN_MONTH . '02',],
        ];
        $roster_1 = \App\Roster::create($array[0]);
        $roster_2 = \App\Roster::create($array[1]);
        $s        = new RosterAccept($this->chief_user->id);
        $ref      = $this->setReflection($s, 'updatePlan');

        try {
            $ref->invoke($s, $roster_1, [], $roster_1->id, true);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals("{$roster_1->entered_on}の予定データが入力されていないようです。", $e->getMessage());
        }
        try {
            $ref->invoke($s, $roster_2, [], $roster_2->id, true);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals("すでに{$roster_2->entered_on}のデータは承認されています。", $e->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 異常系_実績データ更新に失敗する() {
        \App\Roster::truncate();

        $array    = [
            ['is_plan_entry' => false, 'user_id' => 1, 'entered_on' => self::PLAN_MONTH . '01',],
            ['is_plan_entry' => true, 'user_id' => 1, 'entered_on' => self::PLAN_MONTH . '02',],
            ['is_plan_entry' => true, 'is_plan_accept' => true, 'user_id' => 1, 'entered_on' => self::PLAN_MONTH . '03',],
            ['is_plan_entry' => true, 'is_plan_accept' => true, 'is_actual_entry' => true, 'is_actual_accept' => true, 'user_id' => 1, 'entered_on' => self::PLAN_MONTH . '04',],
        ];
        $roster_1 = \App\Roster::create($array[0]);
        $roster_2 = \App\Roster::create($array[1]);
        $roster_3 = \App\Roster::create($array[2]);
        $roster_4 = \App\Roster::create($array[3]);
        $s        = new RosterAccept($this->chief_user->id);
        $ref      = $this->setReflection($s, 'updateActual');

        try {
            $ref->invoke($s, $roster_1, [], $roster_1->id, true);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals("{$roster_1->entered_on}の予定データが入力されていないようです。", $e->getMessage());
        }
        try {
            $ref->invoke($s, $roster_2, [], $roster_2->id, true);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals("{$roster_2->entered_on}の予定データが承認されていないようです。先に予定の承認を行ってください。", $e->getMessage());
        }
        try {
            $ref->invoke($s, $roster_3, [], $roster_3->id, true);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals("{$roster_3->entered_on}の実績データが入力されていないようです。", $e->getMessage());
        }
        try {
            $ref->invoke($s, $roster_4, [], $roster_4->id, true);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals("すでに{$roster_4->entered_on}のデータは承認されています。", $e->getMessage());
        }
    }

}
