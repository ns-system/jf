<?php

namespace App\Http\Middleware;

use Closure;

class SuisinAdminMiddleware
{

    /**
     * 送られてきたリクエストの処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
//        var_dump("suisin_admin!!!!");
        $user = \Auth::user();
        if (!$user->is_super_user)
        {
            $suisin = \App\SuisinUser::user($user->id);
            if (!$suisin->exists() || !$suisin->first()->is_administrator)
            {
                return redirect(route('permission_error'));
            }
        }
        return $next($request);
    }

}
