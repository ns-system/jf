<?php

namespace App\Http\Middleware;

use Closure;

class RosterUserMiddleware
{

    /**
     * 送られてきたリクエストの処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $id = \Auth::user()->id;
        if (!\App\RosterUser::user($id)->exists())
        {
            \Session::flash('warn_message', '勤怠管理システムのユーザーが登録されていないようです。');
            return redirect(route('app::roster::user::show'));
        }
//        if (!\App\SinrenUser::where('user_id', '=', $id)->exists())
//        {
//            \Session::flash('warn_message', '勤怠管理システムのユーザーが登録されていないようです。');
//            return redirect('/permission_error');
//        }

        return $next($request);
    }

}
