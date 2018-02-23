<?php

namespace App\Http\Requests\Suisin;

use App\Http\Requests\Request;

class DailyDate extends Request
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
            'date' => 'required',
        ];
    }

    public function attributes() {
        return ['date' => '処理日付'];
    }

}
