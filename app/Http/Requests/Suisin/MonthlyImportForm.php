<?php

namespace App\Http\Requests\Suisin;

use App\Http\Requests\Request;

class MonthlyImportForm extends Request
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
        $in = \Input::get();
        $rules = [];
        foreach($in['process'] as $key => $val){
            $rules["process.{$key}"] = 'required|exists:mysql_suisin.zenon_monthly_process_status,id';
//            $rules["id.{$key}"] = 'required|exists:mysql_suisin.zenon_monthly_process_status,id';
        }
        return $rules;
    }
}
