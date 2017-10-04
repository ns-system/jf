<?php

namespace App\Http\Middleware;

use Closure;

class SuisinMiddleware
{

    /**
     * 送られてきたリクエストの処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $user = \Auth::user();
        if ($user->is_super_user != true || ($user->SuisinUser && $user->SuisinUser->is_administrator != true))
        {
            return redirect('/permission_error');
        }
        return $next($request);
    }

}
