<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    const COLORS = [
        '未入力' => '200, 200, 200',
        '未承認' => '218, 131,0',
        '却下'  => '206, 51, 35',
        '承認'  => '0, 163, 131',
    ];

    public function show()
    {
        $new_users     = [];
        $is_chief      = false;
        $notifications = \App\Notification::with('user')->deadline(date('Y-m-d'))->orderBy('created_at', 'desc')->take(5)->get();


        if (!\Auth::check()) {
            return view('auth.login', ['notifications' => $notifications]);
        }
        $user       = \Auth::user();
        $roster_log = $this->getRosterUserLog($user->id);

        if ($user->is_super_user) {
            $new_users = $this->getNewUser();
            return view('admin.home', ['new_users' => $new_users, 'notifications' => $notifications, 'is_chief' => $is_chief]);
        }
        $roster = \App\RosterUser::user($user->id);
        if ($roster->exists() && $roster->first()->is_administrator) {
            return view('admin.home', ['new_users' => $new_users, 'notifications' => $notifications, 'is_chief' => $is_chief]);
        }
        if (!$roster->exists()) {
            return view('app.home', ['rows' => null, 'notifications' => $notifications, 'is_chief' => $is_chief]);
        }

        $roster_user = $roster->first();

        $cu       = \App\ControlDivision::where('control_divisions.user_id', $user->id)
            ->join('sinren_db.sinren_users', 'control_divisions.division_id', '=', 'sinren_users.division_id')
            ->join('sinren_db.sinren_divisions', 'control_divisions.division_id', '=', 'sinren_divisions.division_id')
            ->get(['sinren_users.user_id', 'control_divisions.division_id', 'sinren_divisions.division_name']);
        $users    = [];
        $is_chief = ($roster_user->is_chief || ($roster_user->is_proxy && $roster_user->is_proxy_active)) ? true : false;
        foreach ($cu as $c) {
            $users[$c->division_id]['division_name'] = $c->division_name;
            $users[$c->division_id]['users'][]       = $c->user_id;
        }

        foreach ($users as $div => $u) {
            $r                    = ($is_chief) ? $this->getRosterChiefNotice($u['users']) : null;
            $users[$div]['count'] = $r;
        }
        $roster_chief_cnt = $users;
//        \Log::debug(['roster_chief_cnt' => $roster_chief_cnt]);
        $roster_user_cnt = $this->getRosterUserNotice($user->id, date('Y-m-d'));

        $s    = new \App\Http\Controllers\RosterCsvExportController();
        $rows = $s->getEnteredUsers();

        $params = ['roster_chief_cnt' => $roster_chief_cnt,
                   'roster_user_cnt'  => $roster_user_cnt,
                   'notifications'    => $notifications,
                   'roster_log'       => $roster_log,
                   'is_chief'         => $is_chief,
                   'rows'             => $rows,
                   'colors'           => self::COLORS,
        ];
        return view('app.home', $params);
    }

    public function permissionError()
    {
        return view('admin.permission_error');
    }

    public function getNewUser()
    {
        $start = date('Y-m-d', strtotime('-1 month'));
        $res   = \App\User::select(\DB::raw('first_name, last_name, updated_at, created_at = updated_at as is_new'))
            ->where('updated_at', '>=', $start)
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();
        return $res;
    }

    private function getRosterChiefNotice($users)
    {

        $cnt = \App\Roster::where(function ($query) use ($users) {
            foreach ($users as $user_id) {
                $query->orWhere('user_id', $user_id);
            }
        })
            ->where(function ($query) {
                $query->orWhere(function ($query) {
                    $query->where('is_plan_entry', true)->where('is_plan_accept', false);
                })->orWhere(function ($query) {
                    $query->where('is_actual_entry', true)->where('is_actual_accept', false);
                });
            })
            ->groupBy('month_id')
            ->select(\DB::raw('count(*) as total, month_id'))
            ->take(4)
            ->get();
        return (!empty($cnt) && !$cnt->isEmpty()) ? $cnt->toArray() : [['month_id' => null, 'total' => 0]];
//        dd($res);
//        $res = \App\ControlDivision::where('control_divisions.user_id', $user_id)
//                ->join('sinren_db.sinren_users', 'control_divisions.division_id', '=', 'sinren_users.division_id')
//                ->get()
//                ->toArray()
//        ;
//        \Log::debug($res);
//        dd($res);
//        $res = \App\ControlDivision::leftJoin('sinren_db.sinren_divisions as S_DIV', 'control_divisions.division_id', '=', 'S_DIV.division_id')
//                ->leftJoin('sinren_db.sinren_users as S_USER', 'control_divisions.division_id', '=', 'S_USER.division_id')
//                ->leftJoin('roster_db.rosters as ROSTER', 'S_USER.user_id', '=', 'ROSTER.user_id')
//                ->where('control_divisions.user_id', '=', $user_id)
//                ->where('S_USER.user_id', '<>', $user_id)
//                ->where('ROSTER.is_plan_entry', '=', true)
////                ->where(function ($query) {
////                    $query->orWhere('is_plan_accept', '=', false);
////                    $query->orWhere('is_actual_accept', '=', false);
////                })
//                ->where(function($query) {
//                    $query->orWhere(function($query) {
//                        $query->where('ROSTER.is_plan_entry', true)->where('ROSTER.is_plan_accept', false);
//                    })->orWhere(function($query) {
//                        $query->where('ROSTER.is_actual_entry', true)->where('ROSTER.is_actual_accept', false);
//                    });
//                })
//                ->select(\DB::raw('count(*) as total, S_DIV.division_id as division_id, S_DIV.division_name as division_name, ROSTER.month_id as month_id'))
//                ->groupBy('month_id')
//                ->orderBy('ROSTER.month_id', 'desc')
//                ->take(4)
//                ->get()
//        ;
////        dd($res);
//        return $res;
    }

    private function getRosterUserLog($user_id)
    {
        $columns = ['users.first_name', 'users.last_name', 'rosters.updated_at as timestamp', 'sinren_divisions.division_name', 'entered_on'];
        $rows    = \App\ControlDivision::join('sinren_db.sinren_users', 'control_divisions.division_id', '=', 'sinren_users.division_id')
            ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
            ->join('roster_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
            ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
            ->leftJoin('sinren_db.sinren_divisions', 'control_divisions.division_id', '=', 'sinren_divisions.division_id')
            ->select($columns)
            ->where('control_divisions.user_id', $user_id)
            ->where('roster_users.is_administrator', '!=', true)
            ->where('roster_users.is_chief', '!=', true)
            ->where('rosters.is_plan_entry', true)
            ->take(60)
//                ->groupBy('users.id')
//                ->groupBy('entered_on')
            ->orderBy('timestamp', 'desc')
            ->get()
            ->chunk(5)//                ->toArray()
        ;
        if (env("APP_DEBUG"))
            \Log::debug($rows->toArray());
//        dd($rows);
        return $rows;
    }

    private function getRosterUserNotice($user_id, $limit_date)
    {
        $res = \App\Roster::where('entered_on', '<=', $limit_date)
            ->leftJoin('sinren_db.holidays', 'rosters.entered_on', '=', 'holidays.holiday')
            ->select(\DB::raw('count(is_plan_entry = 0 or null) as plan_total, count(is_actual_entry = 0 or null) as actual_total, month_id, user_id'))
            ->where('rosters.user_id', '=', $user_id)
            ->whereNull('holidays.holiday_name')
            ->where(\DB::raw('DAYOFWEEK(entered_on)'), '<>', 1)
            ->where(\DB::raw('DAYOFWEEK(entered_on)'), '<>', 7)
            ->where(function ($query) {
                $query->orWhere(['is_plan_accept' => false, 'is_actual_accept' => false]);
            })
            ->orderby('month_id', 'desc')
            ->groupBy('month_id')
            ->take(4)
//                ->toSql()
            ->get();
//        dd($res);
        return $res;
    }

}
