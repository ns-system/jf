<?php

namespace App\Http\Middleware;

use Closure;

class RosterProxyMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $id   = \Auth::user()->id;
        $user = \App\RosterUser::where('user_id', '=', $id)->first();

        if (!$user->is_chief)
        {
            if (!$user->is_proxy || !$user->is_proxy_active)
            {
                return redirect(route('permission_error'));
            }
        }
        return $next($request);
    }

}
