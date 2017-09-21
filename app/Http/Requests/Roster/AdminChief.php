<?php

namespace App\Http\Requests\Roster;

use App\Http\Requests\Request;

class AdminChief extends Request
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
        $input       = \Input::get();
//        var_dump($input);
        $rules       = [];
        $rules['id'] = 'required|exists:mysql_laravel.users,id';
        if ($input['is_chief'] == true)
        {
            $rules['is_proxy']        = 'required|regex:/^[0]/';
            $rules['is_proxy_active'] = 'required|regex:/^[0]/';
            $rules                    = $this->makeDivisionRules($input['control_division'], $rules);
        }
        if ($input['is_proxy'] == true)
        {
            $rules['is_chief'] = 'required|regex:/^[0]/';
            $rules             = $this->makeDivisionRules($input['control_division'], $rules);
        }
//        var_dump($rules);
        return $rules;
    }

    public function attributes() {
        return [
            'is_chief'         => '責任者',
            'is_proxy'         => '責任者代理',
            'is_proxy_active'  => '責任者代理有効区分',
            'control_division' => '管轄店舗',
        ];
    }

    private function makeDivisionRules($divs, $rules) {
        foreach ($divs as $key => $div) {
            if ($div != 0)
            {
                $rules["control_division.{$key}"] = 'required|exists:mysql_sinren.sinren_divisions,division_id';
            }
        }
        return $rules;
    }

}
