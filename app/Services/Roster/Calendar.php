<?php

namespace App\Services\Roster;

class Calendar
{

    protected $id;
    protected $work_types;
    protected $user;

    public function __construct() {
        $types      = [];
        $tmp        = \App\WorkType::orderBy('work_type_id')->get();
        $this->user = \App\RosterUser::user()->first();
        foreach ($tmp as $t) {
            $types[$t->id] = $t;
        }
        $this->work_types = $types;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setTimes(/* $type, */$row) {
        $start           = null;
        $end             = null;
        $plan_start      = null;
        $plan_end        = null;
        $actual_start    = null;
        $actual_end      = null;
        $is_plan_entry   = false;
        $is_actual_entry = false;
        $plan_type       = null;
        $actual_type     = null;

        $types = $this->work_types;

        if (isset($row->is_plan_entry))
        {
            $is_plan_entry = $row->is_plan_entry;
        }

        if (isset($row->is_actual_entry))
        {
            $is_actual_entry = $row->is_actual_entry;
        }

        if (!empty($this->user->work_type_id))
        {
            $plan_type   = $types[$this->user->work_type_id];
            $actual_type = $types[$this->user->work_type_id];
        }

        if (!empty($row->plan_work_type_id))
        {
            $plan_type = $types[$row->plan_work_type_id];
        }

        if (!empty($row->actual_work_type_id))
        {
            $actual_type = $types[$row->actual_work_type_id];
        }

        if (!empty($row->plan_overtime_start_time) && $row->plan_overtime_start_time != '0000-00-00 00:00:00')
        {
            $plan_start = $row->plan_overtime_start_time;
            $plan_end   = $row->plan_overtime_end_time;
        }
        else
        {
            $plan_start = $plan_type->work_start_time;
            $plan_end   = $plan_type->work_end_time;
        }

        if (!empty($row->actual_overtime_start_time) && $row->actual_overtime_start_time != '0000-00-00 00:00:00')
        {
            $actual_start = $row->actual_overtime_start_time;
            $actual_end   = $row->actual_overtime_end_time;
        }
        else
        {
            $actual_start = $actual_type->work_start_time;
            $actual_end   = $actual_type->work_end_time;
        }


        $plan_start_hour   = (int) date('H', strtotime($plan_start));
        $plan_start_time   = (int) date('i', strtotime($plan_start));
        $plan_end_hour     = (int) date('H', strtotime($plan_end));
        $plan_end_time     = (int) date('i', strtotime($plan_end));
        $actual_start_hour = (int) date('H', strtotime($actual_start));
        $actual_start_time = (int) date('i', strtotime($actual_start));
        $actual_end_hour   = (int) date('H', strtotime($actual_end));
        $actual_end_time   = (int) date('i', strtotime($actual_end));

        $times = [
            'is_plan_entry'     => $is_plan_entry,
            'is_actual_entry'   => $is_actual_entry,
            'plan_start_hour'   => $plan_start_hour,
            'plan_start_time'   => $plan_start_time,
            'plan_end_hour'     => $plan_end_hour,
            'plan_end_time'     => $plan_end_time,
            'actual_start_hour' => $actual_start_hour,
            'actual_start_time' => $actual_start_time,
            'actual_end_hour'   => $actual_end_hour,
            'actual_end_time'   => $actual_end_time,
        ];
//        var_dump($times);
//        exit();
        return $times;
    }

    public function editPlan($id, $request) {
//        $roster     = \App\Roster::user()->entered_on($request['entered_on']);
        $roster = \App\Roster::find($id);
        if (empty($roster))
        {
            throw new \Exception('ユーザーが見つかりませんでした。');
        }
        $start_time = null;
        $end_time   = null;
        if (empty($request['plan_rest_reason_id']))
        {
            $start_time = date('H:i:s', strtotime($request['plan_start_hour'] . ":" . $request['plan_start_time'] . ":00"));
            $end_time   = date('H:i:s', strtotime($request['plan_end_hour'] . ":" . $request['plan_end_time'] . ":00"));
        }
//        if (!$roster->exists())
//        {
//            $roster             = new \App\Roster();
//            $roster->entered_on = $request['entered_on'];
//            $roster->month_id   = (int) $request['month_id'];
//        }
//        else
//        {
//            $roster = $roster->first();
//        }
        $roster->user_id                  = \Auth::user()->id;
        $roster->is_plan_entry            = (int) true;
        $roster->is_plan_reject           = (int) false;
        $roster->plan_overtime_reason     = $request['plan_overtime_reason'];
        $roster->plan_overtime_start_time = $start_time;
        $roster->plan_overtime_end_time   = $end_time;
        $roster->plan_entered_at          = date('Y-m-d H:i:s');
        $roster->save();
    }

    public function getPages() {
        $pages = [
            'prev' => date('Ym', strtotime($this->id . '01' . '-1 month')),
            'next' => date('Ym', strtotime($this->id . '01' . '+1 month')),
        ];
        return $pages;
    }

    public function editActual($id, $request) {
        $roster = \App\Roster::find($id);
        if (empty($roster))
        {
            throw new \Exception('予定データが入力されていないようです。');
        }
        $start_time = null;
        $end_time   = null;
        if (empty($request['actual_rest_reason_id']))
        {
            $start_time                         = date('H:i:s', strtotime($request['actual_start_hour'] . ":" . $request['actual_start_time'] . ":00"));
            $end_time                           = date('H:i:s', strtotime($request['actual_end_hour'] . ":" . $request['actual_end_time'] . ":00"));
            $roster->actual_overtime_start_time = $start_time;
            $roster->actual_overtime_end_time   = $end_time;
        }
        else
        {
            $roster->actual_rest_reason_id = $request['actual_rest_reason_id'];
        }
        $roster->user_id                = \Auth::user()->id;
        $roster->is_actual_entry        = (int) true;
        $roster->is_actual_reject       = (int) false;
        $roster->actual_work_type_id    = $request['actual_work_type_id'];
        $roster->actual_overtime_reason = $request['actual_overtime_reason'];
        $roster->actual_entered_at      = date('Y-m-d H:i:s');
        $roster->save();
//        exit();
    }

    public function delete($id) {
        $roster = \App\Roster::find($id);
        if (!$roster->exists())
        {
            throw new \Exception('予定データが見つかりませんでした。');
//            \Session::flash('warn_message', '予定データが見つかりませんでした。');
//            return back();
        }
        
        if($roster->is_plan_accept || $roster->is_actual_accept){
            throw new \Exception('データはすでに承認されているため、削除できません。');
            
        }
        $ym   = $roster->month_id;
        $date = date('n月j日', strtotime($roster->entered_on));

        $roster->is_plan_entry              = (int) false;
        $roster->plan_rest_reason_id        = null;
        $roster->plan_overtime_reason       = '';
        $roster->plan_overtime_start_time   = null;
        $roster->plan_overtime_end_time     = null;
        $roster->is_actual_entry            = (int) false;
        $roster->actual_rest_reason_id      = null;
        $roster->actual_overtime_reason     = '';
        $roster->actual_overtime_start_time = null;
        $roster->actual_overtime_end_time   = null;
        $roster->save();

        return [
            'ym'   => $ym,
            'date' => $date,
        ];
    }

    private function getDate($format = 'Y-m-d', $day = '01') {
        $day = "" . sprintf('%02d', (int) $day);
        return date($format, strtotime($this->id . $day));
    }

    private function getWeekName($week) {
        switch ($week) {
            case 1:
                return '月';
            case 2:
                return '火';
            case 3:
                return '水';
            case 4:
                return '木';
            case 5:
                return '金';
            case 6:
                return '土';
            case 0:
                return '日';
        }
    }

    public function getHoliday() {
        $f_day = $this->getDate('Y-m-d');
        $l_day = $this->getDate('Y-m-t');

        $holidays = \App\Holiday::where('holiday', '>=', $f_day)
                ->where('holiday', '<=', $l_day)
                ->get()
        ;
        return $holidays;
    }

    public function makeCalendar($rosters = null) {
        $f_day  = (int) $this->getDate('d');
        $l_day  = (int) $this->getDate('t');
        $f_week = $this->getDate('w');

        $cal = [];
        for ($i = 1; $i <= $f_week; $i++) {
            $cal[$i] = [
                'week'         => $i - 1,
                'week_name'    => '',
                'holiday'      => 0,
                'holiday_name' => '',
                'day'          => 0,
                'date'         => 0,
                'data'         => [],
            ];
        }

        for ($i = $f_day; $i <= $l_day; $i++) {
            $tmp_week          = (int) $this->getDate('w', sprintf('%02d', $i));
            $cal[$i + $f_week] = [
                'week'         => $tmp_week,
                'week_name'    => $this->getWeekName($tmp_week),
                'holiday'      => 0,
                'holiday_name' => '',
                'day'          => $i,
                'date'         => $this->getDate('Y-m-d', $i),
                'data'         => [],
            ];
        }

        // setHoliday
        $holidays = $this->getHoliday();
        foreach ($holidays as $holiday) {
            $pointer                                 = (int) date('d', strtotime($holiday->holiday));
            $cal[$pointer + $f_week]['holiday']      = 1;
            $cal[$pointer + $f_week]['holiday_name'] = $holiday->holiday_name;
        }
        if ($rosters !== null)
        {
            foreach ($rosters as $roster) {
                $key                         = (int) date('d', strtotime($roster->entered_on));
                $cal[$key + $f_week]['data'] = $roster;
            }
        }

//        var_dump($cal);
//        exit();

        return $cal;
    }

    public function convertCalendarToList($calendar) {
        $list = [];
        foreach ($calendar as $c) {
            if ($c['date'] == 0)
            {
                continue;
            }
            $list[] = $c;
        }
        return $list;
    }

    public function makeList($div) {
//        echo $this->month_id;
        $rosters = $this->makeRoster($div);
        $r       = $rosters->get();

        $users = $rosters->groupBy('users.id')->get(['users.id', 'users.name']);
//        var_dump($users);
//        exit();

        $f_day = (int) $this->getDate('d');
        $l_day = (int) $this->getDate('t');

        $lists = [];
        for ($i = $f_day; $i <= $l_day; $i++) {
            $date     = $this->getDate('Y-m-d', $i);
            $tmp_week = $this->getDate('w', $i);
            $tmp_list = [
                'date'         => $date,
                'week'         => $tmp_week,
                'week_name'    => $this->getWeekName($tmp_week),
                'holiday'      => 0,
                'holiday_name' => '',
            ];

            $tmp_list_users = [];
            foreach ($users as $user) {
                $tmp_list_users[$user->id] = [
                    'name'   => $user->name,
                    'roster' => null,
                ];
            }
            $tmp_list['users'] = $tmp_list_users;
            $lists[$i]         = $tmp_list;
        }
//        var_dump($lists);

        $holidays = $this->getHoliday();
        foreach ($holidays as $holiday) {
            $pointer = (int) date('d', strtotime($holiday->holiday));

            $lists[$pointer]['holiday']      = 1;
            $lists[$pointer]['holiday_name'] = $holiday->holiday_name;
        }

//        var_dump($lists);
//
//        exit();

        foreach ($r as $roster) {
//            var_dump($roster->id);
            $i = (int) date('j', strtotime($roster->entered_on));

            $lists[$i]['users'][$roster->user_id]['roster'] = $roster;
//            var_dump($i . '-'.$roster->user_id);
        }
//        var_dump($lists);
//        var_dump($lists);
//        exit();
//        exit();
        return $lists;
    }

}
