<?php

namespace App\Http\Requests\Roster;

use App\Http\Requests\Request;

class Actual extends Request
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
        $rules = ['actual_work_type_id' => 'required|exists:mysql_roster.work_types,work_type_id'];
//        var_dump($input['actual_rest_reason']);
//        exit();
        if (!empty($input['actual_rest_reason']))
        {
            $rules['actual_rest_reason'] = 'required|exists:mysql_roster.rest_reasons,rest_reason_id';
        }
        else
        {
            $rules['actual_start_hour'] = 'required|integer|min:0|max:23';
            $rules['actual_start_time'] = 'required|integer|min:0|max:55';
            $rules['actual_end_hour']   = 'required|integer|min:0|max:23';
            $rules['actual_end_time']   = 'required|integer|min:0|max:55';
        }
        return $rules;
    }

    public function attributes() {
        return [
            'actual_work_type_id' => '勤務形態',
            'actual_rest_reason'  => '休暇理由',
            'actual_start_hour'   => '開始時間',
            'actual_start_time'   => '開始時間',
            'actual_end_hour'     => '終了時間',
            'actual_end_time'     => '終了時間',
        ];
    }

}
