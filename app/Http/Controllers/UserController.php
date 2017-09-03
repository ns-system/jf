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
                return redirect(route('permission_error'));
            }
            $user   = User::find($id);
            $divs   = \App\Division::get();
            $prefs  = \App\Prefecture::get();
            $stores = \App\Store::get();
            $ctls   = \App\ControlStore::orderAsc()->get();
            $works  = \App\WorkType::get();
            return view('/app/user', ['user' => $user, 'divs' => $divs, 'prefs' => $prefs, 'stores' => $stores, 'ctls' => $ctls, 'works' => $works]);
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function name(UserRequest\UserName $request, $id) {
        try {
            if (!$this->isSelf($id))
            {
                return redirect(route('permission_error'));
            }
            $input       = $request->only(['name']);
            $input['id'] = $id;
            $this->service->editUserName($input);
            \Session::flash('flash_message', "ユーザー名を変更しました。");
            return back();
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function userIcon(Request $icon_object, UserRequest\UserIcon $request, $id) {
        try {
            if (!$this->isSelf($id))
            {
                return redirect(route('permission_error'));
            }
            $input       = $request->all();
            $input['id'] = $id;
            $this->service->editUserIcon($input, $icon_object);
            \Session::flash('flash_message', "アイコンを変更しました。");
            return back();
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function division($id, UserRequest\Division $request) {
        try {
            var_dump("ok");
            if (!$this->isSelf($id))
            {
                return redirect(route('permission_error'));
            }
            $this->service->editUserDivision($request);
            \Session::flash('flash_message', "部署を変更しました。");
            return back();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function password($id, UserRequest\Password $request) {

        try {
            if (!$this->isSelf($id))
            {
                return redirect(route('permission_error'));
            }
            $service     = $this->service;
            $input       = $request->all();
            $input['id'] = \Auth::user()->id;
            if (!$service->isPasswordMatch($input))
            {
                \Session::flash('flash_message', "パスワードが一致しませんでした。");
                return back();
            }
            $service->editUserPassword($input);
            \Session::flash('flash_message', "パスワードを変更しました。");
            return back();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    private function isSelf($id){
        if($id != \Auth::user()->id){
            return false;
        }
        return true;
    }
}
