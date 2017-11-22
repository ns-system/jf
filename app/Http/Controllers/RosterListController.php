<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
//use App\Http\Requests;
use App\Http\Controllers\Controller;

class RosterListController extends Controller
{

    public function is_valid_division($id) {
        $div = \App\SinrenUser::user($id)->first()->division_id;
//        var_dump("{$div} <=> {$id}");
//        exit();
        if ($id == $div)
        {
            return true;
        }
        return false;
    }

    public function check() {
        $id = \Auth::user()->id;
        if (empty(\App\SinrenUser::user($id)->first()))
        {
            return redirect()->route('app::roster::user::show', ['id' => $id]);
        }
        return redirect()->route('app::roster::division::index', ['div' => \App\SinrenUser::user($id)->first()->division_id]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
//        var_dump($this->is_valid_division($id));
        if (!$this->is_valid_division($id))
        {
            \Session::flash('warn_message', "許可されていない部署を閲覧しようとしました。");
            return redirect(route('index'));
        }
        $div   = \App\Division::where('division_id', '=', $id)->first();
        $max   = \App\Roster::max('month_id');
        $db    = \DB::connection('mysql_sinren')
                ->table('sinren_users')
                ->select(\DB::Raw('month_id, COUNT(*) AS cnt'))
                ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
                ->where('is_plan_entry', '=', true)
                ->where('rosters.month_id', '<>', 0)
                ->groupBy('rosters.month_id')
                ->groupBy('sinren_users.division_id')
                ->orderBy('rosters.month_id', 'desc')
                ->take(24)
        ;
        $month = [];
        foreach ($db->get() as $r) {
            $tmp     = strtotime($r->month_id . '01');
            $month[] = [
                'id'      => (int) date('Ym', $tmp),
                'display' => date('Y年n月', $tmp),
                'count'   => $r->cnt,
            ];
        }
        return view('roster.app.divisions.index', ['month' => $month, 'this_month' => $max, 'div' => $div, 'id' => $id]);
    }

    public function show($div, $ym) {
//        echo $div;
//        echo $ym;
        if (!$this->is_valid_division($div))
        {
            \Session::flash('warn_message', "許可されていない部署を閲覧しようとしました。");
            return redirect(route('index'));
        }
        $div_name = \App\Division::where('division_id', '=', $div)->first()->division_name;
        $date     = date('Y年n月', strtotime($ym . '01'));

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
        $users    = \DB::connection('mysql_sinren')
                ->table('sinren_users')
                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->where('sinren_users.division_id', '=', $div)
                ->get()
        ;
//        var_dump($rows);
        $obj      = new \App\Services\Roster\Calendar();
        $cal      = $obj->setId($ym)->makeCalendar();
        $cal      = $obj->convertCalendarToList($cal);
        $calendar = [];
        foreach ($cal as $c) {
            $rows = \App\Roster::where('entered_on', '=', $c['date'])
                    ->join('sinren_db.sinren_users', 'rosters.user_id', '=', 'sinren_users.user_id')
                    ->join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
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
        $d     = date('Y-m-d', strtotime($ym . '01'));
        $prev  = date('Ym', strtotime($d . ' -1 month'));
        $next  = date('Ym', strtotime($d . ' +1 month'));
        $param = [
//            'rows'     => $rows,
            'rows'     => $calendar,
            'users'    => $users,
            'rests'    => $rests,
            'ym'       => $ym,
            'div'      => $div,
            'div_name' => $div_name,
            'date'     => $date,
            'types'    => $types,
            'prev'     => $prev,
            'next'     => $next,
        ];
        return view('roster.app.divisions.list', $param);
    }

}
