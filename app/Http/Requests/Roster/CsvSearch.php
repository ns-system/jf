<?php

namespace App\Http\Requests\Roster;

use App\Http\Requests\Request;

class CsvSearch extends Request
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
        $in    = \Input::all();
        $rules = [];
        if (isset($in['plan']))
        {
            $rules['plan'] = 'required|regex:/^[0-3]{1}$/';
        }
        if (isset($in['actual']))
        {
            $rules['actual'] = 'required|regex:/^[0-3]{1}$/';
        }

        if (!empty($in['division']))
        {
            $rules['division'] = 'required|exists:mysql_sinren.sinren_divisions,division_id';
        }
        if (!empty($in['date']))
        {
            $rules['date'] = 'required|date';
        }

//        var_dump($rules);
        return $rules;
    }

    public function attributes() {
        return [
            'plan'     => '予定',
            'actual'   => '実績',
            'name'     => 'ユーザー名',
            'division' => '部署',
            'date'     => '日付',
        ];
    }

}
