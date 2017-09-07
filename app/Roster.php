<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Roster extends Model
{

//    use SoftDeletes;

    protected $connection = 'mysql_roster';
    protected $table      = 'rosters';
    protected $guarded    = ['id'];

    public function scopeUser($query, $user_id = null) {
        if ($user_id === null)
        {
            $user_id = \Auth::user()->id;
        }
        return $query->where('user_id', '=', $user_id);
    }

    public function scopeNot_myself($query, $user_id = null) {
        if ($user_id === null)
        {
            $user_id = \Auth::user()->id;
        }
        return $query->where('user_id', '<>', $user_id);
    }

    public function scopeMonth($query, $month = '') {
        if ($month == '')
        {
            $month = date('Ym');
        }
        return $query->where('month_id', '=', $month);
    }

    public function scopeEntered_on($query, $day) {
        return $query->where('entered_on', '=', $day);
    }

    public function scopePlan($query) {
        return $query->where('is_plan_entry', '=', true);
    }

    public function scopeActual($query) {
        return $query->where('is_actual_entry', '=', true);
    }

//    public function getPlanAccept($query) {
//        return $query->where('is_accept', '=', 1)->groupBy('is_plan_accept')->count();
//    }
//
    public function PlanType() {
        return $this->hasOne('\App\WorkType', 'work_type_id', 'plan_work_type_id');
    }

    public function ActualType() {
        return $this->hasOne('\App\WorkType', 'work_type_id', 'actual_work_type_id');
    }

    public function PlanRest() {
        return $this->hasOne('\App\Rest', 'rest_reason_id', 'plan_rest_reason_id');
    }

    public function ActualRest() {
        return $this->hasOne('\App\Rest', 'rest_reason_id', 'actual_rest_reason_id');
    }

}
