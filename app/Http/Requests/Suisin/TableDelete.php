<?php

namespace App\Http\Requests\Suisin;

use App\Http\Requests\Request;

class TableDelete extends Request
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
        $rules = [
            'tables' => 'required',
            'agree'  => 'accepted',
        ];
        $input = \Input::get();
        if (!isset($input['tables']))
        {
            return $rules;
        }
        foreach ($input['tables'] as $i => $table) {
            $rules["tables.{$i}"] = "required|exists:mysql_suisin.zenon_data_monthly_process_status,id";
        }
        return $rules;
    }

    public function attributes() {
        return [
            'tables' => '対象テーブル',
            'agree'  => '認証',
        ];
    }

}
