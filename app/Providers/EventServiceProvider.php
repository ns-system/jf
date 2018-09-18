<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * アプリケーションのイベントリスナーのマップ
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
//        'event.name'           => [
//            'EventListener',
//        ],
//        'illuminate.query'     => [// 追加
//            'App\Services\SqlTracker',
//        ],
    ];

    /**
     * アプリケーションのその他のイベントの登録
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events) {
        parent::boot($events);

        //
    }

}
