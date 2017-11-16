<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FuncSuperUserControllerTest
 *
 * @author r-kawanishi
 */
class FuncSuperUserControllerTest extends TestCase
{

    protected static $init = false;
    protected $user;

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
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
    }

    /**
     * @tests
     */
    public function スーパーユーザーが管理ユーザー設定を見ることができる() {

        $user = factory(\App\User::class)->create(['is_super_user' => true]);

        $this->actingAs($user)
                ->visit('/admin/super_user/user')
                ->seePageIs('/admin/super_user/user')
        ;
    }

    /**
     * @tests
     */
    public function スーパーユーザー以外のユーザーが管理ユーザーページを見たときエラーになる() {
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->visit('/admin/super_user/user')
                ->see('許可されていないアクセスを行おうとしました。')
        ;
    }

    /**
     * @tests
     */
    public function 一般ユーザーをスーパーユーザに変更できる() {
//        \App\User::truncate();
        $super_user   = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user  = factory(\App\User::class)->create(['is_super_user' => false]);
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user/' . $target_user->id)
                ->see($target_user->first_name . " " . $target_user->last_name)
                ->select('1', 'is_super_user')
                ->press('更新する')
                ->seePageIs('/admin/super_user/user')
        ;
        $changed_user = \App\User::find($target_user->id);
        $this->assertEquals(0, $target_user->is_super_user);
        $this->assertEquals(1, $changed_user->is_super_user);
    }

    /**
     * @tests
     */
    public function スーパーユーザを一般ユーザーに変更できる() {
//        \App\User::truncate();
        $super_user   = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user  = factory(\App\User::class)->create(['is_super_user' => true]);
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user/' . $target_user->id)
                ->see($target_user->first_name . " " . $target_user->last_name)
                ->select('0', 'is_super_user')
                ->press('更新する')
                ->seePageIs('/admin/super_user/user')
        ;
        $changed_user = \App\User::find($target_user->id);
        $this->assertEquals(1, $target_user->is_super_user);
        $this->assertEquals(0, $changed_user->is_super_user);
    }

