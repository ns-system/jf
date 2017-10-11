<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

//    protected $permission_error_url = '/permission_error';
    protected $service;

    public function __construct() {
        $this->service = new \App\Services\UserService();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        try {
            if (!$this->isSelf($id))
            {
                return redirect()->route('permission_error');
//                return redirect(route('permission_error'));
            }
            $user   = User::find($id);
            $divs   = \App\Division::get();
            $prefs  = \App\Models\Common\Prefecture::get();
            $stores = \App\Models\Common\Store::get();
            $ctls   = \App\ControlStore::orderAsc()->get();
            $works  = \App\WorkType::get();
            return view('/app/user', ['user' => $user, 'divs' => $divs, 'prefs' => $prefs, 'stores' => $stores, 'ctls' => $ctls, 'works' => $works]);
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function name(UserRequest\UserName $request, $id) {

        if (!$this->isSelf($id))
        {
            return redirect()->route('permission_error');
//            return redirect(route('permission_error'));
        }
//        $input = $request->except(['_token']);
        $this->service->editUserName($id, $request);
        \Session::flash('success_message', "ユーザー名を変更しました。");
        return back();
    }

    public function userIcon(/* Request $icon_object, */UserRequest\UserIcon $request, $id) {
//        dd(\Input::all());
//        dd($request->file('user_icon'));
        try {
            if (!$this->isSelf($id))
            {
                return redirect()->route('permission_error');
//                return redirect(route('permission_error'));
            }
//            $input       = $request->all();
//            $input['id'] = $id;
            $this->service->editUserIcon($id, $request);
            \Session::flash('success_message', "アイコンを変更しました。");
            return back();
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function division($id, UserRequest\Division $request) {
//        try {
        if (!$this->isSelf($id))
        {
            return redirect()->route('permission_error');
        }
        $this->service->editUserDivision($id, $request);
        \Session::flash('success_message', "部署を変更しました。");
        return back();
//        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
//        }
    }

    public function password($id, UserRequest\Password $request) {


        if (!$this->isSelf($id))
        {
            return redirect()->route('permission_error');
//            return redirect(route('permission_error'));
        }
        $service = $this->service;
//        $input   = $request->except(['_token']);
//        dd($input);
//        $input['id'] = \Auth::user()->id;
        if (!$service->isPasswordMatch($id, $request))
        {
            \Session::flash('warn_message', "パスワードが一致しませんでした。");
            return back();
        }
        $service->editUserPassword($id, $request);
        \Session::flash('success_message', "パスワードを変更しました。");
        return back();
    }

    private function isSelf($id) {
        if ($id != \Auth::user()->id)
        {
            return false;
        }
        return true;
    }

}
