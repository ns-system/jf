<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * アプリケーションで提供するArtisanコマンド
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\CreateDatabase::class,
        Commands\DropDatabase::class,
        Commands\CreateUser::class,
//        Commands\ImportCsv::class,
//        Commands\CopyCsvFile::class,
       
    ];

    /**
     * アプリケーションのコマンド実行スケジュール定義
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
    }
}
