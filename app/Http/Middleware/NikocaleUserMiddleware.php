<?php

namespace App\Http\Middleware;

use Closure;

class NikocaleUserMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $user        = \Auth::user();
        $sinren_user = \App\SinrenUser::user($user->id)->first();
        if (empty($sinren_user->id))
        {
            \Session::flash('warn_message', 'ユーザー情報が登録されていないようです。所属部署を登録してください。');
            return redirect(route('app::user::show', ['id' => $user->id]));
        }
        return $next($request);
    }

}
