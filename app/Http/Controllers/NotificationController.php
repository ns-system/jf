<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Notification;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{

    const CATEGORIES = [
        1 => '告知',
        2 => '緊急',
        3 => '重要',
        4 => '依頼',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $rows = \App\Notification::orderBy('created_at', 'desc')->with('user')->paginate(25);

        return view('notification.index', ['rows' => $rows, 'categories' => self::CATEGORIES]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create() {
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Notification $request) {
        $n     = new \App\Notification();
        $input = $request->input();

        $n->message  = $input['message'];
        $n->category = (!empty(self::CATEGORIES[$input['category']])) ? self::CATEGORIES[$input['category']] : '';
        $n->user_id  = \Auth::user()->id;
        $n->deadline = $input['deadline'];
        $n->save();

        \Session::flash('success_message', 'お知らせの作成が完了しました。');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id) {
//        //
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id) {
//        //
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Notification $request, $id) {
        $n     = \App\Notification::find($id);
        $input = $request->input();

        $n->message  = $input['message'];
        $n->category = (!empty(self::CATEGORIES[$input['category']])) ? self::CATEGORIES[$input['category']] : '';
        $n->user_id  = \Auth::user()->id;
        $n->deadline = $input['deadline'];
        $n->save();

        \Session::flash('success_message', 'お知らせの更新が完了しました。');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
