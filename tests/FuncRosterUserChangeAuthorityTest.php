<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class FuncRosterUserChangeAuthorityTest extends TestCase
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
    }

    /**
     * @tests
     */
    public function スーパーユーザーが管理ユーザーリストを見ることができる() {

        $user = factory(\App\User::class)->create(['is_super_user' => '1']);

        $this->actingAs($user)
                ->visit('/admin/roster/user')
                ->seePageIs('/admin/roster/user')
        ;
    }

    /**
     * @tests
     */
    public function 非勤怠責任者ユーザを勤怠責任者ユーザに変更できる() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => 2, 'is_chief' => '1', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/admin/roster/user')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $target_user->id)->first();


        $this->assertEquals(0, $roster_unchanged_user->is_chief);
        $this->assertEquals(1, $roster_changed_user->is_chief);
    }

    /**
     * @tests
     */
    public function 権限のないユーザーが非勤怠責任者ユーザを勤怠責任者ユーザに変更するとエラー() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($target_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '1', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 勤怠責任者ユーザを非勤怠責任者ユーザに変更できる() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '1', "is_proxy" => '0', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/admin/roster/user')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $target_user->id)->first();


        $this->assertEquals(1, $roster_unchanged_user->is_chief);
        $this->assertEquals(0, $roster_changed_user->is_chief);
    }

    /**
     * @tests
     */
    public function 権限のないユーザーが勤怠責任者ユーザを非勤怠責任者ユーザに変更するとエラー() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '1', "is_proxy" => '0', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($target_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 非勤怠責任者代理ユーザを非勤怠責任者代理ユーザに変更できる() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '1', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/admin/roster/user')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $target_user->id)->first();


        $this->assertEquals(0, $roster_unchanged_user->is_proxy);
        $this->assertEquals(1, $roster_changed_user->is_proxy);
    }

    /**
     * @tests
     */
    public function 権限のないユーザーが非勤怠責任者代理ユーザを非勤怠責任者代理ユーザに変更するとエラー() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($target_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '1', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 勤怠責任者代理ユーザを非勤怠責任者代理ユーザに変更できる() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/admin/roster/user')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $target_user->id)->first();

        $this->assertEquals(1, $roster_unchanged_user->is_proxy);
        $this->assertEquals(0, $roster_changed_user->is_proxy);
    }

    /**
     * @tests
     */
    public function 権限のないユーザーが勤怠責任者代理ユーザを非勤怠責任者代理ユーザに変更するとエラー() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($target_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 責任者代理機能を有効に変更できる() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '1', 'is_proxy_active' => '1', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/admin/roster/user')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $target_user->id)->first();


        $this->assertEquals(0, $roster_unchanged_user->is_proxy_active);
        $this->assertEquals(1, $roster_changed_user->is_proxy_active);
    }

    /**
     * @tests
     */
    public function 権限のないユーザーが責任者代理機能を有効に変更するとエラー() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($target_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '1', 'is_proxy_active' => '1', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 責任者代理機能を無効に変更できる() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '1']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '1', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/admin/roster/user')
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $target_user->id)->first();


        $this->assertEquals(1, $roster_unchanged_user->is_proxy_active);
        $this->assertEquals(0, $roster_changed_user->is_proxy_active);
    }

    /**
     * @tests
     */
    public function 権限のないユーザーが責任者代理機能を無効に変更するとエラー() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '1', "is_proxy_active" => '1']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($target_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '1', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 勤怠管理に登録されていないユーザーを変更するとエラー() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user  = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0']));
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => 61, 'is_chief' => '1', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/admin/roster/user/' . $target_user->id)
        ;
    }

    /**
     * @tests
     */
    public function 責任者と責任者代理に同時にしようとするとするとエラー() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user  = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0']));
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => 61, 'is_chief' => '1', 'is_proxy' => '1', 'is_proxy_active' => '0', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/admin/roster/user/' . $target_user->id)
        ;
    }

    /**
     * @tests
     */
    public function 責任者代理以外のユーザーに責任者代理機能を有効にしようとするとエラー() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        $supar_user            = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user           = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0']));
        $roster_unchanged_user = \App\RosterUser::where('user_id', $target_user->id)->first();
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => 61, 'is_chief' => '', 'is_proxy' => '', 'is_proxy_active' => '1', 'control_division' => [0 => 0]])
                ->assertRedirectedTo('/admin/roster/user/' . $target_user->id)
        ;
        $roster_changed_user   = \App\RosterUser::where('user_id', $target_user->id)->first();
    }

    /**
     * @tests
     */
    public function 管理者が管轄部署を登録できる() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        \App\ControlDivision::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        factory(\App\SinrenDivision::class)->create(['division_id' => '2']);
        $supar_user                = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user               = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '1', "is_proxy" => '0', "is_proxy_active" => '0']));
        $before_control_divisionsr = \App\ControlDivision::where('user_id', $target_user->id)->count();
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '1', 'is_proxy' => '0', 'is_proxy_active' => '0', 'control_division' => [0 => 1]])
                ->assertRedirectedTo('/admin/roster/user')
        ;
        $after_control_divisionsr  = \App\ControlDivision::where('user_id', $target_user->id)->count();


        $this->assertEquals(0, $before_control_divisionsr);
        $this->assertEquals(1, $after_control_divisionsr);
    }

    /**
     * @tests
     */
    public function 管理者代理が管轄部署を登録できる() {
        \App\User::truncate();
        \App\SinrenUser::truncate();
        \App\SinrenDivision::truncate();
        \App\RosterUser::truncate();
        \App\ControlDivision::truncate();
        factory(\App\SinrenDivision::class)->create(['division_id' => '1']);
        factory(\App\SinrenDivision::class)->create(['division_id' => '2']);
        $supar_user                = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user               = factory(\App\User::class)->create(['is_super_user' => '0']);
        factory(\App\SinrenUser::class)->create(['user_id' => $target_user->id, 'division_id' => '1']);
        factory(\App\RosterUser::create(['user_id' => $target_user->id, "is_administrator" => '0', "is_chief" => '0', "is_proxy" => '0', "is_proxy_active" => '0']));
        $before_control_divisionsr = \App\ControlDivision::where('user_id', $target_user->id)->count();
        $this->actingAs($supar_user)
                ->visit('/admin/roster/user/' . $target_user->id)
                ->post('/admin/roster/user/edit', ['_token' => csrf_token(), 'id' => $target_user->id, 'is_chief' => '0', 'is_proxy' => '1', 'is_proxy_active' => '0', 'control_division' => [0 => 1]])
                ->assertRedirectedTo('/admin/roster/user')
        ;
        $after_control_divisionsr  = \App\ControlDivision::where('user_id', $target_user->id)->count();


        $this->assertEquals(0, $before_control_divisionsr);
        $this->assertEquals(1, $after_control_divisionsr);
    }

}
