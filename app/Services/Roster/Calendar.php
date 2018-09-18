<?php

namespace App\Services\Roster;

class Calendar
{

    protected $id;
    protected $work_types;
    protected $user;

    public function __construct()
    {
        $types      = [];
        $tmp        = \App\WorkType::orderBy('work_type_id')->get();
        $this->user = \App\RosterUser::user()->first();
        foreach ($tmp as $t) {
            $types[$t->id] = $t;
        }
        $this->work_types = $types;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setTimes(/* $type, */
        $row)
    {
//        $plan_type   = null;
//        $actual_type = null;
//        $work_type_id = (!empty($this->user->work_type_id)) ? $this->user->work_type_id : null;
//        dd($this->work_types);

        $types  = $this->work_types;
        $ttypes = [];
        foreach ($types as $t) {
            $ttypes[$t->work_type_id] = $t;
        }

        $tmp_type = (!empty($this->user->work_type_id)) ? $ttypes[$this->user->work_type_id] : null;

        $is_plan_entry       = (isset($row->is_plan_entry)) ? $row->is_plan_entry : false;
        $is_actual_entry     = (isset($row->is_actual_entry)) ? $row->is_actual_entry : false;
        $plan_type           = (!empty($row->plan_work_type_id)) ? $ttypes[$row->plan_work_type_id] : $tmp_type;
        $actual_type         = (!empty($row->actual_work_type_id)) ? $ttypes[$row->actual_work_type_id] : $tmp_type;
        $is_plan_different   = ((/* 空じゃない */
                                    !empty($row->plan_overtime_start_time) && !empty($row->plan_overtime_end_time)) && (/* 違ってる */
                                    $row->plan_overtime_start_time != $row->plan_overtime_end_time)) ? true : false;
        $is_actual_different = ((/* 空じゃない */
                                    !empty($row->actual_overtime_start_time) && !empty($row->actual_overtime_end_time)) && (/* 違ってる */
                                    $row->actual_overtime_start_time != $row->actual_overtime_end_time)) ? true : false;

        $plan_start   = ($is_plan_different) ? $row->plan_overtime_start_time : $plan_type->work_start_time;
        $plan_end     = ($is_plan_different) ? $row->plan_overtime_end_time : $plan_type->work_end_time;
        $actual_start = ($is_actual_different) ? $row->actual_overtime_start_time : $actual_type->work_start_time;
        $actual_end   = ($is_actual_different) ? $row->actual_overtime_end_time : $actual_type->work_end_time;

//        $plan_start_hour   = null;
//        $plan_start_time   = null;
//        $plan_end_hour     = null;
//        $plan_end_time     = null;
        $actual_start_hour = null;
        $actual_start_time = null;
        $actual_end_hour   = null;
        $actual_end_time   = null;

        $plan_start_hour = (int) date('H', strtotime($plan_start));
        $plan_start_time = (int) date('i', strtotime($plan_start));
        $plan_end_hour   = (int) date('H', strtotime($plan_end));
        $plan_end_time   = (int) date('i', strtotime($plan_end));

        if (!empty($actual_start) || !empty($actual_end)) {
            $actual_start_hour = (int) date('H', strtotime($actual_start));
            $actual_start_time = (int) date('i', strtotime($actual_start));
            $actual_end_hour   = (int) date('H', strtotime($actual_end));
            $actual_end_time   = (int) date('i', strtotime($actual_end));
        }

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
        return $times;
    }

    public function editPlan($id, $request)
    {
//        dd($request->input());
        $roster = \App\Roster::findOrFail($id);

        $start_time = null;
        $end_time   = null;
        if (empty($request['plan_rest_reason_id'])) {
            $start_time = date('H:i:s', strtotime($request['plan_start_hour'] . ":" . $request['plan_start_time'] . ":00"));
            $end_time   = date('H:i:s', strtotime($request['plan_end_hour'] . ":" . $request['plan_end_time'] . ":00"));
            if ($start_time > $end_time) {
                throw new \Exception("開始時間 < 終了時間となるように入力してください。");
            }
            // 勤務形態より少しでもオーバーしたら残業理由を強制的に入力させる
            if (!$this->inTime($roster->plan_work_type_id, $start_time, $end_time) && empty($request['plan_overtime_reason'])) {
                throw new \Exception("予定勤務時間を超過する場合、必ず理由を入力してください。");
            }
        }
        $roster->user_id                  = \Auth::user()->id;
        $roster->is_plan_entry            = (int) true;
        $roster->is_plan_reject           = (int) false;
        $roster->plan_overtime_reason     = $request['plan_overtime_reason'];
        $roster->plan_overtime_start_time = $start_time;
        $roster->plan_overtime_end_time   = $end_time;
        $roster->plan_entered_at          = date('Y-m-d H:i:s');
        $roster->save();
    }

    private function inTime($work_type_id, $start_time, $end_time)
    {
        $work_type = (!empty($work_type_id)) ? \App\WorkType::workTypeId($work_type_id)->first() : null;
//        dd($work_type);
        if (empty($work_type) || (empty($work_type->work_start_time) && $work_type->work_end_time)) {
            return true;
        }
        return (($work_type->work_start_time == $start_time) && ($work_type->work_end_time == $end_time)) ? true : false;
    }

    public function getPages()
    {
        $pages = [
            'prev' => date('Ym', strtotime($this->id . '01' . '-1 month')),
            'next' => date('Ym', strtotime($this->id . '01' . '+1 month')),
        ];
        return $pages;
    }

    public function editActual($id, $request)
    {
        // 遅刻・早退は打刻しないといけないため無理やり機能実装することにする
        $rest   = (empty($request['actual_rest_reason_id'])) ? null : \App\Rest::where('rest_reason_id', $request['actual_rest_reason_id'])->first();
        $roster = \App\Roster::findOrFail($id);

        $start_time = null;
        $end_time   = null;
        $is_rest    = (!empty($rest)) ? true : false;
//        $is_short_time = false;
        $is_short_time = (!empty($rest) && ($rest->rest_reason_name === '遅刻' || $rest->rest_reason_name === '早退')) ? true : false;

        if (empty($rest) || !empty($rest) && ($rest->rest_reason_name === '遅刻' || $rest->rest_reason_name === '早退')) {
            $start_time = date('H:i:s', strtotime($request['actual_start_hour'] . ":" . $request['actual_start_time'] . ":00"));
            $end_time   = date('H:i:s', strtotime($request['actual_end_hour'] . ":" . $request['actual_end_time'] . ":00"));
            if ($start_time > $end_time) {
                throw new \Exception("開始時間 < 終了時間となるように入力してください。");
            }
            // 勤務形態より少しでもオーバーしたら残業理由を強制的に入力させる
            $work_type = (empty($request['actual_work_type_id'])) ? $roster->plan_work_type_id : $request['actual_work_type_id'];
            if (!$this->inTime($work_type, $start_time, $end_time) && empty($request['actual_overtime_reason'])) {
                throw new \Exception("勤務時間を超過する場合、必ず理由を入力してください。");
            }
        }
        $roster->user_id          = \Auth::user()->id;
        $roster->is_actual_entry  = (int) true;
        $roster->is_actual_reject = (int) false;
//        $roster->actual_work_type_id        = (!$is_rest || $is_short_time) ? $request['actual_work_type_id'] : 0;
        $roster->actual_work_type_id        = $request['actual_work_type_id'];
        $roster->actual_rest_reason_id      = ($is_rest) ? $request['actual_rest_reason_id'] : 0;
        $roster->actual_overtime_reason     = $request['actual_overtime_reason'];
        $roster->actual_entered_at          = date('Y-m-d H:i:s');
        $roster->actual_overtime_start_time = $start_time;
        $roster->actual_overtime_end_time   = $end_time;
        $roster->save();
        // if (env('APP_DEBUG')) {
        //     \Log::info('actual edit :');
        //     \Log::info(['start_time' => $start_time, 'end_time' => $end_time]);
        //     \Log::info(['is_rest' => $is_rest, 'is_short_time' => $is_short_time, $roster->toArray(),]);
        // }
    }

    public function delete($id)
    {
        $roster = \App\Roster::findOrFail($id);

        if ($roster->is_plan_accept || $roster->is_actual_accept) {
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

    private function getDate($format = 'Y-m-d', $day = '01')
    {
        $day = "" . sprintf('%02d', (int) $day);
        return date($format, strtotime($this->id . $day));
    }

    private function getWeekName($week)
    {
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

    public function getHoliday()
    {
        $f_day = $this->getDate('Y-m-d');
        $l_day = $this->getDate('Y-m-t');

        $holidays = \App\Holiday::where('holiday', '>=', $f_day)
            ->where('holiday', '<=', $l_day)
            ->get();
        return $holidays;
    }

    public function makeCalendar($rosters = null)
    {
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
        if ($rosters !== null) {
            foreach ($rosters as $roster) {
                $key                         = (int) date('d', strtotime($roster->entered_on));
                $cal[$key + $f_week]['data'] = $roster;
            }
        }
        return $cal;
    }

    public function convertCalendarToList($calendar)
    {
        $list = [];
        foreach ($calendar as $c) {
            if (empty($c['date'])) {
                continue;
            }
            $list[] = $c;
        }
        return $list;
    }

}
