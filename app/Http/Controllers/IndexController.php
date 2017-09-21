<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public function show() {
        if (!\Auth::check())
        {
            return view('auth.login');
        }
        $user = \Auth::user();

        if ($user->is_super_user)
        {
            return view('admin.home');
        }
        if ($user->SuisinUser && $user->SuisinUser->is_administrator)
        {
            return view('admin.home');
        }
        $roster = \App\Roster::user($user->id);
        if ($roster->exists() && $roster->first()->is_administrator)
        {
            return view('admin.home');
        }
        return view('app.home');
    }

    public function permissionError() {
        return view('admin.permission_error');
    }

}
