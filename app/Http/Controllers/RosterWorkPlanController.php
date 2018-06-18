<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Roster\WorkPlan;
use App\Http\Controllers\Controller;
use App\Services\Roster\RosterWorkPlan;

class RosterWorkPlanController extends Controller
{

    const INT_MONTH_LATER = 2;  // 何ヶ月後先まで表示するか
    const INT_MONTH_COUNT = 12; // 表示される月数

    public function index() {
        // 勤務データが登録されていれば最新のもの、でなければ今月を当月扱いとする
//        $current_month = (!empty(\App\Roster::max('month_id'))) ? \App\Roster::max('month_id') : date('Ym');
        $current_month = date('Ym');

        $months = [];
        $tmp    = $current_month . '01';
        for ($i = self::INT_MONTH_LATER; $i > self::INT_MONTH_LATER - self::INT_MONTH_COUNT; $i--) {
            $months[] = [
                'id'      => date('Ym', strtotime($tmp . " {$i} month")),
                'display' => date('Y年n月', strtotime($tmp . " {$i} month")),
            ];
        }
        return view('roster.app.work_plan.index', ['months' => $months, 'current' => date('Ym')]);
    }

    public function division($month) {

        $divs  = \App\ControlDivision::user(\Auth::user()->id)->get();
        $cnt   = [];
        $users = \App\SinrenUser::join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->join('roster_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->where(function($query) use ($divs) {
                    foreach ($divs as $d) {
                        $query->orWhere(['sinren_users.division_id' => $d->division_id]);
                    }
                })
//                ->where('roster_users.user_id', '<>', \Auth::user()->id)
                ->where('roster_users.is_administrator', '<>', true)
                ->where('roster_users.is_chief', '<>', true)
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

    public function userList($month, $user_id) {
        $plans     = \App\Roster::where(['month_id' => $month, 'user_id' => $user_id])->get();
        $user      = \App\RosterUser::user($user_id)->join('laravel_db.users', 'roster_users.user_id', '=', 'users.id')->first();
        $obj       = new \App\Services\Roster\Calendar();
        $calendar  = $obj->setId($month)->makeCalendar($plans);
        $tmp_rests = \App\Rest::orderBy('rest_reason_id')->get();
        $tmp_types = \App\WorkType::workTypeList()->get();
        $types     = [];
        $rests     = [];
        foreach ($tmp_rests as $r) {
            $rests[$r->rest_reason_id] = ['rest_reason_id' => $r->rest_reason_id, 'rest_reason_name' => $r->rest_reason_name,];
        }

        foreach ($tmp_types as $t) {
            $types[$t->work_type_id] = [
                'work_type_id'   => $t->work_type_id,
                'work_type_name' => $t->work_type_name,
                'work_time'      => (empty($t->display_time)) ? '' : '（' . $t->display_time . '）',
            ];
        }
        return view('roster.app.work_plan.edit_plan', ['days' => $calendar, 'id' => $user_id, 'types' => $types, 'rests' => $rests, 'user' => $user, 'month' => $month]);
    }

    public function edit($month_id, $user_id, WorkPlan $request) {
        $input    = $request->input();
        $name     = \App\User::find($user_id)->last_name;
        $service  = new RosterWorkPlan();
        $chief_id = \Auth::user()->id;
        try {
            $service->updateWorkPlan($input, $user_id, $month_id, $chief_id);
        } catch (\Exception $e) {
            \Log::error(['errors' => $e, 'input' => $input, 'user_id' => $user_id, 'month_id' => $month_id, 'chief_id' => $chief_id]);
            \Session::flash('warn_message', 'エラーがあったため処理を中断しました。');
            return back();
        }
        \Session::flash('success_message', "{$name}さんのデータを更新しました。");
        return redirect()->route('app::roster::work_plan::division', ['month' => $month_id]);
    }

}
