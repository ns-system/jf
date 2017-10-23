<?php

namespace App\Services;

//use App\Http\Requests;

class UserService
{

    // FIXME: 本当はセッター通してユーザーチェック→例外スローをキャッチ的な感じにしたほうがスマート
    public function editUserName($id, $input) {
        try {
//            $user = \App\User::find($id);
//            if (!$user->exists())
//            {
//                throw new \Exception('ユーザーが存在しません。');
//            }
//            存在しないIDをfindするとエラーが起きた
//            無かったら例外投げるメゾットが存在したので書き換え

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
        try {
            $icon            = $request->file('user_icon');
            $file_name       = $icon->getClientOriginalName();
            $file_name       = $id . '_' . $file_name;
            $request->file('user_icon')->move(public_path('user_icon'), $file_name);
            $user            = \App\User::findOrFail($id);
//            $user      = \App\User::find($id);
//            if (!$user->exists())
//            {
//                throw new \Exception('ユーザーが存在しません。');
//            }
            $user->user_icon = $file_name;
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function editUserPassword($id, $input) {
        try {
//            $user = \App\User::find($id);
//            if (!$user->exists())
//            {
//                throw new \Exception('ユーザーが存在しません。');
//            }
            $user                     = \App\User::findOrFail($id);
            $user->password           = bcrypt($input['new_password']);
            $user->unencrypt_password = $input['new_password'];
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function isPasswordMatch($id, $input) {
//        $user = \App\User::find($id);
//        if (!$user->exists())
//        {
//            return false;
////            throw new \Exception('ユーザーが存在しません。');
//        }
        $user = \App\User::findOrFail($id);
        if ($user->unencrypt_password !== $input['password'])
        {
            return false;
        }
        return true;
    }

    public function editUserDivision($id, $input) {
        try {
//            $id                = \Auth::user()->id;
//            var_dump($id);
            $user              = \App\SinrenUser::firstOrNew(['user_id' => $id]);
//            $user->id          = $id;
            $user->division_id = (int) $input['division_id'];
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
