<?php

namespace App\Services;

class Swim
{

    use \App\Services\Sample;

    public function swim($meter) {
        print 'You swimed ' . $this->setMeter($meter)->getKm() . 'km.';
    }

}
