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
                ->check('is_chief')
                ->press('submit')
                ->seePageIs('/app/roster/user/' . $actor->id)
                ->see('ユーザーの更新が完了しました。')
                ->dontSee('要修正')
                ->select('2', 'work_type_id')
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
                ->POST(route('app::roster::user::edit', ['id' => $this->admin->id]), ['_token' => csrf_token(), 'division_id' => '1', 'work_type_id' => '1'])
                ->assertRedirectedTo('/permission_error')
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
    public function 正常系_登録されていないユーザーを編集したらリダイレクトする() {
        $actor = $this->super;
        $this->actingAs($actor)
                ->visit('/admin/roster/user/' . $this->user->id)
                ->seePageIs(route('app::roster::user::show', ['id' => $this->user->id]))
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
                ->visit('/admin/roster/user')
                ->visit('/app/roster/user/' . $user->id)
                ->select('1', 'division_id')
                ->select('2', 'work_type_id')
                ->press('submit')
                ->seePageIs('/app/roster/user/' . $user->id)
                ->see('ユーザーの更新が完了しました。')
                ->dontSee('要修正')
        ;
    }

    /**
     * @tests
     * 一部radio要素がブラウザから操作できないため、実際の動作はPOSTを偽装してテストしている
     */
    public function 正常系_勤怠管理のユーザー変更画面まで行ける() {
        $actor = $this->super;
        $user  = $this->chief;
        $this->actingAs($actor)
                ->visit('/app/roster/user/' . $user->id)
                ->select('1', 'division_id')
                ->select('2', 'work_type_id')
                ->press('submit')
                ->visit(route('admin::roster::user::show', ['id' => $user->id]))
                ->seePageIs('/admin/roster/user/' . $user->id)
                ->see($user->first_name)
                ->dontSee('要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系_管理ユーザー変更時に存在しないユーザーを指定するとエラーになる() {
        $actor = $this->super;
        \Session::start();
        $this->actingAs($actor)
                ->POST('/admin/roster/user/edit/' . $this->user->id, ['_token' => csrf_token(), 'id' => $this->user->id, 'is_chief' => '1', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => ['0' => 1]])
                ->assertSessionHas('warn_message', 'ユーザーが登録されていないようです。')
        ;
    }

    /**
     * @tests
     */
    public function 正常系_部署の削除ができる() {
        $actor     = $this->super;
        $user      = $this->user;
        $divisions = [];
        for ($i = 0; $i < 5; $i++) {
            $divisions[] = \App\ControlDivision::create(['user_id' => $user->id, 'division_id' => ($i + 1)]);
        }
        $this->actingAs($actor)
                ->visit(route('admin::roster::user::delete', ['id' => $divisions[0]->id]))
                ->visit(route('admin::roster::user::delete', ['id' => 0]))
        ;

        $actual_1 = \App\ControlDivision::find($divisions[0]->id);
        $actual_2 = \App\ControlDivision::find(0);
        $this->assertEquals(null, $actual_1);
        $this->assertEquals(null, $actual_2);
    }

    /**
     * @tests
     */
    public function 正常系_複数管轄部署をまとめて登録できる() {
        $actor = $this->super;
        $user  = $this->chief;

        $this->actingAs($actor)
                ->visit('/app/roster/user/' . $user->id)
                ->select('1', 'division_id')
                ->select('2', 'work_type_id')
                ->press('submit')
                ->POST('/admin/roster/user/edit/' . $user->id, ['_token' => csrf_token(), 'id' => $user->id, 'is_chief' => '1', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => ['0' => 1, '1' => 2, '2' => null]])
        ;

        $actual_1 = \App\ControlDivision::where('user_id', '=', $user->id)->get()->toArray();
        $this->assertEquals(2, count($actual_1));
    }

    /**
     * @tests
     */
    public function 正常系_権限を変えると管轄部署がまとめて削除できる() {
        $actor     = $this->super;
        $user      = $this->chief;
        $divisions = [];
        for ($i = 0; $i < 5; $i++) {
            $divisions[] = \App\ControlDivision::create(['user_id' => $user->id, 'division_id' => ($i + 1)]);
        }
        $this->actingAs($actor)
                ->visit('/app/roster/user/' . $user->id)
                ->select('1', 'division_id')
                ->select('2', 'work_type_id')
                ->press('submit')
                ->POST('admin/roster/user/edit/' . $user->id, ['_token' => csrf_token(), 'id' => $user->id, 'is_chief' => '0', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => ['0' => 0]])
        ;

        $actual_1 = \App\ControlDivision::where('user_id', '=', $user->id)->get()->toArray();
        $this->assertEquals([], $actual_1);
    }

}
