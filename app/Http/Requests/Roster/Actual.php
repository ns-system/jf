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
        $input = \Input::all();
        $rules = ['actual_work_type_id' => 'required|exists:mysql_roster.work_types,work_type_id'];

        // 早退・遅刻を打刻するために無理やり実装する
        $rest = (empty($input['actual_rest_reason_id'])) ? null : \App\Rest::where('rest_reason_id', $input['actual_rest_reason_id'])->first();
        if (!empty($input['actual_rest_reason_id'])) {
            $rules['actual_rest_reason_id'] = 'required|exists:mysql_roster.rest_reasons,rest_reason_id';
        }

        if (empty($rest) || !empty($rest) && ($rest->rest_reason_name === '早退' || $rest->rest_reason_name === '遅刻')) {
            $rules['actual_start_hour'] = 'required|integer|min:0|max:23';
            $rules['actual_start_time'] = 'required|integer|min:0|max:55';
            $rules['actual_end_hour']   = 'required|integer|min:0|max:23';
            $rules['actual_end_time']   = 'required|integer|min:0|max:55';
        }

        $rules['actual_overtime_reason'] = (!empty($input['actual_overtime_reason'])) ? 'string|max:20' : '';
        return $rules;
    }

    public function attributes()
    {
        return [
            'actual_work_type_id'    => '勤務形態',
            'actual_rest_reason'     => '休暇理由',
            'actual_start_hour'      => '開始時間',
            'actual_start_time'      => '開始時間',
            'actual_end_hour'        => '終了時間',
            'actual_end_time'        => '終了時間',
            'actual_overtime_reason' => '実績残業理由',
        ];
    }

}
