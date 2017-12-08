<?php

use App\Services\Traits\Testing\FileTestable;

class FuncUserControllerTest extends TestCase
{

    use FileTestable;

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
    public function 異常系部署が無いときに変更しようとするとエラーになる() {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->see($user->first_name)
                ->press('btn_division')
                ->seePageIs('/app/user/1')
                ->see('部署は必須です。')
                ->dontSee('成功：要修正')

        ;
    }

    /**
     * @tests
     */
    public function 正常系部署の変更ができる() {
        $user = factory(\App\User::class)->create();
        \App\SinrenDivision::insert([['division_id' => 1, 'division_name' => 'System'], ['division_id' => 2, 'division_name' => 'Sales']]);
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->select(2, 'division_id')
                ->press('btn_division')
                ->seePageIs('/app/user/' . $user->id)
                ->see('部署を変更しました。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系本人以外が部署を変更しようとするとエラーになる() {
        \Session::start();
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->POST('/app/user/division/999999', ['_token' => csrf_token(), 'division_id' => '2'])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 異常系名前を変更時姓を空欄にするとエラーになる() {
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('', 'last_name')
                ->see('btn_name')
                ->press('btn_name')
                ->seePageIs('/app/user/' . $user->id)
                ->see('姓は必須です。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系名前を変更時名を空欄にするとエラーになる() {
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('', 'first_name')
                ->see('btn_name')
                ->press('btn_name')
                ->seePageIs('/app/user/' . $user->id)
                ->see('名は必須です。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系名前を変更時姓仮名を空欄にするとエラーになる() {
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('', 'last_name_kana')
                ->see('btn_name')
                ->press('btn_name')
                ->seePageIs('/app/user/' . $user->id)
                ->see('姓（ひらがな）は必須です。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系名前を変更時名仮名を空欄にするとエラーになる() {
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('', 'first_name_kana')
                ->see('btn_name')
                ->press('btn_name')
                ->seePageIs('/app/user/' . $user->id)
                ->see('名（ひらがな）は必須です。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 正常系名前の変更ができる() {
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->dontsee('名前 テスト')
                ->type('テスト', 'first_name')
                ->type('名前', 'last_name')
                ->type('へんこう', 'first_name_kana')
                ->type('せいこう', 'last_name_kana')
                ->see('btn_name')
                ->press('btn_name')
                ->seePageIs('/app/user/' . $user->id)
                ->see('ユーザー名を変更しました。')
                ->see('名前 テスト')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系本人以外が名前を変更しようとするとエラーになる() {
        \Session::start();
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->POST('/app/user/name/999999', ['_token' => csrf_token(), 'first_name' => 'テスト', 'last_name' => '名前', 'first_name_kana' => 'へんこう', 'last_name_kana' => 'せいこう'])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 異常系パスワードを変更時現在のパスワードを空欄にするとエラーになる() {
        $user = factory(\App\User::class)->create(['unencrypt_password' => 'password']);
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('', 'password')
                ->type('newpass', 'new_password')
                ->see('btn_password')
                ->press('btn_password')
                ->seePageIs('/app/user/' . $user->id)
                ->see('現在のパスワードは必須です。')
                ->dontsee('新しいパスワードは必須です。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系パスワードを変更時新しいパスワードを空欄にするとエラーになる() {
        $user = factory(\App\User::class)->create(['unencrypt_password' => 'password']);
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('', 'new_password')
                ->type('password', 'password')
                ->see('btn_password')
                ->press('btn_password')
                ->seePageIs('/app/user/' . $user->id)
                ->see('新しいパスワードは必須です。')
                ->dontsee('現在のパスワードは必須です。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系パスワードを変更時をパスワード再入力を空欄にするとエラーになる() {
        $user = factory(\App\User::class)->create(['unencrypt_password' => 'password']);
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('password', 'password')
                ->type('newpass', 'new_password')
                ->type('', 'new_password_confirmation')
                ->see('btn_password')
                ->press('btn_password')
                ->seePageIs('/app/user/' . $user->id)
                ->see('新しいパスワードは確認用項目と一致していません。')
                ->dontsee('現在のパスワードは必須です。')
                ->dontsee('新しいパスワードは必須です。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系パスワードを変更時現在のパスワード六文字未満にするとエラーになる() {
        $user = factory(\App\User::class)->create(['unencrypt_password' => 'password']);
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('12345', 'password')
                ->type('newpass', 'new_password')
                ->type('newpass', 'new_password_confirmation')
                ->see('btn_password')
                ->press('btn_password')
                ->seePageIs('/app/user/' . $user->id)
                ->see('現在のパスワードは6文字以上にしてください。')
                ->dontsee('新しいパスワードは必須です。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系パスワードを変更時新しいパスワードを六文字未満にするとエラーになる() {
        $user = factory(\App\User::class)->create(['unencrypt_password' => 'password']);
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('password', 'password')
                ->type('123', 'new_password')
                ->type('123', 'new_password_confirmation')
                ->see('btn_password')
                ->press('btn_password')
                ->seePageIs('/app/user/' . $user->id)
                ->see('新しいパスワードは6文字以上にしてください。')
                ->dontsee('現在のパスワードは必須です。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系パスワードを変更時新しいパスワードと再入力を異なる値にするとエラーになる() {
        $user = factory(\App\User::class)->create(['unencrypt_password' => 'password']);
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('password', 'password')
                ->type('123456', 'new_password')
                ->type('654321', 'new_password_confirmation')
                ->see('btn_password')
                ->press('btn_password')
                ->seePageIs('/app/user/' . $user->id)
                ->see('新しいパスワードは確認用項目と一致していません。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 正常系パスワードを変更できる() {

        $user = factory(\App\User::class)->create(['unencrypt_password' => 'password']);
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('password', 'password')
                ->type('123456', 'new_password')
                ->type('123456', 'new_password_confirmation')
                ->see('btn_password')
                ->press('btn_password')
                ->seePageIs('/app/user/' . $user->id)
                ->see('パスワードを変更しました。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系パスワードが異なるとエラーになる() {
        $user = factory(\App\User::class)->create(['unencrypt_password' => 'password']);
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->type('altpassword', 'password')
                ->type('123456', 'new_password')
                ->type('123456', 'new_password_confirmation')
                ->see('btn_password')
                ->press('btn_password')
                ->seePageIs('/app/user/' . $user->id)
                ->see('パスワードが一致しませんでした。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系本人以外がパスワードを変更しようとするとエラーになる() {
        \Session::start();
        $user = factory(\App\User::class)->create(['unencrypt_password' => 'password']);
        $this->actingAs($user)
                ->POST('/app/user/password/999999', ['_token' => csrf_token(), 'password' => 'password', 'new_password' => '123456', 'new_password_confirmation' => '123456'])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 正常系アイコンを変更できる() {
        $user            = factory(\App\User::class)->create();
        $image_file_name = "cat_image_for_success_change_user_icon_test.jpg";
        $path            = storage_path() . '/tests/' . $image_file_name;
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->attach($path, 'user_icon')
                ->press('btn_icon')
                ->seePageIs('/app/user/' . $user->id)
                ->see('アイコンを変更しました。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系アイコンの画像のサイズが500kb以上だとエラーになる() {
        $user            = factory(\App\User::class)->create();
        $image_file_name = "over_500kb_image.png";
        $path            = storage_path() . '/tests/' . $image_file_name;
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->attach($path, 'user_icon')
                ->press('btn_icon')
                ->seePageIs('/app/user/' . $user->id)
                ->see('アイコンは500 KB以下のファイルにしてください。')
                ->dontSee('成功：要修正')
        ;
    }

    /**
     * @tests
     */
    public function 異常系アイコンにイメージファイル以外を選択するとエラーになる() {
        $user            = factory(\App\User::class)->create();
        $image_file_name = "testfile.txt";
        $path            = storage_path() . '/tests/' . $image_file_name;
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id]))
                ->attach($path, 'user_icon')
                ->press('btn_icon')
                ->seePageIs('/app/user/' . $user->id)
                ->see('アイコンは画像にしてください。')
                ->dontSee('成功：要修正')
        ;
    }

    
    /**
     * @tests
     */
    public function 異常系本人以外がアイコンを変えようとするとエラーになる() {
        \Session::start();
        $user            = factory(\App\User::class)->create();
        $image_file_name = "cat_image_for_success_change_user_icon_test.jpg";
        $mime_type       = "image/ipg";
        $this->actingAs($user)
                ->POST(route("app::user::icon", ['id' => $user->id + 1]), ['_token' => csrf_token(), 'user_icon' => $this->createUploadFile(storage_path(). '/tests/', $image_file_name, $mime_type)])
                ->assertRedirectedTo('/permission_error')
        ;
    }

    /**
     * @tests
     */
    public function 異常系本人以外のユーザーのユーザー情報を見ようとするとエラー() {
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->visit(route('app::user::show', ['id' => $user->id + 1]))
                ->seePageIs('/permission_error')
        ;
    }

}
