<?php

namespace App\Services\Roster;

//use App\Services\CsvService;
use App\Services\Traits\CsvUsable;

class CsvExport
{

    use CsvUsable;

    protected $rosters;
    protected $ym;
    protected $plan_rows;
    protected $actual_rows;

    public function setMonth($ym) {
        if (mb_strlen($ym) === 6 && is_numeric($ym))
        {
            $this->ym = $ym;
        }
        return $this;
    }

    public function getRosters() {
        $ym            = $this->ym;
        $time_format   = '%k:%i';
        $rosters       = \App\Roster::where('month_id', '=', $ym)
                ->leftJoin('sinren_db.sinren_users', 'rosters.user_id', '=', 'sinren_users.user_id')
                ->leftJoin('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->leftJoin('laravel_db.users as USER', 'rosters.user_id', '=', 'USER.id')
                ->leftJoin('roster_db.roster_users', 'rosters.user_id', '=', 'roster_users.user_id')
                //
                ->leftJoin('roster_db.work_types as PWORK', 'rosters.plan_work_type_id', '=', 'PWORK.work_type_id')
                ->leftJoin('roster_db.work_types as AWORK', 'rosters.actual_work_type_id', '=', 'AWORK.work_type_id')
                ->leftJoin('roster_db.rest_reasons as PREST', 'rosters.plan_rest_reason_id', '=', 'PREST.rest_reason_id')
                ->leftJoin('roster_db.rest_reasons as AREST', 'rosters.actual_rest_reason_id', '=', 'AREST.rest_reason_id')
                //
                ->leftJoin('laravel_db.users as PACCE', 'rosters.plan_accept_user_id', '=', 'PACCE.id')
                ->leftJoin('laravel_db.users as PREJE', 'rosters.plan_reject_user_id', '=', 'PREJE.id')
                ->leftJoin('laravel_db.users as AACCE', 'rosters.actual_accept_user_id', '=', 'AACCE.id')
                ->leftJoin('laravel_db.users as AREJE', 'rosters.actual_reject_user_id', '=', 'AREJE.id')
                //
                ->select(\DB::raw('*, rosters.id AS key_id'))
                ->addSelect(\DB::raw("DATE_FORMAT(rosters.plan_overtime_start_time, '{$time_format}') as plan_overtime_start_time"))
                ->addSelect(\DB::raw("DATE_FORMAT(rosters.plan_overtime_end_time, '{$time_format}') as plan_overtime_end_time"))
                ->addSelect(\DB::raw("DATE_FORMAT(rosters.actual_overtime_start_time, '{$time_format}') as actual_overtime_start_time"))
                ->addSelect(\DB::raw("DATE_FORMAT(rosters.actual_overtime_end_time, '{$time_format}') as actual_overtime_end_time"))
                ->addSelect(\DB::raw("rosters.updated_at as updated_at"))
                ->addSelect(\DB::raw('USER.last_name as last_name, USER.first_name as first_name'))
                ->addSelect(\DB::raw('CONCAT(USER.last_name, " ", USER.first_name) as user_name'))
                // work_types整形
                ->addSelect(\DB::raw('PWORK.work_type_name as plan_work_type_name, AWORK.work_type_name as actual_work_type_name'))
                ->addSelect(\DB::raw("DATE_FORMAT(PWORK.work_start_time,'{$time_format}') as plan_work_start_time, DATE_FORMAT(PWORK.work_end_time,'{$time_format}') as plan_work_end_time"))
                ->addSelect(\DB::raw("DATE_FORMAT(AWORK.work_start_time,'{$time_format}') as actual_work_start_time, DATE_FORMAT(AWORK.work_end_time,'{$time_format}') as actual_work_end_time"))
//                ->addSelect(\DB::raw('AWORK.work_type_name as actual_work_type_name, AWORK.work_start_time as actual_work_start_time, AWORK.work_end_time as actual_work_end_time'))
                ->addSelect(\DB::raw('PREST.rest_reason_name as plan_rest_reason_name'))
                ->addSelect(\DB::raw('AREST.rest_reason_name as actal_rest_reason_name'))
                ->addSelect(\DB::raw('CONCAT(PACCE.last_name, " ", PACCE.first_name) as plan_accept_user_name'))
                ->addSelect(\DB::raw('CONCAT(PREJE.last_name, " ", PREJE.first_name) as plan_reject_user_name'))
                ->addSelect(\DB::raw('CONCAT(AACCE.last_name, " ", AACCE.first_name) as actual_accept_user_name'))
                ->addSelect(\DB::raw('CONCAT(AREJE.last_name, " ", AREJE.first_name) as actual_reject_user_name'))
                ->orderBy('sinren_users.division_id', 'asc')
                ->orderBy('rosters.entered_on', 'asc')
                ->orderBy('sinren_users.user_id', 'asc')
        ;
        $this->rosters = $rosters;
        return $rosters;
    }

    public function getCalendar() {
        $obj      = new \App\Services\Roster\Calendar();
        $c        = $obj->setId($this->ym)->makeCalendar();
        $cal      = $obj->convertCalendarToList($c);
        $calendar = [];
        foreach ($cal as $c) {
            $calendar[$c['date']] = $c;
        }
        return $calendar;
    }

    public function update($input) {
        \DB::connection()->transaction(function() use($input) {
            $r                             = \App\Roster::findOrFail($input['id']);
            $r->plan_work_type_id          = $input['plan_work_type_id'];
            $r->actual_work_type_id        = $input['actual_work_type_id'];
            $r->actual_rest_reason_id      = $input['actual_rest_reason_id'];
            $r->plan_rest_reason_id        = $input['plan_rest_reason_id'];
            $r->plan_overtime_reason       = $input['plan_overtime_reason'];
            $r->actual_overtime_reason     = $input['actual_overtime_reason'];
            $r->plan_overtime_start_time   = (empty($input['plan_overtime_start_time'])) ? null : $input['plan_overtime_start_time'];
            $r->plan_overtime_end_time     = (empty($input['plan_overtime_end_time'])) ? null : $input['plan_overtime_end_time'];
            $r->actual_overtime_start_time = (empty($input['actual_overtime_start_time'])) ? null : $input['actual_overtime_start_time'];
            $r->actual_overtime_end_time   = (empty($input['actual_overtime_end_time'])) ? null : $input['actual_overtime_end_time'];

//            if (!empty($input['plan_overtime_start_time']))
//            {
//                $r->plan_overtime_start_time = $input['plan_overtime_start_time'];
//            }
//            else
//            {
//                $r->plan_overtime_start_time = null;
//            }
//            if (!empty($input['plan_overtime_end_time']))
//            {
//                $r->plan_overtime_end_time = $input['plan_overtime_end_time'];
//            }
//            else
//            {
//                $r->plan_overtime_end_time = null;
//            }
//            if (!empty($input['actual_overtime_start_time']))
//            {
//                $r->actual_overtime_start_time = $input['actual_overtime_start_time'];
//            }
//            else
//            {
//                $r->actual_overtime_start_time = null;
//            }
//            if (!empty($input['actual_overtime_end_time']))
//            {
//                $r->actual_overtime_end_time = $input['actual_overtime_end_time'];
//            }
//            else
//            {
//                $r->actual_overtime_end_time = null;
//            }

            switch ($input['plan_accept']) {
                case '0': /* reject */
                    $r->is_plan_entry       = (int) true;
                    $r->is_plan_accept      = (int) false;
                    $r->is_plan_reject      = (int) true;
                    $r->plan_reject_user_id = \Auth::user()->id;
                    $r->plan_rejected_at    = date('Y-m-d H:i:s');
                    break;
                case '1': /* accept */
                    $r->is_plan_entry       = (int) true;
                    $r->is_plan_accept      = (int) true;
                    $r->is_plan_reject      = (int) false;
                    $r->plan_accept_user_id = \Auth::user()->id;
                    $r->plan_accepted_at    = date('Y-m-d H:i:s');
                    break;
                case '2': /* reset */
                    $r->is_plan_entry       = (int) false;
                    $r->is_plan_accept      = (int) false;
                    $r->is_plan_reject      = (int) false;
                    $r->plan_reject_user_id = 0;
                    $r->plan_accept_user_id = 0;
                    $r->plan_accepted_at    = null;
                    $r->plan_rejected_at    = null;
                    break;
            }
            switch ($input['actual_accept']) {
                case '0': /* reject */
                    $r->is_actual_entry       = (int) true;
                    $r->is_actual_accept      = (int) false;
                    $r->is_actual_reject      = (int) true;
                    $r->actual_reject_user_id = \Auth::user()->id;
                    $r->actual_rejected_at    = date('Y-m-d H:i:s');
                    break;
                case '1': /* accept */
                    $r->is_actual_entry       = (int) true;
                    $r->is_actual_accept      = (int) true;
                    $r->is_actual_reject      = (int) false;
                    $r->actual_accept_user_id = \Auth::user()->id;
                    $r->actual_accepted_at    = date('Y-m-d H:i:s');
                    break;
                case '2': /* reset */
                    $r->is_actual_entry       = (int) false;
                    $r->is_actual_accept      = (int) false;
                    $r->is_actual_reject      = (int) false;
                    $r->actual_reject_user_id = 0;
                    $r->actual_accept_user_id = 0;
                    $r->actual_accepted_at    = null;
                    $r->actual_rejected_at    = null;
                    break;
            }
            $r->save();
        });
    }

    public function getSearchRosters($input) {
        $rosters = $this->getRosters();
        if (isset($input['plan']))
        {
            switch ($input['plan']) {
                case 1:
                    $rosters->where('is_plan_entry', '=', true)
                            ->where('is_plan_accept', '<>', true)
                            ->where('is_plan_reject', '<>', true);
                    break;
                case 2:
                    $rosters->where('is_plan_entry', '=', true)
                            ->where('is_plan_accept', '=', true)
                            ->where('is_plan_reject', '<>', true);
                    break;
                case 3:
                    $rosters->where('is_plan_entry', '=', true)
                            ->where('is_plan_accept', '<>', true)
                            ->where('is_plan_reject', '=', true);
                    break;
            }
        }

        if (isset($input['actual']))
        {
            switch ($input['actual']) {
                case 1:
                    $rosters->where('is_actual_entry', '=', true)
                            ->where('is_actual_accept', '<>', true)
                            ->where('is_actual_reject', '<>', true);
                    break;
                case 2:
                    $rosters->where('is_actual_entry', '=', true)
                            ->where('is_actual_accept', '=', true)
                            ->where('is_actual_reject', '<>', true);
                    break;
                case 3:
                    $rosters->where('is_actual_entry', '=', true)
                            ->where('is_actual_accept', '<>', true)
                            ->where('is_actual_reject', '=', true);
                    break;
            }
        }

        if (!empty($input['name']))
        {
            $rosters->where('USER.last_name', 'LIKE', "%{$input['name']}%");
        }

        if (!empty($input['division']))
        {
            $rosters->where('sinren_users.division_id', '=', $input['division']);
        }

        if (!empty($input['min_date']))
        {
            $date = date('Y-m-d', strtotime($input['min_date']));
            $rosters->where('rosters.entered_on', '>=', $date);
        }
        if (!empty($input['max_date']))
        {
            $date = date('Y-m-d', strtotime($input['max_date']));
            $rosters->where('rosters.entered_on', '<=', $date);
        }
        return $rosters;
    }

    public function makeExportData($input) {
        $rs          = $this->getSearchRosters($input)->get();
        $plan_rows   = [];
        $actual_rows = [];
        foreach ($rs as $r) {
            $ebas001 = $r->staff_number;
            $lsls001 = date('Y/m/j', strtotime($r->entered_on));
            $ltlt001 = $lsls001;
            $lsls002 = 1;
            $ltlt002 = 1;
            $lsls003 = (!empty($r->plan_work_type_id)) ?
                    sprintf('%03d', $r->plan_work_type_id) :
                    '';
            $ltlt003 = (!empty($r->actual_work_type_id)) ?
                    sprintf('%03d', $r->actual_work_type_id) :
                    '';
            $lsls004 = (!empty($r->plan_rest_reason_id)) ?
                    $r->plan_rest_reason_id :
                    '';
            $ltlt004 = (!empty($r->actual_rest_reason_id)) ?
                    $r->actual_rest_reason_id :
                    '';
            $ltdt001 = (!empty($r->actual_overtime_start_time) && $r->actual_overtime_start_time != '0000-00-00 00:00:00') ?
                    date('G:i', strtotime($r->actual_overtime_start_time)) :
                    '';
            $ltdt002 = (!empty($r->actual_overtime_end_time) && $r->actual_overtime_end_time != '0000-00-00 00:00:00') ?
                    date('G:i', strtotime($r->actual_overtime_end_time)) :
                    '';
            $ltlt009 = $r->actual_overtime_reason;

            $plan_rows[]   = [$ebas001, $lsls001, $lsls002, $lsls003, $lsls004,];
            $actual_rows[] = [$ebas001, $ltlt001, $ltlt002, $ltlt003, $ltlt004, $ltdt001, $ltdt002, $ltlt009,];
        }
        $this->plan_rows   = $plan_rows;
        $this->actual_rows = $actual_rows;
        return $this;
    }

    public function getRows($type) {
        if ($type !== 'plan' && $type !== 'actual')
        {
            throw new \Exception('予期しないデータ名が指定されました。');
        }

        return ($type === 'plan') ? $this->plan_rows : $this->actual_rows;
    }

    public function export($rows, $file_name, $header/* , $is_enclose */) {
//        $obj = new CsvService();
        return $this->exportCsv($rows, $file_name, $header/* , $is_enclose */);
    }

    public function getRawData($input) {
        $rs       = $this->getSearchRosters($input)->get();
        $csv_rows = [];
        foreach ($rs as $r) {
            $plan_enter_state    = ($r->is_plan_entry) ? "入力済み" : "未入力";
            $actual_enter_state  = ($r->is_actual_entry) ? "入力済み" : "未入力";
            $plan_accept_state   = ($r->is_plan_accept) ? "承認済み" : "未承認";
            $plan_reject_state   = ($r->is_plan_reject) ? "却下" : "";
            $actual_accept_state = ($r->is_actual_accept) ? "承認済み" : "未承認";
            $actual_reject_state = ($r->is_actual_reject) ? "却下" : "";

            $csv_rows[] = [
                $r->staff_number,
                $r->user_name,
                $r->division_name,
                //
                $plan_enter_state,
                $actual_enter_state,
                $plan_accept_state,
                $plan_reject_state,
                $r->plan_accept_user_name,
                $r->plan_reject_user_name,
                $actual_accept_state,
                $actual_reject_state,
                $r->actual_accept_user_name,
                $r->actual_reject_user_name,
                //
                $r->plan_work_type_name,
                $r->plan_rest_reason_name,
                $r->plan_work_start_time,
                $r->plan_work_end_time,
                $r->plan_overtime_start_time,
                $r->plan_overtime_end_time,
                $r->plan_overtime_reason,
                //
                $r->actual_work_type_name,
                $r->actual_rest_reason_name,
                $r->actual_work_start_time,
                $r->actual_work_end_time,
                $r->actual_overtime_start_time,
                $r->actual_overtime_end_time,
                $r->actual_overtime_reason,
                //
                $r->updated_at->format('Y-m-d H:i:s'),
            ];
        }
        return $csv_rows;
//        dd($rs);
//        dd($csv_rows);
    }

}
