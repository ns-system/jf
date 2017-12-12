<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FuncNikocakeControllerTest extends TestCase
{

    use \App\Services\Traits\Testing\DbDisconnectable;

//    use DatabaseMigrations;

    protected $connection  = ['mysql_laravel', 'mysql_suisin', 'mysql_sinren', 'mysql_nikocale'];
    protected static $init = false;

    /**
     * @before
     */
    public function setUpDatabase() {
//            echo "in ";
        if (!static::$init)
        {
//            echo "mig ";
            static::$init = true;

            \Artisan::call('db:reset', ['--dbenv' => 'testing', '--hide' => 'true']);
            \Artisan::call('db:create', ['--dbenv' => 'testing', '--hide' => 'true']);
            \Artisan::call('migrate');
        }
    }

    public function tearDown() {
        $this->disconnect();
        parent::tearDown();
    }

    /**
     * @tests
     */
    public function 画面が見れる() {
        $user = [];
        for ($i = 0; $i < 4; $i++) {
            $user[] = factory(\App\User::class)->create();
        }
        \App\SinrenDivision::insert([['division_id' => 9999, 'division_name' => 'System'], ['division_id' => 9998, 'division_name' => 'Sales']]);
        /* $sinren_user_1 = */ factory(\App\SinrenUser::class)->create(['user_id' => $user[0]->id, 'division_id' => 9999]);
        /* $sinren_user_2 = */ factory(\App\SinrenUser::class)->create(['user_id' => $user[1]->id, 'division_id' => 9999]);
        /* $sinren_user_3 = */ factory(\App\SinrenUser::class)->create(['user_id' => $user[2]->id, 'division_id' => 9998]);
        /* $sinren_user_4 = */ factory(\App\SinrenUser::class)->create(['user_id' => $user[3]->id, 'division_id' => 9998]);
        for ($i = 0; $i < 40; $i++) {
            $date = factory(\App\Emotion::class)->create(['user_id' => $user[rand(0, 3)]->id,]);
        }
        $acter      = $user[0];
        \App\Emotion::where(['entered_on' => $date, 'user_id' => $acter->id,])->delete();
        $date       = $date->entered_on;
        $monthly_id = date('Ym', strtotime($date));

        $this->actingAs($acter)
                ->visit(route('app::nikocale::index', ['monthly_id' => $monthly_id]))
                ->seePageIs('/app/nikocale/index/' . $monthly_id)
                ->see('System')
                ->see($acter->first_name)
                ->see($user[1]->first_name)
                ->dontSee($user[2]->first_name)
                ->dontSee($user[3]->first_name)
                ->see("#{$date}_{$acter->id}")
                ->dontSee("#{$date}_{$user[1]->id}")
                ->type('1', 'emotion')
                ->type('特に何もありませんでした。', 'comment')
                ->press("submit_{$date}_{$acter->id}")
                ->see("データが更新されました。")
        ;
        $result_1 = \App\Emotion::where(['user_id' => $acter->id, 'entered_on' => $date])->first()->toArray();
        $this->assertEquals('特に何もありませんでした。', $result_1['comment']);
        $this->assertEquals(1, $result_1['emotion']);

        $this->type('3', 'emotion')
                ->type('やな感じ。', 'comment')
                ->press("submit_{$date}_{$acter->id}")
        ;
        $result_2 = \App\Emotion::where(['user_id' => $acter->id, 'entered_on' => $date])->first()->toArray();
        $this->assertEquals('やな感じ。', $result_2['comment']);
        $this->assertEquals(3, $result_2['emotion']);

        $this->actingAs($acter)
                ->visit(route('app::nikocale::index', ['monthly_id' => $monthly_id]))
                ->seePageIs('/app/nikocale/index/' . $monthly_id)
                ->see("destroy_{$result_2['id']}_{$acter->id}")
                ->click("destroy_{$result_2['id']}_{$acter->id}")
                ->see("データが削除されました。")
        ;
        $result_3 = \App\Emotion::where(['user_id' => $acter->id, 'entered_on' => $date])->get();
        $this->assertTrue($result_3->isEmpty());
    }

}
