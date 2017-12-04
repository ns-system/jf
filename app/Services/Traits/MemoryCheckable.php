<?php

namespace App\Services\Traits;

trait MemoryCheckable
{

    private function debugMemory($title) {
        list($max) = sscanf(ini_get('memory_limit'), '%dM');
        $peak = ((int) memory_get_peak_usage() / 1024 / 1024);
        echo '  ' . $title . ' : ' . PHP_EOL .
        '      Allow : ' . $max . ' MB' . PHP_EOL .
        '      Now   : ' . round((int) memory_get_usage() / 1024 / 1024, 2) . ' MB' . PHP_EOL .
        '      Full  : ' . round((int) memory_get_usage(true) / 1024 / 1024, 2) . ' MB' . PHP_EOL .
        '      Peak  : ' . round($peak, 2) . ' MB' . PHP_EOL .
        '      Per   : ' . round(($peak / $max) * 100, 2) . ' %' . PHP_EOL
        ;
    }

}
