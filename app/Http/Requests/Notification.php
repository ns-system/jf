<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class Notification extends Request
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
            'message'  => 'required|string|min:10',
            'deadline' => 'required|date',
            'category' => 'required|numeric',
        ];
        return $rules;
    }

    public function attributes() {
        return [
            'message'  => 'メッセージ',
            'deadline' => '公開期限',
            'category' => 'カテゴリー',
        ];
    }

    public function messages() {
        return [
            'font_size:between'   => ':attribute はリストの中から選択してください。',
            'font_weight:between' => ':attribute はリストの中から選択してください。',
            'font:in'             => ':attribute はリストの中から選択してください。',
            'font_color:regex'    => ':attribute が正しく選択されませんでした。もう一度やり直してください。',
        ];
    }

}
