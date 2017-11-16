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
// なぜかバグってたのでコメントアウト。dbいじるのはテスト前にした方がいいかもね。
//    public function setUp() {
//        parent::setUp();
//        echo 'init';
//
//        if (!static::$init)
//        {
//            static::$init = true;
//            try {
////                \Artisan::call('db:reset');
////                \Artisan::call('db:create');
////                \Artisan::call('migrate');
//            } catch (\Exception $exc) {
//                echo $exc->getTraceAsString();
//            }
//        }
//    }

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
    public function 異常系_名前変更時ユーザーが存在しない() {
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
    public function 正常系_名前の変更ができる() {
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
    public function 異常系_別ユーザーでアイコンを変更できない() {
        \App\User::truncate();
        \Session::start();

        $user            = factory(\App\User::class)->create();
        $image_file_name = "cat_image_for_success_change_user_icon_test.jpg";
        $mime_type       = "image/jpeg";

        $fake_icon  = $this->createUploadFile(storage_path() . '/tests/', $image_file_name, $mime_type);
        $fake_token = csrf_token();
        $this->actingAs($user);
        $this->call(/* method = */'POST', /* uri = */ '/app/user/icon/' . ($user->id + 1), [/* params = */ '_token' => $fake_token,], [/* Cookie = */], [/* files = */ 'user_icon' => $fake_icon]);
        $this->assertRedirectedTo('/permission_error');
    }

    /**
     * @tests
     */
    public function 異常系_ファイルをPOSTしていない() {
        \App\User::truncate();
        \Session::start();

        $user = factory(\App\User::class)->create();

        $fake_token = csrf_token();
        $this->actingAs($user);
        try {
            $this->call(/* method = */'POST', /* uri = */ '/app/user/icon/' . ($user->id + 1), [/* params = */ '_token' => $fake_token,], [], ['test' => null]);
            $this->fail('予期しないエラーです。');
        } catch (\Exception $e) {
            $this->assertEquals('An uploaded file must be an array or an instance of UploadedFile.', $e->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 異常系_パスワード変更時ユーザーが存在しない() {
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
    public function 正常系_パスワードの変更ができる() {
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
    public function 正常系_パスワードがマッチする() {
        $user    = factory(\App\User::class)->create(['unencrypt_password' => $this->password_match_input["password"]]);
        $service = new \App\Services\UserService();
        $this->assertTrue($service->isPasswordMatch($user->id, $this->password_match_input));
    }

    /**
     * @tests
     */
    public function 異常系_パスワードがマッチしない() {
        $user    = factory(\App\User::class)->create(['unencrypt_password' => $this->password_match_input["password"] . "falsse"]);
        $service = new \App\Services\UserService();
        $this->assertFalse($service->isPasswordMatch($user->id, $this->password_match_input));
    }

    /**
     * @tests
     */
    public function 異常系_パスワードをマッチさせるユーザーが存在しない() {
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
    public function 正常系_所属が変更できる() {

        \App\SinrenDivision::insert([['division_id' => 1, 'division_name' => 'System'], ['division_id' => 2, 'division_name' => 'Sales']]);
        $user                  = factory(\App\User::class)->create(['unencrypt_password' => $this->password_match_input["password"]]);
        $service               = new \App\Services\UserService();
        $service->editUserDivision($user->id, $this->division_change_input);
        $user_changed_division = \App\SinrenUser::where('user_id', $user->id)->first();
        $this->assertEquals($user_changed_division->division_id, $this->division_change_input['division_id']);
    }

    /**
     * @tests
     */
    public function 異常系_ユーザーアイコン変更時にエラーが起きる() {
        \App\User::truncate();
        \Session::start();

        $user = factory(\App\User::class)->create();

        $service = new \App\Services\UserService();
        try {
            $service->editUserIcon(null, null);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals('データが送信されていないようです。', $e->getMessage());
        }
        try {
            $service->editUserIcon(0, 1);
            $this->fail('エラー：例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertEquals('ユーザーが登録されていないようです。', $e->getMessage());
        }
    }

}
