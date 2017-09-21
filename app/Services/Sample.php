<?php

namespace App\Services;

trait Sample
{

    protected $meter;

    public function setMeter($meter) {
        $this->meter = $meter;
        return $this;
    }

    public function getKm() {
        return $this->meter / 1000;
    }

    public function warkKm($meter) {
        print 'You walked ' . $this->getKm($meter) . 'km.';
    }

}
