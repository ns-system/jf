<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class AcceptController extends Controller
{

//    public function __construct() {
//        $this->middleware('roster.chief');
//    }

    public function show() {
        $input = \Input::all();
        $month = date('Ym');
        if ($input != null)
        {
            $month = date('Ym', strtotime($input['id'] . '01'));
        }

//        var_dump(\Auth::user());
        $divs = \App\ControlDivision::users(\Auth::user()->id)->get(['division_id']);

//        $divs = \App\ControlDivision::where('id', '=', 1);
        foreach ($divs as $div) {
            echo $div->id;
        }
//        foreach($divs as $div){
//            $where 
//            var_dump($div->division_id);
//        }
//exit();
        $obj      = new \App\Providers\AcceptProvider();
        $accpepts = $obj
                ->accepts()
                ->accepts_where($month, $divs)
        ;

        $rows = $accpepts
                ->select(\DB::raw('*, rosters.id as roster_id'))
                ->orderBy('sinren_users.division_id', 'asc')
                ->orderBy('rosters.entered_on', 'asc')
                ->orderBy('rosters.user_id', 'asc')
                ->get()
        ;

        $types      = \App\WorkType::get();
        $work_types = [];
        foreach ($types as $type) {
            $work_types[$type->work_type_id] = [
                'name'  => $type->work_type_name,
                'start' => $type->work_start_time,
                'end'   => $type->work_end_time,
            ];
        }

        $rests = [];
        $tmps  = \App\Rest::get();
        foreach ($tmps as $tmp) {
            $rests[$tmp->id] = $tmp->rest_reason_name;
        }

//        $holidays = \App\Holiday::current($month)->get();
        $pages  = [
            'prev' => date('Ym', strtotime($month . '01' . '-1 month')),
            'next' => date('Ym', strtotime($month . '01' . '+1 month')),
        ];
        $params = [
            'id'    => $month,
            'rows'  => $rows,
            'types' => $work_types,
            'rests' => $rests,
            'pages' => $pages,
        ];
        return view('roster.chief.accept', $params);
    }

    public function editAll() {
        $input     = \Input::all();
        var_dump($input);
        $rules     = [
            'id'     => 'required|exists:mysql_roster.rosters,id',
            'plan'   => 'required',
            'actual' => 'required',
        ];
        $validator = \Validator::make($input, $rules);
        if ($validator->fails())
        {
            return \Redirect::to('/roster/chief/accept')->withErrors($validator)->withInput();
        }

        \DB::connection('mysql_roster')->transaction(function () use ($input) {
            foreach ($input['id'] as $id) {
                $roster = \App\Roster::find($id);
                if (isset($input['plan'][$id]))
                {
                    $this->plan_accept($input['plan'][$id], $roster);
                }
                if (isset($input['actual'][$id]))
                {
                    $this->actual_accept($input['actual'][$id], $roster);
                }
                $roster->reject_reason = $input['reject_reason'][$id];
                $roster->save();
            }
        });
        \Session::flash('flash_message', '承認が完了しました。');
        return \Redirect('/roster/chief/accept');
    }

    public function editUnit() {
        $id = (int) \Request::query()['id'];
        $in = \Input::all();

        var_dump($in);
        var_dump($id);
        $input = [];

        if (isset($in['plan']) && array_key_exists($id, $in['plan']))
        {
            $input['plan'] = $in['plan'][$id];
        }
        if (isset($in['actual']) && array_key_exists($id, $in['actual']))
        {
            $input['actual'] = $in['actual'][$id];
        }
        $input['reject_reason'] = $in['reject_reason'][$id];
        
        var_dump($input);
        \DB::connection('mysql_roster')->transaction(function () use ($input, $id) {
            $roster = \App\Roster::find($id);
            if (isset($input['plan']))
            {
                $this->plan_accept($input['plan'], $roster);
            }
            if (isset($input['actual']))
            {
                $this->actual_accept($input['actual'], $roster);
            }
            $roster->reject_reason = $input['reject_reason'];
            $roster->save();
        });
        \Session::flash('flash_message', '承認が完了しました。');
        return \Redirect('/roster/chief/accept');
    }

    private function plan_accept($flag, $roster) {
        if ($flag == 1)
        {
            $roster->is_plan_accept      = 1;
            $roster->is_plan_reject      = 0;
            $roster->plan_accepted_at    = date('Y-m-d H:i:s');
            $roster->plan_accept_user_id = \Auth::user()->id;
        }
        else
        {
            $roster->is_plan_accept      = 0;
            $roster->is_plan_reject      = 1;
            $roster->plan_rejected_at    = date('Y-m-d H:i:s');
            $roster->plan_reject_user_id = \Auth::user()->id;
        }
    }

    private function actual_accept($flag, $roster) {
        if ($flag == 1)
        {
            $roster->is_actual_accept      = 1;
            $roster->is_actual_reject      = 0;
            $roster->actual_accepted_at    = date('Y-m-d H:i:s');
            $roster->actual_accept_user_id = \Auth::user()->id;
        }
        else
        {
            $roster->is_actual_accept      = 0;
            $roster->is_actual_reject      = 1;
            $roster->actual_rejected_at    = date('Y-m-d H:i:s');
            $roster->actual_reject_user_id = \Auth::user()->id;
        }
    }

}
