<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class CallJava extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

//    use Monolog\Logger;
//    use Monolog\Handler\StreamHandler;

    protected $log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
//        $this->log = new Logger($this->signature);
//        $this->log->pushHandler(new StreamHandler(storage_path() . '/logs/console/command_signature_here.log', config('app.log_level')));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $jar    = 'D:/products/cvs/app/Console/Commands/connect/phpConnectTest.jar';
        $cmd    = escapeshellcmd('java -jar', $jar);
        $result = shell_exec($cmd);
        var_dump($result);
//        if (!$result)
//        {
//            \Log::info('ok!!');
//        }
//        $this->log->addInfo('tests');
    }

}
