<?php

namespace App\Jobs;

use Mail;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class SampleMailJob extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

    protected $addr;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($addr) {
        $this->addr = $addr;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
//        var_dump($this->addr);
//        echo date('Y-m-d H:i:s') . ', hello, world.';
        $addr = $this->addr;
//        dd($addr);
        Mail::send('emails.sample', $addr, function($m) use ($addr) {
            $m->to($addr['addr'])
                    ->subject('テストメール')
            ;
        });
    }

}
