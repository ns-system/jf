<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ConfigDelete extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
//        dd('hi');
        return [
            'confirm'=>'accepted',
        ];
    }
    public function messages(){
        return [
            'confirm.accepted'=>'削除に同意してください。',
        ];
    }
}
