<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkType extends Model
{

    protected $connection = 'mysql_roster';
    protected $table      = 'work_types';
    protected $guarded    = ['id'];

    public function scopeWorkTypeList($query) {
        return $query->orderBy('work_type_id', 'asc')
                // CONCAT : 文字列結合。一つでもNULLがあると全てNULLとなる点に注意。
                ->select(\DB::raw("*, CONCAT(DATE_FORMAT(work_start_time, '%k:%i'), '～', DATE_FORMAT(work_end_time, '%k:%i')) as display_time"))
        ;
    }

}
