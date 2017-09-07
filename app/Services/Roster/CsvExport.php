<?php

namespace App\Services\Roster;

use App\Services\CsvService;

class CsvExport
{

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
        $rosters       = \App\Roster::where('month_id', '=', $ym)
                ->join('sinren_data_db.sinren_users', 'rosters.user_id', '=', 'sinren_users.user_id')
                ->join('sinren_data_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->join('laravel_db.users', 'rosters.user_id', '=', 'users.id')
                ->select(\DB::raw('*, rosters.id AS key_id'))
                ->orderBy('sinren_users.division_id', 'asc')
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
            $r                         = \App\Roster::finputd($input['id']);
            $r->plan_work_type_id      = $input['plan_work_type_id'];
            $r->actual_work_type_id    = $input['actual_work_type_id'];
            $r->actual_rest_reason_id  = $input['actual_rest_reason_id'];
            $r->plan_rest_reason_id    = $input['plan_rest_reason_id'];
            $r->plan_overtime_reason   = $input['plan_overtime_reason'];
            $r->actual_overtime_reason = $input['actual_overtime_reason'];

            if (!empty($input['plan_overtime_start_time']))
            {
                $r->plan_overtime_start_time = $input['plan_overtime_start_time'];
            }
            else
            {
                $r->plan_overtime_start_time = null;
            }
            if (!empty($input['plan_overtime_end_time']))
            {
                $r->plan_overtime_end_time = $input['plan_overtime_end_time'];
            }
            else
            {
                $r->plan_overtime_end_time = null;
            }

            if (!empty($input['actual_overtime_start_time']))
            {
                $r->actual_overtime_start_time = $input['actual_overtime_start_time'];
            }
            else
            {
                $r->actual_overtime_start_time = null;
            }

            if (!empty($input['actual_overtime_end_time']))
            {
                $r->actual_overtime_end_time = $input['actual_overtime_end_time'];
            }
            else
            {
                $r->actual_overtime_end_time = null;
            }

            switch ($input['plan_accept']) {
                case '0':
                    $r->is_plan_entry       = (int) true;
                    $r->is_plan_accept      = (int) false;
                    $r->is_plan_reject      = (int) true;
                    $r->plan_accept_user_id = \Auth::user()->id;
                    $r->plan_accepted_at    = date('Y-m-d H:i:s');
                    break;
                case '1':
                    $r->is_plan_entry       = (int) true;
                    $r->is_plan_accept      = (int) false;
                    $r->is_plan_reject      = (int) true;
                    $r->plan_reject_user_id = \Auth::user()->id;
                    $r->plan_rejected_at    = date('Y-m-d H:i:s');
                    break;
                case '2':
                    $r->is_plan_accept      = (int) false;
                    $r->is_plan_reject      = (int) false;
                    $r->plan_reject_user_id = 0;
                    $r->plan_accept_user_id = 0;
                    $r->plan_accepted_at    = null;
                    $r->plan_rejected_at    = null;
                    break;
            }
            switch ($input['actual_accept']) {
                case '0':
                    $r->is_actual_entry       = (int) true;
                    $r->is_actual_accept      = (int) false;
                    $r->is_actual_reject      = (int) true;
                    $r->actual_accept_user_id = \Auth::user()->id;
                    $r->actual_accepted_at    = date('Y-m-d H:i:s');
                    break;
                case '1':
                    $r->is_actual_entry       = (int) true;
                    $r->is_actual_accept      = (int) false;
                    $r->is_actual_reject      = (int) true;
                    $r->actual_reject_user_id = \Auth::user()->id;
                    $r->actual_rejected_at    = date('Y-m-d H:i:s');
                    break;
                case '2':
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
            $rosters->where('users.name', 'LIKE', "%{$input['name']}%");
        }

        if (!empty($input['division']))
        {
            $rosters->where('sinren_users.division_id', '=', $input['division']);
        }

        if (!empty($input['date']))
        {
            $date = date('Y-m-d', strtotime($input['date']));
            $rosters->where('rosters.entered_on', '=', $date);
        }
        return $rosters;
    }

    public function makeExportData($input) {
        $rs          = $this->getSearchRosters($input)->get();
        $plan_rows   = [];
        $actual_rows = [];
        foreach ($rs as $r) {
            $ebas001 = $r->staff_number;
            $lsls001 = date('Y/n/j', strtotime($r->entered_on));
            $ltlt001 = $lsls001;
            $lsls002 = 1;
            $ltlt002 = 1;
            $lsls003 = (!empty($r->plan_work_type_id)) ? $r->plan_work_type_id : '';
            $ltlt003 = (!empty($r->actual_work_type_id)) ? $r->actual_work_type_id : '';
            $lsls004 = (!empty($r->plan_rest_reason_id)) ? $r->plan_rest_reason_id : '';
            $ltlt004 = (!empty($r->actual_rest_reason_id)) ? $r->actual_rest_reason_id : '';
            $ltdt001 = (!empty($r->actual_overtime_start_time) && $r->actual_overtime_start_time != '0000-00-00 00:00:00') ? date('G:i', strtotime($r->actual_overtime_start_time)) : '';
            $ltdt002 = (!empty($r->actual_overtime_end_time) && $r->actual_overtime_end_time != '0000-00-00 00:00:00') ? date('G:i', strtotime($r->actual_overtime_end_time)) : '';
            $ltlt009 = $r->actual_overtime_reason;

            $plan_rows[]   = [
                $ebas001,
                $lsls001,
                $lsls002,
                $lsls003,
                $lsls004,
            ];
            $actual_rows[] = [
                $ebas001,
                $ltlt001,
                $ltlt002,
                $ltlt003,
                $ltlt004,
                $ltdt001,
                $ltdt002,
                $ltlt009,
            ];
        }
        $this->plan_rows   = $plan_rows;
        $this->actual_rows = $actual_rows;
        return $this;
    }

    public function getRows($type) {
        if ($type == 'plan')
        {
            return $this->plan_rows;
        }
        elseif ($type == 'actual')
        {
            return $this->actual_rows;
        }
        else
        {
            throw new \Exception('予期しないデータ名が指定されました。');
        }
    }

    public function export($rows, $file_name, $header) {
        $obj = new CsvService();
        return $obj->exportCsv($rows, $file_name, $header);
    }

}
