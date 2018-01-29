<?php

namespace App\Http\Requests\SuperUser;

use App\Http\Requests\Request;

class ColumnDelete extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    public function rules() {
        $rules  = [
            'zenon_format_id' => 'required',
            'agree'           => 'accepted',
        ];
        $inputs = \Input::only('zenon_format_id');
        if (!isset($inputs['zenon_format_id']))
        {
            return $rules;
        }
        foreach ($inputs['zenon_format_id'] as $i => $input) {
            $rules["zenon_format_id.{$i}"] = 'exists:mysql_suisin.zenon_data_csv_files,zenon_format_id';
        }
        return $rules;
    }

    public function attributes() {
        return [
            'zenon_format_id' => '全オンテーブル名',
            'agree'           => '認証',
        ];
    }

}
