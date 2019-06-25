<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
      |--------------------------------------------------------------------------
      | 登録／ログインコントローラ
      |--------------------------------------------------------------------------
      |
      | このコントローラハンドラーは新ユーザーを登録し、同時に既存の
      | ユーザーを認証します。デフォルトでこのコントローラは振る舞いを
      | 追加するためにシンプルなトレイトを使用します。試してみませんか？
      |
     */

use AuthenticatesAndRegistersUsers,
    ThrottlesLogins;

    /**
     * 新しい認証コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * やって来た登録リクエストに対するバリデターを取得
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        $rules = [
            'first_name'      => 'required|max:255',
            'last_name'       => 'required|max:255',
            'first_name_kana' => 'required|max:255',
            'last_name_kana'  => 'required|max:255',
            'email'           => 'required|email|max:255|unique:users',
            'password'        => 'required|confirmed|min:6',
        ];
        $attr  = [
            'first_name'      => '名',
            'last_name'       => '姓',
            'first_name_kana' => '名（かな）',
            'last_name_kana'  => '姓（かな）',
            'email'           => 'メールアドレス',
            'password'        => 'パスワード',
        ];
        return Validator::make($data, $rules, [], $attr);
    }

    /**
     * 登録内容を確認後、新しいユーザーインスタンスを生成
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {
        return User::create([
//                    'name'               => $data['name'],
                    'first_name'         => $data['first_name'],
                    'last_name'          => $data['last_name'],
                    'first_name_kana'    => $data['first_name_kana'],
                    'last_name_kana'     => $data['last_name_kana'],
                    'email'              => $data['email'],
                    'password'           => bcrypt($data['password']),
                    'unencrypt_password' => $data['password'],
        ]);
    }

}
