<?php

namespace App\Http\Middleware;

use Closure;

class RosterAdminMiddleware
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
        if (!$user->is_super_user)
        {
            $roster = \App\RosterUser::user($user->id);
            if (!$roster->exists() || !$roster->first()->is_administrator)
            {
                return redirect(route('permission_error'));
            }
        }
        return $next($request);
    }

}
