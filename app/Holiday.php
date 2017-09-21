<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{

    protected $connection = 'mysql_sinren';
    protected $table      = 'holidays';
    protected $guarded    = ['id'];

    public function scopeCurrent($query, $month_id) {
        $first_day = date('Y-m-d', strtotime($month_id . '01'));
        $last_day  = date('Y-m-t', strtotime($month_id . '01'));

        var_dump($first_day. $last_day);
        return $query->where('holiday', '>=', $first_day)
                        ->where('holiday', '<=', $last_day)
        ;
    }

}
