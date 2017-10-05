<?php

namespace App\Http\Requests\Nikocale;

use App\Http\Requests\Request;

class Nikocale extends Request
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
            'emotion' => 'required|min:1|max:3',
        ];
    }

}
