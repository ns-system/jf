<?php

namespace App\Http\Middleware;

use Closure;

class RosterChiefMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $user = \App\RosterUser::user()->first();
        if (empty($user) || !$user->is_chief)
        {
            return redirect(route('permission_error'));
        }
//        var_dump("chief");
        return $next($request);
    }

}
