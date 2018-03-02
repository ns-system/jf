<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class Fonts extends Request
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
        $input                = \Input::get();
        $rules['font_size']   = 'required|integer|between:12,24';
        $rules['font_weight'] = (isset($input['font_weight'])) ? 'integer|between:100,700' : '';
        $rules['font_color']  = (isset($input['font_color'])) ? ['regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'] : '';
        return $rules;
    }

    public function attributes() {
        return [
            'font'        => 'フォント',
            'font_size'   => 'フォントサイズ',
            'font_weight' => 'フォントの太さ',
            'font_color'  => 'フォントカラー',
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
