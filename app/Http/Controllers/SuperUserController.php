<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
//use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperUser\SuperUser;

class SuperUserController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected $user_service;

    public function __construct() {
        $this->user_service = new \App\Services\SuperUserService();
    }

    public function show() {
//        try {
            $service   = $this->user_service;
            $parameter = $service->parameter(\Input::all());
            $divs      = \App\Division::get();
            $users     = $service->getRegisterUsers();
//            dd($users);
            return view('admin.super_user.index', ['users' => $users, 'divs' => $divs])->with($parameter);
//        } catch (\Exception $exc) {
//            \Session::flash('warn_message', $exc->getMessage());
////            return back();
//            echo $exc->getTraceAsString();
//        }
    }

    public function search() {
        $divs      = \App\Division::get();
        $service   = $this->user_service;
        $users     = $service->searchUsers(\Input::all());
        $parameter = $service->parameter(\Input::all());
        return view('admin.super_user.index', ['users' => $users, 'divs' => $divs])->with($parameter);
    }

    public function user($id) {
        $user = \App\User::find($id);
        return view('admin.super_user.detail', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, SuperUser $res) {
        try {
            $service = $this->user_service;
            $service->editUser($res, $id);
            \Session::flash('success_message', \App\User::find($id)->last_name . "さんの情報を変更しました。");
            return redirect()->route('admin::super::user::show');
        } catch (\Exception $exc) {
            \Session::flash('warn_message', $exc->getMessage());
            return back();
//            echo $exc->getTraceAsString();
        }
    }

}
