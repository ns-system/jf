<?php

namespace App\Services;

//use App\Http\Requests;

class UserService
{

    public function editUserName($input) {
        try {
            $user = \App\User::find($input['id']);
            if (!$user->exists())
            {
                throw new \Exception('ユーザーが存在しません。');
            }
            $user->name = $input['name'];
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function editUserIcon($input, $request) {
        try {
            $file_name = $input['user_icon']->getClientOriginalName();
            $file_name = $input['id'] . '_' . $file_name;
            $request->file('user_icon')->move(public_path('user_icon'), $file_name);
            $user      = \App\User::find($input['id']);
            if (!$user->exists())
            {
                throw new \Exception('ユーザーが存在しません。');
            }
            $user->user_icon = $file_name;
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function editUserPassword($input) {
        try {
            $user = \App\User::find($input['id']);
            if (!$user->exists())
            {
                throw new \Exception('ユーザーが存在しません。');
            }
            $user->password           = bcrypt($input['new_password']);
            $user->unencrypt_password = $input['new_password'];
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function isPasswordMatch($input) {
        $user = \App\User::find($input['id']);
        if (!$user->exists())
        {
            throw new \Exception('ユーザーが存在しません。');
        }
        if ($user->unencrypt_password !== $input['password'])
        {
            return false;
        }
        return true;
    }

    public function editUserDivision($input) {
        try {
            $id                = \Auth::user()->id;
            var_dump($id);
            $user              = \App\SinrenUser::firstOrNew(['user_id' => $id]);
            $user->id          = $id;
            $user->division_id = (int) $input['division_id'];
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