//    /**
//     * @tests
//     */
//    public function 推進一般ユーザーを推進管理ユーザーに変更できる() {
//        \App\User::truncate();
//        \App\SuisinUser::truncate();
//        $super_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
//        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
//        factory(\App\SuisinUser::create(['user_id' => $target_user->id, "is_administrator" => '0']));
//        $suisin_unchanged_user = \App\SuisinUser::where('user_id', $target_user->id)->first();
//        $this->actingAs($super_user)
//                ->visit('/admin/super_user/user/' . $target_user->id)
//                ->see($target_user->first_name . " " . $target_user->last_name)
//                ->select('1', 'suisin_is_administrator')
//                ->press('更新する')
//                ->seePageIs('/admin/super_user/user')
//        ;
//        $suisin_changed_user   = \App\SuisinUser::where('user_id', $target_user->id)->first();
//        $this->assertEquals(0, $suisin_unchanged_user->is_administrator);
//        $this->assertEquals(1, $suisin_changed_user->is_administrator);
//    }
//
//    /**
//     * @tests
//     */
//    public function 推進管理ユーザーを推進一般ユーザーに変更できる() {
//        \App\User::truncate();
//        \App\SuisinUser::truncate();
//        $super_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
//        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
//        factory(\App\SuisinUser::create(['user_id' => $target_user->id, "is_administrator" => '1']));
//        $suisin_unchanged_user = \App\SuisinUser::where('user_id', $target_user->id)->first();
//        $this->actingAs($super_user)
//                ->visit('/admin/super_user/user/' . $target_user->id)
//                ->see($target_user->first_name . " " . $target_user->last_name)
//                ->select('0', 'suisin_is_administrator')
//                ->press('更新する')
//                ->seePageIs('/admin/super_user/user')
//        ;
//        $suisin_changed_user   = \App\SuisinUser::where('user_id', $target_user->id)->first();
//        $this->assertEquals(1, $suisin_unchanged_user->is_administrator);
//        $this->assertEquals(0, $suisin_changed_user->is_administrator);
//    }

    /**
     * @tests
     */
    public function 勤怠一般ユーザーを勤怠管理ユーザに変更できる() {
//        \App\User::truncate();
//        \App\SinrenUser::truncate();
//        \App\SinrenDivision::truncate();
//        \App\RosterUser::truncate();
        $div                   = \App\SinrenDivision::create(['division_id' => 1, 'division_name' => 'division_1']);
        $super_user            = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => false]);
        \App\SinrenUser::create(['user_id' => $target_user->id, 'division_id' => $div->division_id]);
        \App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => false]);
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user/' . $target_user->id)
                ->see($target_user->first_name . " " . $target_user->last_name)
                ->select('1', 'roster_is_administrator')
                ->press('更新する')
                ->seePageIs('/admin/super_user/user')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->assertEquals(0, $roster_unchanged_user->is_administrator);
        $this->assertEquals(1, $roster_changed_user->is_administrator);
    }

    /**
     * @tests
     */
    public function 勤怠管理ユーザを勤怠一般ユーザに変更できる() {
//        \App\User::truncate();
//        \App\SinrenUser::truncate();
//        \App\SinrenDivision::truncate();
//        \App\RosterUser::truncate();
        $div                   = \App\SinrenDivision::create(['division_id' => 1, 'division_name' => 'division_1']);
        $super_user            = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => false]);
        \App\SinrenUser::create(['user_id' => $target_user->id, 'division_id' => $div->division_id]);
        \App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => true]);
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user/' . $target_user->id)
                ->see($target_user->first_name . " " . $target_user->last_name)
                ->select('0', 'roster_is_administrator')
                ->press('更新する')
                ->seePageIs('/admin/super_user/user')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->assertEquals(1, $roster_unchanged_user->is_administrator);
        $this->assertEquals(0, $roster_changed_user->is_administrator);
    }

    /**
     * @tests
     */
    public function スーパーユーザー以外がユーザーをスーパーユーザーにしようとするとエラー() {
        \Session::start();
//        \App\User::truncate();
        $super_user  = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user = factory(\App\User::class)->create(['is_super_user' => '0']);
        $this->actingAs($target_user)
                ->POST('/admin/super_user/user/edit/' . $target_user->id, ['_token' => csrf_token(), 'is_super_user' => "1"])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function スーパーユーザー以外がユーザーを推進管理ユーザーにしようとするとエラー() {
        \Session::start();
//        \App\User::truncate();
//        \App\SuisinUser::truncate();
        $super_user  = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user = factory(\App\User::class)->create(['is_super_user' => false]);
        \App\SuisinUser::create(['user_id' => $target_user->id, "is_administrator" => false]);
        $this->actingAs($target_user)
                ->POST('/admin/super_user/user/edit/' . $target_user->id, ['_token' => csrf_token(), 'suisin_is_administrator' => "1"])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function スーパーユーザー以外がユーザーを勤怠管理ユーザーにしようとするとエラー() {
        \Session::start();
//        \App\User::truncate();
//        \App\RosterUser::truncate();
//        \App\SinrenUser::truncate();
//        \App\SinrenDivision::truncate();
        $div         = \App\SinrenDivision::create(['division_id' => 1, 'division_name' => 'division_1']);
        $super_user  = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user = factory(\App\User::class)->create(['is_super_user' => false]);
        \App\SinrenUser::create(['user_id' => $target_user->id, 'division_id' => $div->division_id]);
        \App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => false]);
        $this->actingAs($target_user)
                ->POST('/admin/super_user/user/edit/' . $target_user->id, ['_token' => csrf_token(), 'roster_is_administrator' => "1"])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function is_super_userに誤った入力がされるとエラー() {
        \Session::start();
//        \App\User::truncate();
//        \App\RosterUser::truncate();
//        \App\SinrenUser::truncate();
//        \App\SinrenDivision::truncate();
        $div         = \App\SinrenDivision::create(['division_id' => 1, 'division_name' => 'division_1']);
        $super_user  = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user = factory(\App\User::class)->create(['is_super_user' => false]);
        \App\SinrenUser::create(['user_id' => $target_user->id, 'division_id' => $div->division_id]);
        \App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => false]);
        $this->actingAs($super_user)
                ->POST('/admin/super_user/user/edit/' . $target_user->id, ['_token' => csrf_token(), 'is_super_user' => "jat@e"])
                ->assertSessionHasErrors()
        ;
    }

    /**
     * @tests
     */
    public function suisin_is_administratorに誤った入力がされるとエラー() {
        \Session::start();
//        \App\User::truncate();
//        \App\RosterUser::truncate();
//        \App\SinrenUser::truncate();
//        \App\SinrenDivision::truncate();
        $div         = \App\SinrenDivision::create(['division_id' => 1, 'division_name' => 'division_1']);
        $super_user  = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user = factory(\App\User::class)->create(['is_super_user' => false]);
        \App\SinrenUser::create(['user_id' => $target_user->id, 'division_id' => $div->division_id]);
        \App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => false]);
        $this->actingAs($super_user)
                ->POST('/admin/super_user/user/edit/' . $target_user->id, ['_token' => csrf_token(), 'suisin_is_administrator' => "jat@e"])
                ->assertSessionHasErrors()
        ;
    }

    /**
     * @tests
     */
    public function roster_is_administratorに誤った入力がされるとエラー() {
        \Session::start();
//        \App\User::truncate();
//        \App\RosterUser::truncate();
//        \App\SinrenUser::truncate();
//        \App\SinrenDivision::truncate();
        $div         = \App\SinrenDivision::create(['division_id' => 1, 'division_name' => 'division_1']);
        $super_user  = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user = factory(\App\User::class)->create(['is_super_user' => false]);
        \App\SinrenUser::create(['user_id' => $target_user->id, 'division_id' => $div->division_id]);
        \App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => false]);
        $this->actingAs($super_user)
                ->POST('/admin/super_user/user/edit/' . $target_user->id, ['_token' => csrf_token(), 'roster_is_administrator' => "jat@e"])
                ->assertSessionHasErrors()
        ;
    }

    /**
     * @tests
     */
    public function ユーザーを名前で検索できる() {
//        \App\User::truncate();
        $super_user   = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user  = factory(\App\User::class)->create(['is_super_user' => true]);
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user')
                ->type($target_user->last_name, "name")
                ->press('検索する')
                ->see($target_user->email)
                ->dontSee($super_user->email)
        ;
        $changed_user = \App\User::find($target_user->id);
    }

    /**
     * @tests
     */
    public function ユーザーをメールアドレスで検索できる() {
//        \App\User::truncate();
        $super_user  = factory(\App\User::class)->create(['is_super_user' => true]);
        $target_user = factory(\App\User::class)->create(['is_super_user' => true]);
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user')
                ->type($target_user->email, "mail")
                ->press('検索する')
                ->see($target_user->email)
                ->dontSee($super_user->email)
        ;
    }

    /**
     * @tests
     */
    public function スーパーユーザーを検索できる() {
//        \App\User::truncate();
        $super_user  = factory(\App\User::class)->create(['is_super_user' => true]);
        $normal_user = factory(\App\User::class)->create(['is_super_user' => false]);
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user')
                ->select('1', 'super')
                ->press('検索する')
                ->see($super_user->last_name)
                ->dontSee($normal_user->last_name)
        ;
    }

    /**
     * @tests
     */
    public function 一般ユーザーを検索できる() {
//        \App\User::truncate();
        $super_user  = factory(\App\User::class)->create(['is_super_user' => true]);
        $normal_user = factory(\App\User::class)->create(['is_super_user' => false]);
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user')
                ->select('0', 'super')
                ->press('検索する')
                ->see($normal_user->email)
                ->dontSee($super_user->email)
        ;
    }

