<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Roster\WorkPlan;
use App\Http\Controllers\Controller;

class RosterWorkPlanController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $current_month = \App\Roster::max('month_id');
//        var_dump($current_month);

        $months = [];
        $tmp    = $current_month . '01';
        for ($i = 2; $i > -4; $i--) {
            $months[] = [
                'id'      => date('Ym', strtotime($tmp . " {$i} month")),
                'display' => date('Y年n月', strtotime($tmp . " {$i} month")),
            ];
        }
//        var_dump($months);
        $current = date('Ym');
        return view('roster.app.work_plan.index', ['months' => $months, 'current' => $current]);
    }

    public function division($month) {
        $divs  = \App\ControlDivision::user()->get();
        $users = \App\SinrenUser::join('sinren_data_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->join('roster_data_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->where(function($query) use ($divs) {
                    foreach ($divs as $d) {
                        $query->orWhere(['sinren_users.division_id' => $d->division_id]);
                    }
                })
                ->where('roster_users.user_id', '<>', \Auth::user()->id)
                ->orderBy('sinren_users.division_id', 'asc')
                ->get()
        ;
        foreach ($users as $user) {
            $cnt[$user->id] = \App\Roster::user($user->id)->month($month)->count();
        }

        $date = date('Y-m-d', strtotime($month . '01'));
        $prev = date('Ym', strtotime($date . '-1 month'));
        $next = date('Ym', strtotime($date . '+1 month'));
        return view('roster.app.work_plan.user_list', ['users' => $users, 'month' => $month, 'cnt' => $cnt, 'next' => $next, 'prev' => $prev,]);
    }

    public function userList($month, $id) {
//        var_dump($month . $id);
        $plans = \App\Roster::where('month_id', '=', $month)
                ->where('user_id', '=', $id)
                ->get()
        ;
//        $count = $plans->count();
//        $plans = $plans->get();
//        foreach($plans as $p){
//        var_dump($p);
//        }
//        exit();

        $user = \App\RosterUser::user($id)->join('laravel_db.users', 'roster_users.user_id', '=', 'users.id')->first();

        $obj       = new \App\Services\Roster\Calendar();
        $calendar  = $obj->setId($month)->makeCalendar($plans);
        $tmp_rests = \App\Rest::orderBy('rest_reason_id')->get();
        $tmp_types = \App\WorkType::orderBy('work_type_id')->get();
        $types     = [];
        $rests     = [];
        foreach ($tmp_rests as $r) {
            $rests[$r->rest_reason_id] = [
                'rest_reason_id'   => $r->rest_reason_id,
                'rest_reason_name' => $r->rest_reason_name,
            ];
        }

        foreach ($tmp_types as $t) {
            $work_time = null;
            if ($t->work_start_time !== $t->work_end_time)
            {
                $work_time = '（' . date('G:i', strtotime($t->work_start_time)) . " ～ " . date('G:i', strtotime($t->work_end_time)) . '）';
            }
            $types[$t->work_type_id] = [
                'work_type_id'   => $t->work_type_id,
                'work_type_name' => $t->work_type_name,
                'work_time'      => $work_time,
            ];
        }

//        var_dump($calendar);
//        var_dump($count);
//        var_dump($plans);
        return view('roster.app.work_plan.edit_plan', ['days' => $calendar, 'id' => $id, 'types' => $types, 'rests' => $rests, 'user' => $user, 'month' => $month]);
    }

    public function edit($month, $id, WorkPlan $request) {
//        var_dump("edit");
        $in   = $request->input();
//        var_dump($in);
        $name = \App\User::find($id)->name;
        \DB::connection('mysql_roster')->transaction(function () use($in, $month, $id) {
            foreach ($in['entered_on'] as $i => $key) {
                $r                      = \App\Roster::firstOrNew(['user_id' => $id, 'month_id' => $month, 'entered_on' => $key]);
//                var_dump($r->id);
                $r->user_id             = $id;
                $r->plan_work_type_id   = $in['work_type'][$key];
                $r->plan_rest_reason_id = $in['rest'][$key];
                $r->entered_on          = $key;
                if ($r->id == null)
                {
                    $r->create_user_id = \Auth::user()->id;
                }
                else
                {
                    $r->edit_user_id = \Auth::user()->id;
                }
                $r->save();
            }
        });
        \Session::flash('flash_message', "{$name}さんのデータを更新しました。");
        return redirect(route('app::roster::work_plan::division', ['month' => $month]));
    }

}
