<?php

namespace App\Http\Requests\UserRequest;

use App\Http\Requests\Request;

class UserIcon extends Request
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
        return [
            'user_icon' => 'required|image|max:500',
        ];
    }

    public function attributes() {
        return [
            'user_icon' => 'アイコン',
        ];
    }

}
