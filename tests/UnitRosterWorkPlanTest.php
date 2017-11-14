<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitRosterWorkPlanTest extends TestCase
{

    protected $s;
    protected $rosters;

    const USER_ID       = 1;
    const MONTH_ID      = 201701;
    const CHIEF_USER_ID = 2;

    private function setReflection($function_name) {
        $s = new \ReflectionMethod($this->s, $function_name);
        $s->setAccessible(true);
        return $s;
    }

    public function __construct() {
        $this->s = new \App\Services\Roster\RosterWorkPlan();
    }

    public function setUp() {
        parent::setUp();
        $roster_1                = [
            'user_id'    => self::USER_ID,
            'entered_on' => '2017-01-01',
            'month_id'   => self::MONTH_ID,
        ];
        $this->rosters['入力済み_1'] = \App\Roster::create($roster_1);
    }

    /**
     * @tests
     */
    public function 異常系_データ登録日カラムエラーで中断() {
        $input_1 = ['entered_on' => ['', '2017-01-01', '2017-01-02'],];
        $input_2 = ['entered_on' => ['this is not date.', '2017-01-01', '2017-01-02'],];
        $service = $this->s;

        try {
            $service->updateWorkPlan($input_1, self::USER_ID, self::MONTH_ID, self::CHIEF_USER_ID);
            $this->fail('エラー：例外発生なし');
        } catch (\Exception $exc) {
            $this->assertEquals('データ登録日カラムに異常があったため、処理を中断しました。', $exc->getMessage());
        }
        try {
            $service->updateWorkPlan($input_2, self::USER_ID, self::MONTH_ID, self::CHIEF_USER_ID);
            $this->fail('エラー：例外発生なし');
        } catch (\Exception $exc) {
            $this->assertEquals('データ登録日カラムに異常があったため、処理を中断しました。', $exc->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 異常系_データが既に入力されていた場合は更新を行わない() {
        $input  = ['entered_on' => ['', '2017-01-01', '2017-01-02'],];
        $s      = $this->setReflection('edit');
        $roster = $this->rosters['入力済み_1'];

        $roster->is_plan_entry   = true;
        $roster->is_actual_entry = true;
        $roster->save();
        $res_1                   = $s->invoke($this->s, $roster, $input, self::USER_ID, '2017-01-01', self::CHIEF_USER_ID);
        $this->assertFalse($res_1);
    }

    /**
     * @tests
     */
    public function 正常系_１レコードの更新が正常に行える() {
        $input = [
            'work_type' => ['2017-01-01' => '1'],
            'rest'      => ['2017-01-01' => '1'],
        ];

        $s     = $this->setReflection('edit');
        $res_1 = $s->invoke($this->s, $this->rosters['入力済み_1'], $input, self::USER_ID, '2017-01-01', self::CHIEF_USER_ID);

        $this->assertTrue($res_1);
    }

    /**
     * @tests
     */
    public function 正常系_レコードの追加が正常に行える() {
        $input   = [
            'entered_on' => ['2000-01-01', '2000-01-02'],
            'work_type'  => [
                '2000-01-01' => '0',
                '2000-01-02' => '2',
            ],
            'rest'       => [
                '2000-01-01' => '1',
                '2000-01-01' => '0',
            ],
        ];
        $service = $this->s;

        $res = $service->updateWorkPlan($input, self::USER_ID, 200001, self::CHIEF_USER_ID);
        $this->assertTrue($res);
    }

}
