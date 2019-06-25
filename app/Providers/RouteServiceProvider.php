<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * この名前空間はルートファイルのコントローラルートへ適用されます。
     *
     * さらに、URLジェネレーターのルート名前空間としても設定されます。
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * ルートモデル結合、パターンフィルターなどを定義
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot() {
        parent::boot();
    }

    /**
     * アプリケーションのルートを定義
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router) {
        $router->group(['namespace' => $this->namespace], function ($router) {
            require base_path('routes/routes.php');
            require base_path('routes/user_routes.php');
            require base_path('routes/roster_routes.php');
        });
    }

}
