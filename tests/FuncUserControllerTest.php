<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FuncUserControllerTest extends TestCase
{

//    use WithoutMiddleware;
//    use DatabaseMigrations;
//test
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
    public function 部署が無いときに変更しようとするとエラーになる() {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->see($user->first_name)
                ->press('edit_division')
                ->seePageIs('/app/user/1')
                ->see('部署は必須です。')
                ->dontSee('成功：要修正')

        ;
    }

    /**
     * @tests
     */
    public function 部署の変更ができる() {
        $user = factory(\App\User::class)->create();
        \App\SinrenDivision::insert([['division_id' => 1, 'division_name' => 'System'], ['division_id' => 2, 'division_name' => 'Sales']]);
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->select(2, 'division_id')
                ->press('edit_division')
                ->seePageIs('/app/user/' . $user->id)
                ->see('部署を変更しました。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 自分以外が部署を変更しようとするとエラーになる() {
        \Session::start();
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->POST('/app/user/division/999999', ['_token' => csrf_token(), 'division_id' => '2'])
                ->assertRedirectedTo('/permission_error')
        ;
    }

}
