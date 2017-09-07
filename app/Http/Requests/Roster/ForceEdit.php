<?php

namespace App\Http\Requests\Roster;

use App\Http\Requests\Request;

class ForceEdit extends Request
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
     * Het the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $in    = \Input::get();
        var_dump($in);
        $rules = [
            'id'            => 'required|exists:mysql_roster.rosters,id',
            'plan_accept'   => 'required|integer|min:-1|max:2',
            'actual_accept' => 'required|integer|min:-1|max:2',
        ];

        if (!empty($in['plan_work_type_id']))
        {
            $rules['plan_work_type_id'] = 'required|exists:mysql_roster.work_types,work_type_id';
        }
        else
        {
            $rules['plan_work_type_id'] = 'required|regex:/^[0]/';
        }

        if (!empty($in['actual_work_type_id']))
        {
            $rules['actual_work_type_id'] = 'required|exists:mysql_roster.work_types,work_type_id';
        }
        else
        {
            $rules['actual_work_type_id'] = 'required|regex:/^[0]/';
        }

        if (!empty($in['plan_rest_reason_id']))
        {
            $rules['plan_rest_reason_id'] = 'required|exists:mysql_roster.rest_reasons,rest_reason_id';
        }
        else
        {
            $rules['plan_rest_reason_id'] = 'required|regex:/^[0]/';
        }

        if (!empty($in['actual_rest_reason_id']))
        {
            $rules['actual_rest_reason_id'] = 'required|exists:mysql_roster.rest_reasons,rest_reason_id';
        }
        else
        {
            $rules['actual_rest_reason_id'] = 'required|regex:/^[0]/';
        }

        if (!empty($in['plan_overtime_start_time']))
        {
            $rules['plan_overtime_start_time'] = 'required|date_format:H:i';
        }
        if (!empty($in['plan_overtime_end_time']))
        {
            $rules['plan_overtime_end_time'] = 'required|date_format:H:i';
        }
        if (!empty($in['actual_overtime_start_time']))
        {
            $rules['actual_overtime_start_time'] = 'required|date_format:H:i';
        }
        if (!empty($in['actual_overtime_end_time']))
        {
            $rules['actual_overtime_end_time'] = 'required|date_format:H:i';
        }

        return $rules;
    }

}
