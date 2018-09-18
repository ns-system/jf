<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    /**
     * アプリケーションのグローバルHTTPミドルウェアスタック
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ];

    /**
     * アプリケーションのルートミドルウェアスタック
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'         => \App\Http\Middleware\Authenticate::class,
        'auth.basic'   => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest'        => \App\Http\Middleware\RedirectIfAuthenticated::class,
        /**
         * Role : administrator
         */
        'super_user'   => \App\Http\Middleware\SuperUserMiddleware::class,
        'suisin_admin' => \App\Http\Middleware\SuisinAdminMiddleware::class,
        'roster_admin' => \App\Http\Middleware\RosterAdminMiddleware::class,
        /**
         * Role : user
         */
        'roster_user'  => \App\Http\Middleware\RosterUserMiddleware::class,
        'roster_proxy' => \App\Http\Middleware\RosterProxyMiddleware::class,
        'roster_chief' => \App\Http\Middleware\RosterChiefMiddleware::class,
        'suisin'       => \App\Http\Middleware\SuisinMiddleware::class,
    ];

}
