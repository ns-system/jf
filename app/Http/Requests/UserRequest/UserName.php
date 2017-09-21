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
            'name' => 'required|min:2|unique:users,name',
        ];
    }

}
