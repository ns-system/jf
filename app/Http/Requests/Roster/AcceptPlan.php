<?php

namespace App\Http\Requests\Roster;

use App\Http\Requests\Request;

class AcceptPlan extends Request
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
        $input = \Input::get();
//        var_dump($input);
        $type   = null;
        if (array_key_exists('plan', $input))
        {
            $type = 'plan';
        }
        elseif (array_key_exists('actual', $input))
        {
            $type = 'actual';
        }
        $rules = [];
        foreach ($input[$type] as $key => $v) {
            $rules["{$type}.{$key}"]  = "required|boolean";
            $rules["form_id.{$key}"] = 'required|exists:mysql_roster.rosters,id';
        }
//        var_dump($rules);
//        exit();
        return $rules;
    }

}
