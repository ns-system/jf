<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Roster\AcceptPlan;
use App\Http\Requests\Roster\CalendarAccept;
use App\Http\Controllers\Controller;
use \App\Services\Roster\RosterAccept;

class RosterAcceptController extends Controller
{

    const INT_MONTH_COUNT = 12;

    private function getQuery() {
        $id = \Auth::user()->id;
        $t  = \App\ControlDivision::joinUsers($id)
                ->joinDivisions()
                // connect to rosters
                ->join('roster_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->where('roster_users.user_id', '<>', $id)
                ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
                ->where('rosters.month_id', '<>', 0)
                ->select(\DB::raw('COUNT(*) AS cnt, sinren_divisions.division_id, rosters.month_id'))
                ->groupBy('rosters.month_id')
                ->orderBy('rosters.month_id', 'desc')
        ;
        return $t;
    }

    private function editKey($rows) {
        if ($rows == null)
        {
            return [];
        }
        $tmp = [];
        foreach ($rows as $row) {
            $tmp[$row->division_id][$row->month_id] = $row->cnt;
        }
        return $tmp;
    }

    public function index() {
        // 月ごとのレコード件数を集計する
        $plan_accept       = $this->getQuery()->groupBy('rosters.is_plan_accept')->where('is_plan_accept', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $plan_reject       = $this->getQuery()->groupBy('rosters.is_plan_reject')->where('is_plan_reject', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $actual_accept     = $this->getQuery()->groupBy('rosters.is_actual_accept')->where('is_actual_accept', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $actual_reject     = $this->getQuery()->groupBy('rosters.is_actual_reject')->where('is_actual_reject', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $plan_entry        = $this->getQuery()->groupBy('rosters.is_plan_entry')->where('is_plan_entry', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $actual_entry      = $this->getQuery()->groupBy('rosters.is_actual_entry')->where('is_actual_entry', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $plan_not_accept   = $this->getQuery()->groupBy('rosters.is_plan_entry')->where(['is_plan_entry' => true, 'is_plan_accept' => false, 'is_plan_reject' => false,])->take(self::INT_MONTH_COUNT)->get();
        $actual_not_accept = $this->getQuery()->groupBy('rosters.is_actual_entry')->where(['is_actual_entry' => true, 'is_actual_accept' => false, 'is_actual_reject' => false,])->take(self::INT_MONTH_COUNT)->get();

        $rows = [
            'plan_accepts'       => $this->editKey($plan_accept),
            'plan_rejects'       => $this->editKey($plan_reject),
            'plan_entry'         => $this->editKey($plan_entry),
            'actual_accepts'     => $this->editKey($actual_accept),
            'actual_rejects'     => $this->editKey($actual_reject),
            'actual_entry'       => $this->editKey($actual_entry),
            'plan_not_accepts'   => $this->editKey($plan_not_accept),
            'actual_not_accepts' => $this->editKey($actual_not_accept),
        ];

        // 管理部署のリストを取得する
        $divs = \App\ControlDivision::joinDivisions()
                ->user(\Auth::user()->id)
                ->orderBy('sinren_divisions.division_id', 'asc')
                ->get()
        ;

        // 最大INT_MONTH_COUNTヶ月分の表示する月を生成
        $months = \App\Roster::groupBy('month_id')->where('month_id', '<>', 0)->take(self::INT_MONTH_COUNT)->orderBy('month_id', 'desc')->get(['month_id']);
        $params = [
            'rows'          => $rows,
            'divs'          => $divs,
            'plan_accept'   => $plan_accept,
            'plan_reject'   => $plan_reject,
            'actual_accept' => $actual_accept,
            'actual_reject' => $actual_reject,
            'plan_entry'    => $plan_entry,
            'actual_entry'  => $actual_entry,
            'months'        => $months,
        ];
        return view('roster.app.accept.index', $params);
    }

    public function calendar($ym, $div, $all = 'all') {
        $is_show_all = ($all == 'all') ? true : false;
//        dd($is_show_all);
        $obj         = new \App\Services\Roster\Calendar();
        $tmp         = $obj->setId($ym)->makeCalendar();
        $cal         = $obj->convertCalendarToList($tmp);
        $calendar    = [];

        $sql = \App\Roster::join('sinren_db.sinren_users as S_USER', 'rosters.user_id', '=', 'S_USER.user_id')
                ->join('roster_db.roster_users as R_USER', 'rosters.user_id', '=', 'R_USER.user_id')
                ->join('sinren_db.sinren_divisions', 'S_USER.division_id', '=', 'sinren_divisions.division_id')
                ->join('laravel_db.users', 'rosters.user_id', '=', 'users.id')
                ->select(\DB::raw('*, rosters.id as key_id'))
                ->where('S_USER.division_id', '=', $div)
                ->where('S_USER.user_id', '<>', \Auth::user()->id)
                ->where('users.is_super_user', '=', false)
                ->where('R_USER.is_administrator', '=', false)
                ->where('R_USER.is_chief', '=', false)
        ;

        if ($is_show_all === false)
        {
            $sql = $sql->where('rosters.is_plan_entry', '=', true)
                    ->where(function($query) {
                $query->orWhere('rosters.is_plan_accept', '<>', true)->orWhere('rosters.is_actual_accept', '<>', true);
            })
            ;
        }

//        dd($sql->toSql());
//        $tmp_rows = $sql->get();
        $rows = [];
        foreach ($sql->get() as $t) {
            $rows[date('Y-m-d', strtotime($t->entered_on))] = $t;
        }
        foreach ($cal as $c) {
            $date = $c['date'];
            if (empty($rows[$date]))
            {
                continue;
            }
            $row                   = $rows[$date];
            $roster[$row->user_id] = $row;
            $c['data']             = $roster;
            $calendar[]            = $c;
        }

        $tmp_types = \App\WorkType::orderBy('work_type_id')->get();
        $types     = [];
        foreach ($tmp_types as $t) {
            $types[$t->work_type_id] = [
                'name' => $t->work_type_name,
                'time' => $t->display_time,
            ];
        }
        $tmp_rests = \App\Rest::get();
        $rests     = [];
        foreach ($tmp_rests as $r) {
            $rests[$r->rest_reason_id] = $r->rest_reason_name;
        }
        $users = \App\SinrenUser::join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->join('roster_db.roster_users as R_USER', 'users.id', '=', 'R_USER.user_id')
                ->where('sinren_users.division_id', '=', $div)
                ->where('sinren_users.user_id', '<>', \Auth::user()->id)
                ->where('R_USER.is_administrator', '=', false)
                ->where('R_USER.is_chief', '=', false)
                ->get()
        ;
        $param = [
            'ym'    => $ym,
            'div'   => $div,
            'rests' => $rests,
            'types' => $types,
            'rows'  => $calendar,
            'users' => $users,
        ];
        return view('roster.app.accept.calendar_list', $param);
    }

    public function calendarAccept(CalendarAccept $request) {
        $input = $request->input();
        try {
            \DB::connection('mysql_roster')->transaction(function() use($input) {
                $service = new RosterAccept(\Auth::user()->id);
                $service->updateRoster($input);
            });
        } catch (\Exception $e) {
            \Session::flash('danger_message', $e->getMessage());
            return back();
        }
        \Session::flash('success_message', 'データの一括更新が完了しました。');
        return back();
    }

//
//    public function getNotAccept($monthly_id, $division_id) {
//        \App\Roster::where('month_id', '=', $monthly_id)
//                ->leftJoin('sinren_db.sinren_users as S_USER', 'rosters.user_id', '=', 'S_USER.user_id')
//                ->leftJoin('sinren_db.sinren_divisions as S_DIV', 'S_USER.division_id', '=', 'S_DIV.division_id')
//
//        ;
//    }
//    public function show($ym, $div) {
//        $plans   = \DB::connection('mysql_sinren')
//                ->table('sinren_users')
//                ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
//                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
//                ->select(\DB::raw('*, rosters.id as form_id'))
//                ->where('rosters.month_id', '=', $ym)
//                ->where('sinren_users.division_id', '=', $div)
//                ->where('is_plan_entry', '=', true)
//                ->where('is_plan_accept', '<>', true)
//                ->where('sinren_users.user_id', '<>', \Auth::user()->id)
//                ->orderBy('entered_on', 'asc')
//                ->orderBy('sinren_users.user_id', 'asc')
//                ->get()
//        ;
//        /**
//         * 予定が承認済みであること
//         * かつ実績が入力されていること
//         * かつ実績が承認されていないこと
//         * かつ自分が入力したデータではないこと
//         */
//        $actuals = \DB::connection('mysql_sinren')
//                ->table('sinren_users')
//                ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
//                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
//                ->select(\DB::raw('*, rosters.id as form_id'))
//                ->where('rosters.month_id', '=', $ym)
//                ->where('sinren_users.division_id', '=', $div)
//                ->where('is_plan_accept', '=', true) // 予定承認済みのものだけ抽出
//                ->where('is_actual_entry', '=', true)
//                ->where('is_actual_accept', '<>', true)
//                ->where('sinren_users.user_id', '<>', \Auth::user()->id)
//                ->orderBy('entered_on', 'asc')
//                ->orderBy('sinren_users.user_id', 'asc')
//                ->get()
//        ;
//        $t_rests = \App\Rest::get()->toArray();
//        $rests   = [];
//        foreach ($t_rests as $rest) {
//            $rests[$rest['id']] = $rest;
//        }
////        var_dump($rests);
////        var_dump($plans);
////        var_dump($actuals);
//        $divs   = \App\Division::where('division_id', '=', $div)->first();
//        $params = [
//            'actuals' => $actuals,
//            'plans'   => $plans,
//            'ym'      => $ym,
//            'display' => date('Y年n月', strtotime($ym . '01')),
//            'div'     => $divs,
//            'rests'   => $rests,
//        ];
//        return view('roster.app.accept.list', $params);
//    }
//
//    public function part($type, $id) {
//        $in    = \Input::get();
//        $input = [
//            'id'     => $id,
//            $type    => $in[$type][$id],
//            'reject' => $in['reject'][$id],
//        ];
//        $rules = [
//            'id'  => 'required|exists:mysql_roster.rosters,id',
//            $type => 'required|boolean',
//        ];
//        $v     = \Validator::make($input, $rules);
//        if ($v->fails() || ($type != 'plan' && $type != 'actual'))
//        {
//            \Session::flash('warn_message', '予期しないエラーが発生しました。');
//            return back()->withErrors($v);
//        }
//        $roster = \App\Roster::find($input['id']);
//        if (isset($input[$type]) && !$input[$type])
//        {
////            var_dump("1");
//            $key          = "is_{$type}_accept";
//            $roster->$key = (int) false;
//            $key          = "is_{$type}_reject";
//            $roster->$key = (int) true;
//            $key          = "{$type}_rejected_at";
//            $roster->$key = date('Y-m-d H:i:s');
//            $key          = "{$type}_reject_user_id";
//            $roster->$key = \Auth::user()->id;
//        }
//        else
//        {
////            var_dump("2");
//            $key          = "is_{$type}_accept";
//            $roster->$key = (int) true;
//            $key          = "is_{$type}_reject";
//            $roster->$key = (int) false;
//            $key          = "{$type}_accepted_at";
//            $roster->$key = date('Y-m-d H:i:s');
//            $key          = "{$type}_accept_user_id";
//            $roster->$key = \Auth::user()->id;
//        }
////        exit();
//        $roster->reject_reason = $input['reject'];
//        $roster->save();
//        \Session::flash('success_message', 'データの更新が完了しました。');
//        return back();
//    }
//
//    public function all($type, AcceptPlan $request) {
//        $input = $request->all();
////        var_dump($input);
//        if ($type != 'plan' && $type != 'actual')
//        {
//            \Session::flash('warn_message', '予期しないエラーが発生しました。');
//            return back();
//        }
//        \DB::connection('mysql_roster')->transaction(function() use ($input, $type) {
//            foreach ($input['form_id'] as $id) {
//                $roster = \App\Roster::find($id);
//                if (!$input[$type][$id])
//                {
//                    $key          = "is_{$type}_accept";
//                    $roster->$key = (int) false;
//                    $key          = "is_{$type}_reject";
//                    $roster->$key = (int) true;
//                    $key          = "{$type}_rejected_at";
//                    $roster->$key = date('Y-m-d H:i:s');
//                    $key          = "{$type}_reject_user_id";
//                    $roster->$key = \Auth::user()->id;
//                }
//                else
//                {
//                    $key          = "is_{$type}_accept";
//                    $roster->$key = (int) true;
//                    $key          = "is_{$type}_reject";
//                    $roster->$key = (int) false;
//                    $key          = "{$type}_accepted_at";
//                    $roster->$key = date('Y-m-d H:i:s');
//                    $key          = "{$type}_accept_user_id";
//                    $roster->$key = \Auth::user()->id;
//                }
//                $roster->reject_reason = $input['reject'][$id];
//                $roster->save();
//            }
//        });
//        \Session::flash('success_message', 'データの一括更新が完了しました。');
//        return back();
//    }
}
