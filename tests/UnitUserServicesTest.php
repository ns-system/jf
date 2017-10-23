<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UnitUserServicesTest
 *
 * @author r-kawanishi
 */
use App\Services\Traits\Testing\FileTestable;
class UnitUserServicesTest extends TestCase
{
use FileTestable;
    protected static $init = false;
    protected $user;
    protected $service;

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

    protected $name_change_input     = [
        'first_name'      => '名無し',
        'first_name_kana' => 'ななし',
        'last_name'       => '権兵衛',
        'last_name_kana'  => 'ごんべえ',];
    protected $icon_change_input     = [
        'file' => array(
            'user_icon' => array(
                'test'         => 'true',
                'originalName' => "cat_image_for_success_change_user_icon_test.jpg",
                'mimeType'     => "application/octet-stream",
                'size'         => 7230,
                'error'        => 0,)
        ),];
    protected $password_change_input = [
        'new_password' => 'newpassword',
    ];
    protected $password_match_input  = [
        'password' => 'password',
    ];
    protected $division_change_input = [
        'division_id' => '2',
    ];

    /**
     * @tests
     */
    public function 異常系名前変更時ユーザーが存在しない() {
        \App\User::truncate();
        $user    = factory(\App\User::class)->create();
        $service = new \App\Services\UserService();
        try {
            $service->editUserName($user->id + 1, $this->name_change_input);
            $this->fail('例外発生なし');
        } catch (Exception $ex) {
            $this->assertEquals("No query results for model [App\User].", $ex->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系名前の変更ができる() {
        $user              = factory(\App\User::class)->create();
        $service           = new \App\Services\UserService();
        $service->editUserName($user->id, $this->name_change_input);
        $user_changed_name = \App\User::find($user->id);
        $this->assertEquals($user_changed_name->first_name, $this->name_change_input['first_name']);
        $this->assertEquals($user_changed_name->first_name_kana, $this->name_change_input['first_name_kana']);
        $this->assertEquals($user_changed_name->last_name, $this->name_change_input['last_name']);
        $this->assertEquals($user_changed_name->last_name_kana, $this->name_change_input['last_name_kana']);
    }

    /**
     * @tests
     */
//    public function 異常系アイコン変更時ユーザーが存在しない() {
//        \App\User::truncate();
//        $user              = factory(\App\User::class)->create();
//        $service           = new \App\Services\UserService();
//         $image_file_name = "cat_image_for_success_change_user_icon_test.jpg";
//        $path = storage_path() . '/tests/'.$image_file_name;
//        $request=$this->actingAs($user)
//                ->visit(route('app::user::show', ['id' => $user->id]))
//                ->attach($path, 'user_icon')
//                ->press('btn_icon');
//        try {
//            $service->editUserIcon($user->id + 1, $request);
//            $this->fail('例外発生なし');
//        } catch (Exception $ex) {
//            $this->assertEquals("No query results for model [App\User].", $ex->getMessage());
//        }
//    }
////リクエストの偽装方法がわからんので後回し
//    /**
//     * @tests
//     */
//    public function 正常系アイコンが変更できる() {
//        \App\User::truncate();
//        $user              = factory(\App\User::class)->create();
//        $service           = new \App\Services\UserService();
//        $image_file_name = "cat_image_for_success_change_user_icon_test.jpg";
//         $mime_type = "image/ipg";
//        $fake_data = [
//             'user_icon'=>$this->createUploadFile(storage_path().'/tests/', $image_file_name, $mime_type),
//             '_token' => csrf_token(),
//             ];
//        $request= $this->post('/app/user/icon/'.$user->id ,$fake_data );
//        $service->editUserIcon($user->id, $request);
//    }

    /**
     * @tests
     */
    public function 異常系パスワード変更時ユーザーが存在しない() {
        \App\User::truncate();
        $user    = factory(\App\User::class)->create();
        $service = new \App\Services\UserService();
        try {
            $service->editUserPassword($user->id + 1, $this->password_change_input);
            $this->fail('例外発生なし');
        } catch (Exception $ex) {
            $this->assertEquals("No query results for model [App\User].", $ex->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系パスワードの変更ができる() {
        $user                  = factory(\App\User::class)->create();
        $service               = new \App\Services\UserService();
        $service->editUserPassword($user->id, $this->password_change_input);
        $user_changed_password = \App\User::find($user->id);
        $this->assertNotEquals($user_changed_password->password, $user->password);
        $this->assertEquals($user_changed_password->unencrypt_password, $this->password_change_input['new_password']);
    }

    /**
     * @tests
     */
    public function 正常系パスワードがマッチする() {
        $user    = factory(\App\User::class)->create(['unencrypt_password' => $this->password_match_input["password"]]);
        $service = new \App\Services\UserService();
        $this->assertTrue($service->isPasswordMatch($user->id, $this->password_match_input));
    }

    /**
     * @tests
     */
    public function 異常系パスワードがマッチしない() {
        $user    = factory(\App\User::class)->create(['unencrypt_password' => $this->password_match_input["password"] . "falsse"]);
        $service = new \App\Services\UserService();
        $this->assertFalse($service->isPasswordMatch($user->id, $this->password_match_input));
    }

    /**
     * @tests
     */
    public function 異常系パスワードをマッチさせるユーザーが存在しない() {
        \App\User::truncate();
        $user    = factory(\App\User::class)->create(['unencrypt_password' => $this->password_match_input["password"]]);
        $service = new \App\Services\UserService();
        try {
            $service->isPasswordMatch($user->id + 1, $this->password_match_input);
            $this->fail('例外発生なし');
        } catch (Exception $ex) {
            $this->assertEquals("No query results for model [App\User].", $ex->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系所属が変更できる() {
        
        \App\SinrenDivision::insert([['division_id' => 1, 'division_name' => 'System'], ['division_id' => 2, 'division_name' => 'Sales']]);
        $user    = factory(\App\User::class)->create(['unencrypt_password' => $this->password_match_input["password"]]);
        $service = new \App\Services\UserService();
        $service->editUserDivision($user->id, $this->division_change_input);
        $user_changed_division = \App\SinrenUser::where('user_id',$user->id)->first();
        $this->assertEquals($user_changed_division->division_id, $this->division_change_input['division_id']);
    }
//    /**
//     * @tests
//     */
//    public function 異常系所属変更時SQL例外発生() {
//       
//    }

}
