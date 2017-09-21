<?php

namespace App\Http\Requests\Roster;

use App\Http\Requests\Request;

class CalendarAccept extends Request
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
//        var_dump("ok");
        $in    = \Input::get();
//        var_dump($in);
        $rules = [];
        foreach ($in['id'] as $id) {
            $rules["id.{$id}"] = 'required|exists:mysql_roster.rosters,id';
            if (isset($in['plan'][$id]))
            {
                $rules["plan.{$id}"] = 'required|boolean';
            }
            if (isset($in['actual'][$id]))
            {
                $rules["actual.{$id}"] = 'required|boolean';
            }
        }
//        var_dump($rules);
//        exit();
        return $rules;
    }

//    public function attributes() {
//        $attr = [];
//        $in = \Input::get();
//        foreach($in['id'] as $id){
//            $attr['id']
//        }
//        return [
//            'id'     => 'ID',
//            'plan.*'   => '予定',
//            'actual' => '実績',
//        ];
//    }

}
