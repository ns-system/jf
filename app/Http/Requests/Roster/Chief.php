<?php

namespace App\Http\Requests\Roster;

use App\Http\Requests\Request;

class Chief extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $in    = \Input::get();
        $rules = [
//            'id'     => 'required|exists:mysql_roster.roster_users,id',
            'proxy'  => 'required|boolean',
            'active' => 'required|boolean',
        ];
        if ($in['active'])
        {
            $rules['proxy'] = 'required|regex:/^[1]/';
        }
        return $rules;
    }

    public function attributes() {
        return [
            'id'     => 'ユーザー情報',
            'proxy'  => '責任者代理',
            'active' => '代理人機能',
        ];
    }

}
