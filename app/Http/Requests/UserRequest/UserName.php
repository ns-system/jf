<?php

namespace App\Http\Requests\UserRequest;

use App\Http\Requests\Request;

class UserName extends Request
{

//    protected $redirectRoute ='/app/user';
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
        return [
            'first_name'      => 'required|min:1',
            'last_name'       => 'required|min:1',
            'first_name_kana' => 'required|min:1',
            'last_name_kana'  => 'required|min:1',
        ];
    }

    public function attributes() {
        return [
            'first_name'      => '名',
            'first_name_kana' => '名（ひらがな）',
            'last_name'       => '姓',
            'last_name_kana'  => '姓（ひらがな）',
        ];
    }

}
