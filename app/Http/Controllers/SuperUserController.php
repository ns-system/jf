<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
//use App\Http\Requests;
use App\Http\Controllers\Controller;

class SuperUserController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected $users;

    public function __construct() {
        $this->users = new \App\Services\SuperUserService();
    }

    public function show() {
        try {
            $super     = $this->users;
            $parameter = $super->parameter(\Input::all());
            $divs      = \App\Division::get();
            $users     = $super->registerUsers();
            return view('admin.super_user.index', ['users' => $users, 'divs' => $divs])->with($parameter);
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function search() {
        try {
            $divs      = \App\Division::get();
            $super     = $this->users;
            $users     = $super->searchUsers(\Input::all());
            $parameter = $super->parameter(\Input::all());
            return view('admin.super_user.index', ['users' => $users, 'divs' => $divs])->with($parameter);
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }
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
    public function edit($id) {
        try {
            $super = $this->users;
            $res   = $super->editUser(\Input::all(), $id);
            if ($res !== true)
            {
                return back()->withErrors($res);
            }
            \Session::flash('flash_message', \App\User::find($id)->name . "さんの情報を変更しました。");
            return redirect(route('admin::super::user::show'));
//            return redirect('/admin/app/admin_user');
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

}
