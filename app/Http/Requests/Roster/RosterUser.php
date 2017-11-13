<?php

namespace App\Http\Requests\Roster;

use App\Http\Requests\Request;

class RosterUser extends Request
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
//        $input = \Request::only(['is_chief', 'work_type_id']);
        $input = \Request::all();
        $rules = [
            'division_id' => 'required|exists:mysql_sinren.sinren_divisions,division_id',
        ];

//        if (!isset($input['work_type_id']))
//        {
//            
//        }

        if (isset($input['is_chief']))
        {
            $rules['is_chief'] = 'required|boolean';
        }
        else
        {
            $rules['work_type_id'] = 'required|exists:mysql_roster.work_types,work_type_id';
        }
//        var_dump($input);
//        var_dump($rules);
//        dd($rules);
        return $rules;
    }

    public function attributes() {
        return [
            'division_id'  => '部署',
            'work_type_id' => '勤務形態',
            'is_chief'     => '責任者区分',
        ];
    }

}
