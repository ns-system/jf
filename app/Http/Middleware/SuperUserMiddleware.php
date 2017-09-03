<?php

namespace App\Http\Middleware;

use Closure;

class SuperUserMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (\Auth::user()->is_super_user != true)
        {
            return redirect('/permission_error');
        }
        return $next($request);
    }

}
