<?php

namespace App\Services;

//use App\Http\Requests;

class UserService
{

    // FIXME: 本当はセッター通してユーザーチェック→例外スローをキャッチ的な感じにしたほうがスマート
    public function editUserName($id, $input) {
        try {
//            存在しないIDをfindするとエラーが起きた
//            無かったら例外投げるメゾットが存在したので書き換え
//            find -> findOrFail

            $user                  = \App\User::findOrFail($id);
            $user->first_name      = $input['first_name'];
            $user->first_name_kana = $input['first_name_kana'];
            $user->last_name       = $input['last_name'];
            $user->last_name_kana  = $input['last_name_kana'];
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function editUserIcon($id, $request) {
        if (empty($request))
        {
            throw new \Exception('データが送信されていないようです。');
        }
        try {
            $user = \App\User::findOrFail($id);
        } catch (\Exception $e) {
            throw new \Exception('ユーザーが登録されていないようです。');
        }
        if (!$request->hasFile('user_icon'))
        {
            throw new \Exception('アイコンファイルが存在しないようです。');
        }

        $icon            = $request->file('user_icon');
        $file_name       = $icon->getClientOriginalName();
        $file_name       = $id . '_' . $file_name;
        $request->file('user_icon')->move(public_path('user_icon'), $file_name);
        $user->user_icon = $file_name;
        $user->save();
    }

    public function editUserPassword($id, $input) {
        try {
            $user                     = \App\User::findOrFail($id);
            $user->password           = bcrypt($input['new_password']);
            $user->unencrypt_password = $input['new_password'];
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function isPasswordMatch($id, $input) {
        $user = \App\User::findOrFail($id);
        if ($user->unencrypt_password !== $input['password'])
        {
            return false;
        }
        return true;
    }

    public function editUserDivision($id, $input) {
//        try {
        $user              = \App\SinrenUser::firstOrNew(['user_id' => $id]);
        $user->division_id = (int) $input['division_id'];
        $user->save();
//        } catch (\Exception $e) {
//            throw $e;
//        }
    }

}
