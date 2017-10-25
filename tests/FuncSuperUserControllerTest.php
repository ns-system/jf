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
    }
    /**
     * @tests
     */
    public function スーパーユーザーが管理ユーザー設定を見ることができる() {
        
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);

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
        \App\User::truncate();
        $supar_user = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user =factory(\App\User::class)->create();
        $this->actingAs($supar_user)
                ->visit('/admin/super_user/user/'.$target_user->id)
                ->see($target_user->first_name." ".$target_user->last_name)
                ->select('1', 'is_super_user')
                ->press('更新する')
                ->seePageIs('/admin/super_user/user')
        ;
        $changed_user = \App\User::find($target_user->id);
        $this->assertEquals(1, $changed_user->is_super_user);
    }
    /**
     * @tests
     */
    public function ユーザーを名前で検索できる() {
        \App\User::truncate();
        $supar_user = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user =factory(\App\User::class)->create(['is_super_user' => '1']);
        $this->actingAs($supar_user)
                ->visit('/admin/super_user/user')
                ->type($target_user->name, "name")
                ->press('検索する')               
                ->see($target_user->email)
                ->dontSee($supar_user->email)
        ;
        
    }
    /**
     * @tests
     */
    public function ユーザーをメールアドレスで検索できる() {
        \App\User::truncate();
        $supar_user = factory(\App\User::class)->create(['is_super_user' => '1']);
        $target_user =factory(\App\User::class)->create(['is_super_user' => '1']);
        $this->actingAs($supar_user)
                ->visit('/admin/super_user/user')
                ->type($target_user->email, "mail")
                ->press('検索する')               
                ->see($target_user->name)
                ->dontSee($supar_user->name)
        ;
        
    }
    /**
     * @tests
     */
    public function スーパーユーザーを検索できる() {
        \App\User::truncate();
        $supar_user = factory(\App\User::class)->create(['is_super_user' => '1']);
        $normal_user = factory(\App\User::class)->create(['is_super_user' => '0']);
        $this->actingAs($supar_user)
                ->visit('/admin/super_user/user')
                ->select('1', 'super')
                ->press('検索する')               
                ->see($supar_user->name)
                ->dontSee($normal_user->name)
        ;
        
    }
     /**
     * @tests
     */
    public function 一般ユーザーを検索できる() {
        \App\User::truncate();
        $supar_user = factory(\App\User::class)->create(['is_super_user' => '1']);
        $normal_user = factory(\App\User::class)->create(['is_super_user' => '0']);
        $this->actingAs($supar_user)
                ->visit('/admin/super_user/user')
                ->select('0', 'super')
                ->press('検索する')               
                ->see($normal_user->name)
                ->dontSee($supar_user->name)
        ;
        
    }
}
