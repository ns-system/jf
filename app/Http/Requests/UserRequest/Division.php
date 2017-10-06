<?php

namespace App\Http\Requests\UserRequest;

use App\Http\Requests\Request;

class Division extends Request
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
            'division_id' => 'required|exists:mysql_sinren.sinren_divisions,division_id',
        ];
    }

    public function attributes() {
        return [
            'division_id' => '部署',
        ];
    }

}
