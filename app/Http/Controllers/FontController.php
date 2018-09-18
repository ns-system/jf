<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class FontController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_id) {
        $sizes  = [12, 14, 16, 18, 20, 22, 24];
        $bolds  = [100, 200, 300, 400, 500, 600, 700];
        $colors = ['#000', '#111', '#222', '#333', '#444', '#555', '#666'];
        $fonts  = [
            ['name_jp' => 'メイリオ', 'name' => 'Meiryo',],
            ['name_jp' => '游ゴシック', 'name' => 'Yu Gothic',],
            ['name_jp' => '游明朝', 'name' => 'Yu Mincho',],
            ['name_jp' => 'M+1p', 'name' => 'Mplus 1p',],
            ['name_jp' => 'Rounded M+1c', 'name' => 'Rounded Mplus 1c',],
            ['name_jp' => 'さわらび明朝', 'name' => 'Sawarabi Mincho',],
            ['name_jp' => 'さわらびゴシック', 'name' => 'Sawarabi Gothic',],
            ['name_jp' => 'Noto Sans Japanese', 'name' => 'Noto Sans Japanese',],
            ['name_jp' => 'MSゴシック', 'name' => 'MS Gothic',],
            ['name_jp' => 'MS明朝', 'name' => 'MS Mincho',],
            ['name_jp' => 'HG丸ゴシックM-PRO', 'name' => 'HG丸ｺﾞｼｯｸM-PRO',],
//            ['name_jp' => 'メイリオ', 'name' => '"Meiryo"',],
//            ['name_jp' => '游ゴシック', 'name' => '"Yu Gothic"',],
//            ['name_jp' => '游明朝', 'name' => '"Yu Mincho"',],
//            ['name_jp' => 'M+1p', 'name' => '"Mplus 1p"',],
//            ['name_jp' => 'Rounded M+1c', 'name' => '"Rounded Mplus 1c"',],
//            ['name_jp' => 'さわらび明朝', 'name' => '"Sawarabi Mincho"',],
//            ['name_jp' => 'さわらびゴシック', 'name' => '"Sawarabi Gothic"',],
//            ['name_jp' => 'Noto Sans Japanese', 'name' => '"Noto Sans Japanese"',],
//            ['name_jp' => 'MSゴシック', 'name' => '"MS Gothic"',],
//            ['name_jp' => 'MS明朝', 'name' => '"MS Micho"',],
//            ['name_jp' => 'HG丸ゴシックM-PRO', 'name' => '"HG丸ｺﾞｼｯｸM-PRO"',],
        ];

        return view('app.font.index', ['sizes' => $sizes, 'fonts' => $fonts, 'bolds' => $bolds, 'colors' => $colors, 'user_id' => $user_id]);
    }

    public function update(\App\Http\Requests\Fonts $request, $user_id) {
        $input             = $request->input();
        $user              = \App\User::find($user_id);
        $user->font        = (isset($input['font'])) ? $input['font'] : $user->font;
        $user->font_size   = (isset($input['font_size'])) ? $input['font_size'] : $user->font_size;
        $user->font_weight = (isset($input['font_weight'])) ? $input['font_weight'] : $user->font_weight;
        $user->font_color  = (isset($input['font_color'])) ? $input['font_color'] : $user->font_color;
        $user->save();
        \Session::flash('success_message', "フォントが正常に変更されました。");
        return back();
    }

}
