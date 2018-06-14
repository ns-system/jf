<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public function show() {
        $new_users     = [];
        $notifications = \App\Notification::with('user')->deadline(date('Y-m-d'))->orderBy('created_at', 'desc')->take(5)->get();

        if (!\Auth::check())
        {
            return view('auth.login', ['notifications' => $notifications]);
        }
        $user = \Auth::user();

        if ($user->is_super_user)
        {
            $new_users = $this->getNewUser();
            return view('admin.home', ['new_users' => $new_users, 'notifications' => $notifications]);
        }
        $roster = \App\RosterUser::user($user->id);
        if ($roster->exists() && $roster->first()->is_administrator)
        {
            return view('admin.home', ['new_users' => $new_users, 'notifications' => $notifications]);
        }
        if (!$roster->exists())
        {
            return view('app.home', ['rows' => null, 'notifications' => $notifications]);
        }

        $roster_user      = $roster->first();
        $roster_chief_cnt = ($roster_user->is_chief || ($roster_user->is_proxy && $roster_user->is_proxy_active)) ? $this->getRosterChiefNotice(\Auth::user()->id) : null;
        $roster_user_cnt  = $this->getRosterUserNotice(\Auth::user()->id, date('Y-m-d'));
        return view('app.home', ['roster_chief_cnt' => $roster_chief_cnt, 'roster_user_cnt' => $roster_user_cnt, 'notifications' => $notifications]);
    }

    public function permissionError() {
        return view('admin.permission_error');
    }

    public function getNewUser() {
        $start = date('Y-m-d', strtotime('-1 month'));
        $res   = \App\User::select(\DB::raw('first_name, last_name, updated_at, created_at = updated_at as is_new'))
                ->where('updated_at', '>=', $start)
                ->orderBy('updated_at')
                ->take(10)
                ->get()
        ;
        return $res;
    }

    private function getRosterChiefNotice($user_id) {
//        dd($user_id);
        $res = \App\ControlDivision::leftJoin('sinren_db.sinren_divisions as S_DIV', 'control_divisions.division_id', '=', 'S_DIV.division_id')
                ->leftJoin('sinren_db.sinren_users as S_USER', 'control_divisions.division_id', '=', 'S_USER.division_id')
                ->leftJoin('roster_db.rosters as ROSTER', 'S_USER.user_id', '=', 'ROSTER.user_id')
                ->where('control_divisions.user_id', '=', $user_id)
                ->where('S_USER.user_id', '<>', $user_id)
                ->where('ROSTER.is_plan_entry', '=', true)
                ->where(function ($query) {
                    $query->orWhere('is_plan_accept', '=', false);
                    $query->orWhere('is_actual_accept', '=', false);
                })
                ->orderby('month_id', 'desc')
                ->groupBy('month_id')
                ->select(\DB::raw('count(*) as total, S_DIV.division_id as division_id, S_DIV.division_name as division_name, ROSTER.month_id as month_id'))
                ->orderBy('ROSTER.month_id', 'desc')
                ->take(4)
                ->get()
        ;
        return $res;
    }

    private function getRosterUserNotice($user_id, $limit_date) {
        $res = \App\Roster::where('entered_on', '<=', $limit_date)
                ->leftJoin('sinren_db.holidays', 'rosters.entered_on', '=', 'holidays.holiday')
                ->select(\DB::raw('count(is_plan_entry = 0 or null) as plan_total, count(is_actual_entry = 0 or null) as actual_total, month_id, user_id'))
                ->where('rosters.user_id', '=', $user_id)
                ->whereNull('holidays.holiday_name')
                ->where(\DB::raw('DAYOFWEEK(entered_on)'), '<>', 1)
                ->where(\DB::raw('DAYOFWEEK(entered_on)'), '<>', 7)
                ->where(function($query) {
                    $query->orWhere(['is_plan_accept' => false, 'is_actual_accept' => false]);
                })
                ->orderby('month_id', 'desc')
                ->groupBy('month_id')
                ->take(4)
//                ->toSql()
                ->get()
        ;
//        dd($res);
        return $res;
    }

}
