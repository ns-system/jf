<?php

namespace App\Http\Requests\Roster;

use App\Http\Requests\Request;

class WorkPlan extends Request
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
        $in    = \Input::get();
        var_dump($in);
        $rules = [];
        foreach ($in['entered_on'] as $i => $day) {
            $rules["entered_on.{$i}"] = 'required|date';
            if ($in['work_type'][$day] != 0)
            {
                $rules["work_type.{$day}"] = 'required|exists:mysql_roster.work_types,work_type_id';
            }
            if ($in['rest'][$day] != 0)
            {
                $rules["rest.{$day}"] = 'required|exists:mysql_roster.rest_reasons,rest_reason_id';
            }
        }
        var_dump($rules);
        return $rules;
    }

    public function attributes() {
        return [
            'entered_on' => '指定日',
            'work_type'  => '勤務形態',
            'rest'       => '休暇理由'
        ];
    }

}