//    /**
//     * @tests
//     */
//    public function 推進管理者ユーザーを検索できる() {
//        \App\User::truncate();
//        \App\SuisinUser::truncate();
//        $super_user                = factory(\App\User::class)->create(['is_super_user' => '1']);
//        $normal_user               = factory(\App\User::class)->create(['is_super_user' => '0']);
//        $suisin_administrator_user = factory(\App\User::class)->create(['is_super_user' => '0']);
//        factory(\App\SuisinUser::create(['user_id' => $normal_user->id, "is_administrator" => '0']));
//        factory(\App\SuisinUser::create(['user_id' => $suisin_administrator_user->id, "is_administrator" => '1']));
//        $this->actingAs($super_user)
//                ->visit('/admin/super_user/user')
//                ->select('1', 'suisin')
//                ->press('検索する')
//                ->see($suisin_administrator_user->name)
//                ->dontSee($normal_user->name)
//        ;
//    }
//
//    /**
//     * @tests
//     */
//    public function 推進一般ユーザーを検索できる() {
//        \App\User::truncate();
//        \App\SuisinUser::truncate();
//        $super_user                = factory(\App\User::class)->create(['is_super_user' => '1']);
//        $normal_user               = factory(\App\User::class)->create(['is_super_user' => '0']);
//        $suisin_administrator_user = factory(\App\User::class)->create(['is_super_user' => '0']);
//        factory(\App\SuisinUser::create(['user_id' => $normal_user->id, "is_administrator" => '0']));
//        factory(\App\SuisinUser::create(['user_id' => $suisin_administrator_user->id, "is_administrator" => '1']));
//        $this->actingAs($super_user)
//                ->visit('/admin/super_user/user')
//                ->select('0', 'suisin')
//                ->press('検索する')
//                ->see($normal_user->name)
//                ->dontSee($suisin_administrator_user->name)
//        ;
//    }

    /**
     * @tests
     */
    public function 勤怠スーパーユーザを検索できる() {
        $div                = \App\SinrenDivision::create(['division_id' => 1, 'division_name' => 'division_1',]);
        $super_user         = factory(\App\User::class)->create(['is_super_user' => true]);
        $roster_admin_user  = factory(\App\User::class)->create(['is_super_user' => false]);
        $roster_normal_user = factory(\App\User::class)->create(['is_super_user' => false]);
        \App\SinrenUser::create(['user_id' => $roster_admin_user->id, 'division_id' => $div->division_id]);
        \App\RosterUser::create(['user_id' => $roster_admin_user->id, "is_administrator" => true]);
        \App\SinrenUser::create(['user_id' => $roster_normal_user->id, 'division_id' => true]);
        \App\RosterUser::create(['user_id' => $roster_normal_user->id, "is_administrator" => false]);
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user')
                ->select('0', 'roster')
                ->press('検索する')
                ->see($roster_normal_user->last_name)
                ->dontSee($roster_admin_user->last_name)
        ;
    }

    /**
     * @tests
     */
    public function 勤怠一般ユーザを検索できる() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        $div                = \App\SinrenDivision::create(['division_id' => 1, 'division_name' => 'division_1']);
        $super_user         = factory(\App\User::class)->create(['is_super_user' => true]);
        $roster_admin_user  = factory(\App\User::class)->create(['is_super_user' => false]);
        $roster_normal_user = factory(\App\User::class)->create(['is_super_user' => false]);
        \App\SinrenUser::create(['user_id' => $roster_admin_user->id, 'division_id' => $div->division_id]);
        \App\RosterUser::create(['user_id' => $roster_admin_user->id, "is_administrator" => true]);
        \App\SinrenUser::create(['user_id' => $roster_normal_user->id, 'division_id' => $div->division_id]);
        \App\RosterUser::create(['user_id' => $roster_normal_user->id, "is_administrator" => false]);
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user')
                ->select('1', 'roster')
                ->press('検索する')
                ->see($roster_admin_user->email)
                ->dontSee($roster_normal_user->email)
        ;
    }

    /**
     * @tests
     */
    public function 部署で検索できる() {
//        \App\User::truncate();
//        \App\SinrenUser::truncate();
//        \App\SinrenDivision::truncate();
//        \App\RosterUser::truncate();
        $div_1           = \App\SinrenDivision::create(['division_id' => 1, 'division_name' => 'division_1']);
        $div_2           = \App\SinrenDivision::create(['division_id' => 2, 'division_name' => 'division_2']);
        $super_user      = factory(\App\User::class)->create(['is_super_user' => true]);
        $division_1_user = factory(\App\User::class)->create(['is_super_user' => false]);
        $division_2_user = factory(\App\User::class)->create(['is_super_user' => false]);
        \App\SinrenUser::create(['user_id' => $division_1_user->id, 'division_id' => $div_1->division_id]);
        \App\SinrenUser::create(['user_id' => $division_2_user->id, 'division_id' => $div_2->division_id]);
        $this->actingAs($super_user)
                ->visit('/admin/super_user/user')
                ->select($div_1->division_id, 'div')
                ->press('検索する')
                ->see($division_1_user->email)
                ->dontSee($division_2_user->email)
        ;
    }

}
