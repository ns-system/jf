<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FuncRosterUserControllerTest extends TestCase
{

    protected static $init = false;
    protected $user;
    protected $chief;
    protected $admin;

    public function setUp() {
        parent::setUp();
        if (static::$init)
        {
            return true;
        }
        \App\Division::truncate();
        \App\WorkType::truncate();

        $this->user  = factory(\App\User::class)->create();
        $this->chief = factory(\App\User::class)->create();
        $this->admin = factory(\App\User::class)->create();
        $this->super = factory(\App\User::class)->create();
        \App\Division::insert([['division_id' => 1, 'division_name' => 'division_1'], ['division_id' => 2, 'division_name' => 'division_2']]);
        \App\WorkType::insert([['work_type_id' => 1, 'work_type_name' => 'work_type_1'], ['work_type_id' => 2, 'work_type_name' => 'work_type_2']]);

        $this->super->is_super_user = true;
        $this->super->save();
    }

    /**
     * @tests
     */
    public function 正常系_一般ユーザーで部署の登録ができる() {
        $actor = $this->user;
        $this->actingAs($actor)
                ->visit('/app/roster/user/' . $actor->id)
                ->select('1', 'division_id')
                ->select('2', 'work_type_id')
                ->press('submit')
                ->seePageIs('/app/roster/user/' . $actor->id)
                ->see('ユーザーの更新が完了しました。')
                ->dontSee('要修正')
                ->check('is_chief')
                ->press('submit')
                ->seePageIs('/app/roster/user/' . $actor->id)
                ->see('ユーザーの更新が完了しました。')
                ->dontSee('要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_一般ユーザーで他ユーザーの変更ができない() {
        $actor = $this->user;
        $this->actingAs($actor)
                ->visit('/app/roster/user/' . $this->admin->id)
                ->seePageIs('/permission_error')

        ;
    }

    /**
     * @tests
     */
    public function 異常系_存在しないユーザーを編集しようとする() {
        $actor = $this->super;
        $this->actingAs($actor)
                ->visit('/admin/roster/user/' . 0)
                ->seePageIs('/permission_error')
                ->dontSee('要修正')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_管理ユーザーで部署の登録ができる() {

        $actor = $this->super;
        $user  = $this->user;
        $this->actingAs($actor)
                ->visit('/app/roster/user/' . $user->id)
                ->select('1', 'division_id')
                ->select('2', 'work_type_id')
                ->press('submit')
                ->seePageIs('/app/roster/user/' . $user->id)
                ->see('ユーザーの更新が完了しました。')
                ->dontSee('要修正')
        ;
    }

}
