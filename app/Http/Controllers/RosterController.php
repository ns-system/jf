<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
//use App\Http\Requests;
use App\Http\Requests\Roster\Plan;
use App\Http\Requests\Roster\Actual;
use App\Http\Controllers\Controller;
//use Validator;
use App\Services\Roster\Calendar;
use App\Services\Traits;

class RosterController extends Controller
{

    use \App\Services\Traits\DateUsable;

    protected $service;

    public function __construct() {
        $this->service = new Calendar();
    }

//    public function home() {
//        return view('roster.app.index', ['count' => 1]);
//    }

    public function show($ym) {
        $input    = \Input::get();
        $position = (!empty($input['position'])) ? $input['position'] : 0;

        $d = \DateTime::createFromFormat('Ymd', $ym . '01');
        if (!$d)
        {
            \Session::flash('warn_message', '日付以外のデータがセットされました。');
            return back();
        }
        $pages = $this->service->setId($ym)->getPages();

        $rosters   = \App\Roster::user()->month($ym)->get();
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
        foreach ($rosters as $r) {
            $times[$r->id] = $this->service->setTimes($r);
        }
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
            'position'  => $position,
        ];
//        \Log::debug($param);
        return view('roster.app.calendar.index', $param);
    }

    public function editPlan($ym, $id, Plan $request) {
        $position = (!empty($request->input()['position'])) ? $request->input()['position'] : 0;
        if (!$this->isDate($ym))
        {

            \Session::flash('warn_message', '日付以外のデータが入力されました。');
            return back();
        }
        try {
            $this->service->editPlan($id, $request);
            \Session::flash('success_message', '予定データを更新しました。');
            return redirect()->route('app::roster::calendar::show', ['ym' => $ym, 'position' => $position]);
        } catch (\Exception $e) {
            \Session::flash('warn_message', $e->getMessage());
            return redirect()->route('app::roster::calendar::show', ['ym' => $ym, 'position' => $position]);
        }
    }

    public function delete($id) {

        try {

            $param = $this->service->delete($id);
            \Session::flash('success_message', "{$param['date']}のデータを削除しました。");
            return redirect(route('app::roster::calendar::show', ['ym' => $param['ym']]));
        } catch (\Exception $e) {

            \Session::flash('warn_message', $e->getMessage());
            return back();
        }
    }

    public function editActual($ym, $id, Actual $request) {
        $position = (!empty($request->input()['position'])) ? $request->input()['position'] : 0;
        try {
            $this->service->editActual($id, $request);

            \Session::flash('success_message', '実績データを更新しました。');
            return redirect()->route('app::roster::calendar::show', ['ym' => $ym, 'position' => $position]);
        } catch (\Exception $e) {
            \Session::flash('warn_message', $e->getMessage());
            return redirect()->route('app::roster::calendar::show', ['ym' => $ym, 'position' => $position]);
        }
    }

}
