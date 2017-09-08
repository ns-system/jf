<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
//use App\Http\Requests;
use App\Http\Requests\Roster\Plan;
use App\Http\Requests\Roster\Actual;
use App\Http\Controllers\Controller;
//use Validator;
use App\Services\Roster\Calendar;

class RosterController extends Controller
{

    protected $service;

    public function __construct() {
        $this->service = new Calendar();
    }

    public function home() {
        return view('roster.app.index', ['count' => 1]);
    }

    public function show($ym) {
        $d = \DateTime::createFromFormat('Ymd', $ym . '01');
        if (!$d)
        {
            \Session::flash('warn_message', '日付以外のデータがセットされました。');
            return back();
        }
        $pages = $this->service->setId($ym)->getPages();


        $rosters   = \App\Roster::user()->month($ym)->get();
//        $rosters = \App\Roster::join('','','=','')
//                ->get();
//        var_dump($rosters);
        $tmp_types = \App\WorkType::get();
        $tmp_rests = \App\Rest::get();
        $types     = [];
        $rests     = [];
        foreach ($tmp_types as $t) {
            $types[$t->work_type_id] = [
                'name'       => $t->work_type_name,
                'time'       => null,
                'start_hour' => null,
                'start_time' => null,
                'end_hour'   => null,
                'end_time'   => null,
            ];
            if ($t->work_start_time != $t->work_end_time)
            {
                $types[$t->work_type_id]['time']       = '（ ' . date('G:i', strtotime($t->work_start_time)) . ' ～ ' . date('G:i', strtotime($t->work_end_time)) . ' ）';
                $types[$t->work_type_id]['start_hour'] = (int) date('G', strtotime($t->work_start_time));
                $types[$t->work_type_id]['start_time'] = (int) date('i', strtotime($t->work_start_time));
                $types[$t->work_type_id]['end_hour']   = (int) date('G', strtotime($t->work_end_time));
                $types[$t->work_type_id]['end_time']   = (int) date('i', strtotime($t->work_end_time));
            }
        }

        $times = [];
        foreach ($rosters as $i => $r) {
//            if($i == 4){
            $times[$r->id] = $this->service->setTimes($r);
//                exit();
//            }
        }
//        var_dump($times);        exit();
        foreach ($tmp_rests as $r) {
            $rests[$r->rest_reason_id] = $r->rest_reason_name;
        }


        $calendar = $this->service->makeCalendar($rosters);
        $param    = [
            'ym'        => $ym,
            'calendars' => $calendar,
            'types'     => $types,
            'times'     => $times,
            'rests'     => $rests,
            'prev'      => $pages['prev'],
            'next'      => $pages['next'],
        ];
        return view('roster.app.calendar.index', $param);
    }

    public function editPlan($ym, $id, Plan $request) {
        $this->service->editPlan($id, $request);
        \Session::flash('flash_message', '予定データを更新しました。');
        return redirect(route('app::roster::calendar::show', ['ym' => $ym]));
    }

    public function delete($id) {

        try {
            $param = $this->service->delete($id);
            \Session::flash('flash_message', "{$param['date']}のデータを削除しました。");
            return redirect(route('app::roster::calendar::show', ['ym' => $param['ym']]));
        } catch (\Exception $e) {
//            echo $e->getTraceAsString();
            \Session::flash('warn_message', $e->getMessage());
            return back();
        }
    }

    public function editActual($ym, $id, Actual $request) {
        try {
            $this->service->editActual($id, $request);
//            exit();
            \Session::flash('flash_message', '実績データを更新しました。');
            return redirect(route('app::roster::calendar::show', ['ym' => $ym]));
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
            \Session::flash('warn_message', $e->getMessage());
            return redirect(route('app::roster::calendar::show', ['ym' => $ym]));
//            return back();
        }
    }

}
