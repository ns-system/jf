<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
//use App\Http\Requests;
use App\Http\Requests\Roster\PlanRequest;
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
        $pages     = $this->service->setId($ym)->getPages();
        $rosters   = \App\Roster::user()->month($ym)->get();
//        var_dump($rosters);
        $tmp_types = \App\WorkType::get();
        $tmp_rests = \App\Rest::get();
        $types     = [];
        $rests     = [];
        foreach ($tmp_types as $t) {
            $types[$t->work_type_id] = [
                'name' => $t->work_type_name,
                'time' => null,
            ];
            if ($t->work_start_time != $t->work_end_time)
            {
                $types[$t->work_type_id]['time'] = '（ ' . date('G:i', strtotime($t->work_start_time)) . ' ～ ' . date('G:i', strtotime($t->work_start_time)) . ' ）';
            }
        }
        foreach ($tmp_rests as $r) {
            $rests[$r->rest_reason_id] = $r->rest_reason_name;
        }


        $calendar = $this->service->makeCalendar($rosters);
        $param    = [
            'ym'        => $ym,
            'calendars' => $calendar,
            'types'     => $types,
            'rests'     => $rests,
            'prev'      => $pages['prev'],
            'next'      => $pages['next'],
        ];
        return view('roster.app.calendar.index', $param);
    }

    public function form($id, $day) {
        $d = \DateTime::createFromFormat('Ymd', $id . sprintf('%02d', (int) $day));
        if (!$d)
        {
            \Session::flash('warn_message', '日付以外のデータがセットされました。');
            return back();
        }
        $date  = $d->format('Ymd');
        $types = \App\WorkType::orderBy('work_type_id', 'asc')->get();
        $rests = \App\Rest::orderBy('rest_reason_id', 'asc')->get();
        $row   = \App\Roster::user()->where('entered_on', '=', $date)->first();
//        var_dump($row);
//        $type  = \App\WorkType::where('work_type_id', '=', \App\RosterUser::user()->first()->work_type_id);

        $times = $this->service->setTimes($row);
        $param = [
            'id'                => $id,
            'row'               => $row,
            'date'              => $date,
            'types'             => $types,
            'rests'             => $rests,
            'plan_start_hour'   => $times['plan_start_hour'],
            'plan_start_time'   => $times['plan_start_time'],
            'plan_end_hour'     => $times['plan_end_hour'],
            'plan_end_time'     => $times['plan_end_time'],
            'actual_start_hour' => $times['actual_start_hour'],
            'actual_start_time' => $times['actual_start_time'],
            'actual_end_hour'   => $times['actual_end_hour'],
            'actual_end_time'   => $times['actual_end_time'],
        ];
        return view('roster.app.calendar.plan.index', $param);
    }

    public function editPlan(PlanRequest $request) {
        $this->service->editPlan($request);
        \Session::flash('flash_message', date('n月j日', strtotime($request['entered_on'])) . 'の予定データを更新しました。');
        return redirect(route('app::roster::calendar::show', ['ym' => $request['month_id']]));
    }

    public function delete($id) {

        try {
            $param = $this->service->delete($id);
            \Session::flash('flash_message', "{$param['date']}のデータを削除しました。");
            return redirect(route('app::roster::calendar::show', ['ym' => $param['ym']]));
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
            \Session::flash('warn_message', $e->getMessage());
            return back();
        }
    }

    public function editActual(Actual $request) {
        try {
            $this->service->editActual($request);
            \Session::flash('flash_message', date('n月j日', strtotime($request['entered_on'])) . 'の実績データを更新しました。');
            return redirect(route('app::roster::calendar::show', ['ym' => $request['month_id']]));
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
            \Session::flash('warn_message', $e->getMessage());
            return back();
        }
    }

}
