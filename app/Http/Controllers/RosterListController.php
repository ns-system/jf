<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
//use App\Http\Requests;
use App\Http\Controllers\Controller;

class RosterListController extends Controller
{

    public function is_valid_division($id) {
        $div = \App\SinrenUser::user()->first()->division_id;
//        var_dump("{$div} <=> {$id}");
//        exit();
        if ($id == $div)
        {
            return true;
        }
        return false;
    }

    public function check() {
        if (empty(\App\SinrenUser::user()))
        {
            return redirect(route('app::roster::user::show'));
        }
        return redirect(route('app::roster::division::index', ['div' => \App\SinrenUser::user()->first()->division_id]));
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
                ->join('roster_data_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
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

        $rows  = \DB::connection('mysql_sinren')
                ->table('sinren_users')
//                ->select(\DB::Raw('month_id, COUNT(*) AS cnt'))
                ->join('roster_data_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->join('roster_data_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->orderBy('rosters.user_id', 'asc')
                ->orderBy('rosters.entered_on', 'asc')
                ->where('sinren_users.division_id', '=', $div)
                ->where('rosters.month_id', '=', $ym)
                ->where('is_plan_entry', '=', true)
                ->get()
        ;
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
        $calendar = $obj->setId($ym)->makeCalendar();
        $tmp      = [];
        foreach ($calendar as $key => $c) {
            if ($c['day'] == 0)
            {
                continue;
            }
            $tmp[$key] = $c;
            foreach ($rows as $r) {
                if ($r->entered_on == $c['date'])
                {
                    $tmp[$key]['data'][$r->user_id] = $r;
                }
            }
        }
//        var_dump($tmp);
        $param = [
            'rows'     => $tmp,
            'users'    => $users,
            'rests'    => $rests,
            'ym'       => $ym,
            'div'      => $div,
            'div_name' => $div_name,
            'date'     => $date,
        ];
        return view('roster.app.divisions.list', $param);
    }

}
