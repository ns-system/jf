<?php

namespace App\Http\Requests\Suisin;

use App\Http\Requests\Request;

class WeeklyFile extends Request
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
//        $rules = [];
//        $input = \Input::get();
//        foreach ($input['files'] as $i => $f) {
//            $rules["files.{$i}"] = 'required';
//        }
        $rules = [
            'files' => 'required',
        ];
        return $rules;
    }

    public function attributes() {
        return ['files' => '処理対象ファイル'];
    }

}
