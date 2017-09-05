<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class RosterCsvExportController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
//        var_dump('test');
        $months  = \App\Roster::groupBy('month_id')
                ->select(\DB::raw('COUNT(*) as cnt, month_id'))
                ->orderBy('month_id', 'desc')
                ->where('month_id', '<>', 0)
                ->take(24)
                ->get()
        ;
//        foreach ($months as $m) {
//            var_dump($m);
//        }
        $current = date('Ym');
        return view('roster.admin.csv.index', ['months' => $months, 'current' => $current,]);
//        var_dump($months);
    }

    public function show($month) {
        var_dump($month);
        $rosters   = \App\Roster::where('month_id', '=', $month)
                ->join('sinren_data_db.sinren_users', 'rosters.user_id', '=', 'sinren_users.user_id')
                ->join('sinren_data_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->join('laravel_db.users', 'rosters.user_id', '=', 'users.id')
                ->select(\DB::raw('*, rosters.id AS key_id'))
                ->orderBy('sinren_users.division_id', 'asc')
                ->orderBy('sinren_users.user_id', 'asc')
                ->paginate(50)
        ;
        $tmp_types = \App\WorkType::orderBy('work_type_id')->get();
        $tmp_rests = \App\Rest::orderBy('rest_reason_id')->get();
        $types     = [];
        $rests     = [];
        foreach ($tmp_types as $t) {
            $types[$t->work_type_id] = [
                'name' => $t->work_type_name,
                'time' => null,
            ];
            if ($t->work_start_time != $t->work_end_time)
            {
                $types[$t->work_type_id]['time'] = '（ ' . date('G:i', strtotime($t->work_start_time)) . ' ～ ' . date('G:i', strtotime($t->work_end_time)) . ' ）';
            }
        }
        var_dump($types);
//        exit();
        foreach ($tmp_rests as $r) {
            $rests[$r->rest_reason_id] = $r->rest_reason_name;
        }
//        $obj      = new \App\Services\Roster\Calendar();
//        $calendar = $obj->setId($month)->makeCalendar();
        return view('roster.admin.csv.list', ['rosters' => $rosters, 'month' => $month, 'types' => $types, 'rests' => $rests,]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
