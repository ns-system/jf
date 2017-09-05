<?php

namespace App\Http\Requests\Roster;

use App\Http\Requests\Request;

class PlanRequest extends Request
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
        $input = \Input::all();
//        var_dump($input);
//        exit();
        $rules = [
            'entered_on' => 'required|date',
        ];

        if (empty($input['plan_rest_reason_id']))
        {
            $rules['plan_start_hour'] = 'required|integer|min:0|max:23';
            $rules['plan_start_time'] = 'required|integer|min:0|max:55';
            $rules['plan_end_hour']   = 'required|integer|min:0|max:23';
            $rules['plan_end_time']   = 'required|integer|min:0|max:55';
        }
        var_dump($rules);
//        exit();
        return $rules;
    }

    public function attributes() {
        return [
            'entered_on' => '入力日',
            'plan_start_hour' => '開始時間',
            'plan_start_time' => '開始時間',
            'plan_end_hour'   => '終了時間',
            'plan_end_time'   => '終了時間',
        ];
    }

}
