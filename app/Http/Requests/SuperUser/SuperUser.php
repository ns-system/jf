<?php

namespace App\Http\Requests\SuperUser;

use App\Http\Requests\Request;

class SuperUser extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    public function rules() {
        $input = \Input::all();
        $rules = [];
        if (isset($input['is_super_user']))
        {
            $rules['is_super_user'] = 'required|boolean';
        }
        if (isset($input['suisin_is_administrator']))
        {
            $rules['suisin_is_administrator'] = 'required|boolean';
        }
        if (isset($input['roster_is_administrator']))
        {
            $rules['roster_is_administrator'] = 'required|boolean';
        }
        return $rules;
    }

    public function attributes() {
        return [
            'is_super_user'           => '管理者',
            'suisin_is_administrator' => '推進管理者',
            'roster_is_administrator' => '勤怠管理管理者',
        ];
    }

}
