<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Roster\AcceptPlan;
use App\Http\Controllers\Controller;

class RosterAcceptController extends Controller
{

    private function getQuery() {
        $id = \Auth::user()->id;
        $t  = \DB::connection('mysql_sinren')
                ->table('control_divisions')
                // connect to sinren_users
                ->join('sinren_data_db.sinren_users', 'control_divisions.division_id', '=', 'sinren_users.division_id')
                // connect to roster_users
                ->join('roster_data_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                // connect to rosters
                ->join('roster_data_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
                // connect to divisions
                ->join('sinren_data_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
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
                ->join('sinren_data_db.sinren_divisions', 'control_divisions.division_id', '=', 'sinren_divisions.division_id')
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
                    ->join('sinren_data_db.sinren_users', 'rosters.user_id', '=', 'sinren_users.user_id')
                    ->join('sinren_data_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                    ->join('laravel_db.users', 'rosters.user_id', '=', 'users.id')
                    ->where('sinren_users.division_id', '=', $div)
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
            $types[$t->work_type_id] = [null,];
            if ($t->work_start_time !== $t->work_end_time)
            {
                $types[$t->work_type_id] = date('G:i', strtotime($t->work_start_time)) . ' ～ ' . date('G:i', strtotime($t->work_end_time));
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
            'rests'=>$rests,
            'types'=>$types,
            'rows'  => $calendar,
            'users' => $users,
        ];
        return view('roster.app.accept.calendar_list', $param);
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
                ->join('roster_data_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
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
                ->join('roster_data_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
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
