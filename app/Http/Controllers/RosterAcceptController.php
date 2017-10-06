<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Roster\AcceptPlan;
use App\Http\Requests\Roster\CalendarAccept;
use App\Http\Controllers\Controller;

class RosterAcceptController extends Controller
{

    private function getQuery() {
        $id = \Auth::user()->id;
        $t  = \DB::connection('mysql_sinren')
                ->table('control_divisions')
                // connect to sinren_users
                ->join('sinren_db.sinren_users', 'control_divisions.division_id', '=', 'sinren_users.division_id')
                // connect to roster_users
                ->join('roster_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                // connect to rosters
                ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
                // connect to divisions
                ->join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->select(\DB::raw('COUNT(*) AS cnt, sinren_divisions.division_id, rosters.month_id')) //, rosters.is_plan_accept, rosters.is_plan_reject, rosters.is_actual_accept, rosters.is_actual_reject
                ->where('control_divisions.user_id', '=', $id)
                ->where('rosters.month_id', '<>', 0)
                ->where('roster_users.user_id', '<>', $id)
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
//        var_dump($rows);
        foreach ($rows as $row) {
            $tmp[$row->division_id][$row->month_id] = $row->cnt;
        }
//        var_dump($tmp);
        return $tmp;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
//        var_dump("test");

        $plan_accept       = $this->getQuery()->groupBy('rosters.is_plan_accept')->where('is_plan_accept', '=', true)->take(12)->get();
        $plan_reject       = $this->getQuery()->groupBy('rosters.is_plan_reject')->where('is_plan_reject', '=', true)->take(12)->get();
        $actual_accept     = $this->getQuery()->groupBy('rosters.is_actual_accept')->where('is_actual_accept', '=', true)->take(12)->get();
        $actual_reject     = $this->getQuery()->groupBy('rosters.is_actual_reject')->where('is_actual_reject', '=', true)->take(12)->get();
        $plan_entry        = $this->getQuery()->groupBy('rosters.is_plan_entry')->where('is_plan_entry', '=', true)->take(12)->get();
        $actual_entry      = $this->getQuery()->groupBy('rosters.is_actual_entry')->where('is_actual_entry', '=', true)->take(12)->get();
        $plan_not_accept   = $this->getQuery()->groupBy('rosters.is_plan_entry')->where(['is_plan_entry' => true, 'is_plan_accept' => false, 'is_plan_reject' => false,])->take(12)->get();
        $actual_not_accept = $this->getQuery()->groupBy('rosters.is_actual_entry')->where(['is_actual_entry' => true, 'is_actual_accept' => false, 'is_actual_reject' => false,])->take(12)->get();

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
//        var_dump($rows);
//        exit();
//        foreach ($t as $tmp) {
//            $rows[$tmp->division_id][$tmp->month_id] = [
//                'cnt'              => $tmp->cnt,
//                'is_plan_accept'   => $tmp->is_plan_accept,
//                'is_plan_reject'   => $tmp->is_plan_reject,
//                'is_actual_accept' => $tmp->is_actual_accept,
//                'is_actual_reject' => $tmp->is_actual_reject,
//            ];
//        }

        $divs = \DB::connection('mysql_sinren')
                ->table('control_divisions')
                // connect to divisions
                ->join('sinren_db.sinren_divisions', 'control_divisions.division_id', '=', 'sinren_divisions.division_id')
                ->where('control_divisions.user_id', '=', \Auth::user()->id)
                ->orderBy('sinren_divisions.division_id', 'asc')
                ->get()
        ;

        $months = \App\Roster::groupBy('month_id')->where('month_id', '<>', 0)->take(12)->orderBy('month_id', 'desc')->get(['month_id']);
//        var_dump($rows);
//        var_dump($divs);
//        var_dump($months);
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

    public function calendar($ym, $div) {
        $obj      = new \App\Services\Roster\Calendar();
        $cal      = $obj->setId($ym)->makeCalendar();
        $cal      = $obj->convertCalendarToList($cal);
        $calendar = [];
        foreach ($cal as $c) {
            $rows = \App\Roster::where('entered_on', '=', $c['date'])
                    ->join('sinren_db.sinren_users', 'rosters.user_id', '=', 'sinren_users.user_id')
                    ->join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                    ->join('laravel_db.users', 'rosters.user_id', '=', 'users.id')
                    ->select(\DB::raw('*, rosters.id as key_id'))
                    ->where('sinren_users.division_id', '=', $div)
                    ->where('sinren_users.user_id', '<>', \Auth::user()->id)
                    ->get()
            ;
            $tmp  = [];
            foreach ($rows as $r) {
                $tmp[$r->user_id] = $r;
            }
            if (!empty($tmp))
            {
                $c['data'] = $tmp;
            }
            $calendar[] = $c;
        }
        $tmp_types = \App\WorkType::orderBy('work_type_id')->get();
        $types     = [];
        foreach ($tmp_types as $t) {
            $types[$t->work_type_id] = [
                'name' => $t->work_type_name,
                'time' => null,
            ];
            if ($t->work_start_time !== $t->work_end_time)
            {
                $types[$t->work_type_id]['time'] = '（ ' . date('G:i', strtotime($t->work_start_time)) . ' ～ ' . date('G:i', strtotime($t->work_end_time)) . ' ）';
            }
        }
        $rs    = \App\Rest::get();
        $rests = [];
        foreach ($rs as $r) {
            $rests[$r->rest_reason_id] = $r->rest_reason_name;
        }
        $users = \DB::connection('mysql_sinren')
                ->table('sinren_users')
                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->where('sinren_users.division_id', '=', $div)
                ->where('sinren_users.user_id', '<>', \Auth::user()->id)
                ->get()
        ;
        $d     = date('Y-m-d', strtotime($ym . '01'));
        $prev  = date('Ym', strtotime($d . ' -1 month'));
        $next  = date('Ym', strtotime($d . ' +1 month'));

        $param = [
            'prev'  => $prev,
            'next'  => $next,
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
        $in = $request->input();

        $debug = [];
        foreach ($in['id'] as $i => $id) {
            $r                   = \App\Roster::find($id);
            $debug[$i]['before'] = $r;
        }


        \DB::connection('mysql_roster')->transaction(function() use($in) {
            foreach ($in['id'] as $id) {
                $r = \App\Roster::find($id);

                if (isset($in['plan'][$id]))
                {
                    if ($in['plan'][$id])
                    {
                        $r->is_plan_accept      = (int) true;
                        $r->is_plan_reject      = (int) false;
                        $r->plan_accepted_at    = date('Y-m-d H:i:s');
                        $r->plan_accept_user_id = \Auth::user()->id;
                        $r->reject_reason       = '';
//                        $r->plan_reject_user_id = 0;
                    }
                    else
                    {
                        $r->is_plan_accept      = (int) false;
                        $r->is_plan_reject      = (int) true;
                        $r->plan_rejected_at    = date('Y-m-d H:i:s');
//                        $r->plan_accept_user_id = 0;
                        $r->plan_reject_user_id = \Auth::user()->id;
                        $r->reject_reason       = $in['plan_reject'][$id];
                    }
                }
                if (isset($in['actual'][$id]))
                {
                    if ($in['actual'][$id])
                    {
                        $r->is_actual_accept      = (int) true;
                        $r->is_actual_reject      = (int) false;
                        $r->actual_accepted_at    = date('Y-m-d H:i:s');
                        $r->actual_accept_user_id = \Auth::user()->id;
                        $r->reject_reason         = '';

//                        $r->actual_reject_user_id = 0;
                    }
                    else
                    {
                        $r->is_actual_accept      = (int) false;
                        $r->is_actual_reject      = (int) true;
                        $r->actual_rejected_at    = date('Y-m-d H:i:s');
//                        $r->actual_accept_user_id = 0;
                        $r->actual_reject_user_id = \Auth::user()->id;
                        $r->reject_reason         = $in['actual_reject'][$id];
                    }
                }
                $r->save();
            }
        });

        \Session::flash('flash_message', 'データの一括更新が完了しました。');
        return back();

        // ==== debug ====
//        var_dump('== input ======================================');
//        var_dump($in);
//        foreach ($in['id'] as $i => $id) {
//            $r                  = \App\Roster::find($id);
//            $debug[$i]['after'] = $r;
//        }
//        echo "<table style='width: 100%;'>";
//        echo "<tr>";
//        echo "<th></th>";
//        echo "<th>plan-before</th>";
//        echo "<th></th>";
//        echo "<th>plan-after</th>";
//        echo "<th>actual-before</th>";
//        echo "<th></th>";
//        echo "<th>actual-after</th>";
//        echo "</tr>";
//        foreach ($debug as $d) {
//
//            echo "<tr>";
//            echo "<th>id</th>";
//            echo "<th colspan='3'>{$d['before']->id}</th>";
//            echo "<th colspan='3'>{$d['after']->id}</th>";
//            echo "</tr>";
//
//            echo "<tr>";
//            echo "<th>is_accept</th>";
//            echo "<td>{$d['before']->is_plan_accept}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->is_plan_accept}</td>";
//            echo "<td>{$d['before']->is_actual_accept}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->is_actual_accept}</td>";
//
//            echo "</tr>";
//
//            echo "<tr>";
//            echo "<th>is_reject</th>";
//            echo "<td>{$d['before']->is_plan_reject}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->is_plan_reject}</td>";
//            echo "<td>{$d['before']->is_actual_reject}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->is_actual_reject}</td>";
//            echo "</tr>";
//
//            echo "<tr>";
//            echo "<th>accept_at</th>";
//            echo "<td>{$d['before']->plan_accepted_at}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->plan_accepted_at}</td>";
//            echo "<td>{$d['before']->actual_accepted_at}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->actual_accepted_at}</td>";
//            echo "</tr>";
//
//            echo "<tr>";
//            echo "<th>reject_at</th>";
//            echo "<td>{$d['before']->plan_rejected_at}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->plan_rejected_at}</td>";
//            echo "<td>{$d['before']->actual_rejected_at}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->actual_rejected_at}</td>";
//            echo "</tr>";
//
//            echo "<tr>";
//            echo "<th>accept_user_id</th>";
//            echo "<td>{$d['before']->plan_accept_user_id}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->plan_accept_user_id}</td>";
//            echo "<td>{$d['before']->actual_accept_user_id}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->actual_accept_user_id}</td>";
//            echo "</tr>";
//
//            echo "<tr>";
//            echo "<th>reject_user_id</th>";
//            echo "<td>{$d['before']->plan_reject_user_id}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->plan_reject_user_id}</td>";
//            echo "<td>{$d['before']->actual_reject_user_id}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->actual_reject_user_id}</td>";
//            echo "</tr>";
//
//            echo "<tr>";
//            echo "<th>reject_reason</th>";
//            echo "<td>{$d['before']->reject_reason}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->reject_reason}</td>";
//            echo "<td>{$d['before']->reject_reason}</td>";
//            echo "<td> -> </td>";
//            echo "<td>{$d['after']->reject_reason}</td>";
//            echo "</tr>";
//        }
        // ==== debug ====
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ym, $div) {
        $plans   = \DB::connection('mysql_sinren')
                ->table('sinren_users')
                ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->select(\DB::raw('*, rosters.id as form_id'))
                ->where('rosters.month_id', '=', $ym)
                ->where('sinren_users.division_id', '=', $div)
                ->where('is_plan_entry', '=', true)
                ->where('is_plan_accept', '<>', true)
                ->where('sinren_users.user_id', '<>', \Auth::user()->id)
                ->orderBy('entered_on', 'asc')
                ->orderBy('sinren_users.user_id', 'asc')
                ->get()
        ;
        /**
         * 予定が承認済みであること
         * かつ実績が入力されていること
         * かつ実績が承認されていないこと
         * かつ自分が入力したデータではないこと
         */
        $actuals = \DB::connection('mysql_sinren')
                ->table('sinren_users')
                ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->select(\DB::raw('*, rosters.id as form_id'))
                ->where('rosters.month_id', '=', $ym)
                ->where('sinren_users.division_id', '=', $div)
                ->where('is_plan_accept', '=', true) // 予定承認済みのものだけ抽出
                ->where('is_actual_entry', '=', true)
                ->where('is_actual_accept', '<>', true)
                ->where('sinren_users.user_id', '<>', \Auth::user()->id)
                ->orderBy('entered_on', 'asc')
                ->orderBy('sinren_users.user_id', 'asc')
                ->get()
        ;
        $t_rests = \App\Rest::get()->toArray();
        $rests   = [];
        foreach ($t_rests as $rest) {
            $rests[$rest['id']] = $rest;
        }
//        var_dump($rests);
//        var_dump($plans);
//        var_dump($actuals);
        $divs   = \App\Division::where('division_id', '=', $div)->first();
        $params = [
            'actuals' => $actuals,
            'plans'   => $plans,
            'ym'      => $ym,
            'display' => date('Y年n月', strtotime($ym . '01')),
            'div'     => $divs,
            'rests'   => $rests,
        ];
        return view('roster.app.accept.list', $params);
    }

    public function part($type, $id) {
        $in    = \Input::get();
        $input = [
            'id'     => $id,
            $type    => $in[$type][$id],
            'reject' => $in['reject'][$id],
        ];
        $rules = [
            'id'  => 'required|exists:mysql_roster.rosters,id',
            $type => 'required|boolean',
        ];
        $v     = \Validator::make($input, $rules);
        if ($v->fails() || ($type != 'plan' && $type != 'actual'))
        {
            \Session::flash('warn_message', '予期しないエラーが発生しました。');
            return back()->withErrors($v);
        }
        $roster = \App\Roster::find($input['id']);
        if (isset($input[$type]) && !$input[$type])
        {
//            var_dump("1");
            $key          = "is_{$type}_accept";
            $roster->$key = (int) false;
            $key          = "is_{$type}_reject";
            $roster->$key = (int) true;
            $key          = "{$type}_rejected_at";
            $roster->$key = date('Y-m-d H:i:s');
            $key          = "{$type}_reject_user_id";
            $roster->$key = \Auth::user()->id;
        }
        else
        {
//            var_dump("2");
            $key          = "is_{$type}_accept";
            $roster->$key = (int) true;
            $key          = "is_{$type}_reject";
            $roster->$key = (int) false;
            $key          = "{$type}_accepted_at";
            $roster->$key = date('Y-m-d H:i:s');
            $key          = "{$type}_accept_user_id";
            $roster->$key = \Auth::user()->id;
        }
//        exit();
        $roster->reject_reason = $input['reject'];
        $roster->save();
        \Session::flash('flash_message', 'データの更新が完了しました。');
        return back();
    }

    public function all($type, AcceptPlan $request) {
        $input = $request->all();
//        var_dump($input);
        if ($type != 'plan' && $type != 'actual')
        {
            \Session::flash('warn_message', '予期しないエラーが発生しました。');
            return back();
        }
        \DB::connection('mysql_roster')->transaction(function() use ($input, $type) {
            foreach ($input['form_id'] as $id) {
                $roster = \App\Roster::find($id);
                if (!$input[$type][$id])
                {
                    $key          = "is_{$type}_accept";
                    $roster->$key = (int) false;
                    $key          = "is_{$type}_reject";
                    $roster->$key = (int) true;
                    $key          = "{$type}_rejected_at";
                    $roster->$key = date('Y-m-d H:i:s');
                    $key          = "{$type}_reject_user_id";
                    $roster->$key = \Auth::user()->id;
                }
                else
                {
                    $key          = "is_{$type}_accept";
                    $roster->$key = (int) true;
                    $key          = "is_{$type}_reject";
                    $roster->$key = (int) false;
                    $key          = "{$type}_accepted_at";
                    $roster->$key = date('Y-m-d H:i:s');
                    $key          = "{$type}_accept_user_id";
                    $roster->$key = \Auth::user()->id;
                }
                $roster->reject_reason = $input['reject'][$id];
                $roster->save();
            }
        });
        \Session::flash('flash_message', 'データの一括更新が完了しました。');
        return back();
    }

}
