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
        $rules = [
            'zenon_format_id' => 'required|exists:mysql_suisin.zenon_data_csv_files,zenon_format_id',
            'agree'           => 'accepted',
        ];
        return $rules;
    }

    public function attributes() {
        return [
            'zenon_format_id' => '全オンテーブル名',
            'agree'           => '認証',
        ];
    }

}
