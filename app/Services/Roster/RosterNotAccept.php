<?php

namespace App\Services\Roster;

class RosterNotAccept
{

//    protected $chief_id    = 0;
//    protected $is_admin    = false;
//    protected $division_id = 0;
//    protected $month_id    = 0;
//    protected $first_day   = null;
//    protected $last_day    = null;
    protected $query = null;

    public function __construct()
    {
        $query = \App\SinrenUser::join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
            ->join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
            ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
            ->join('sinren_db.control_divisions', 'sinren_users.division_id', '=', 'control_divisions.division_id')
            ->leftJoin('sinren_db.holidays', 'rosters.entered_on', '=', 'holidays.holiday')
            ->where('users.retirement', false)
            ->groupBy('rosters.id')
            ->orderBy('entered_on');

        $this->query = $query;
    }

    public function chiefId(int $chief_id)
    {
        $this->query->where('control_divisions.user_id', $chief_id);
        return $this;
    }

    public function userId(int $user_id)
    {
        $this->query->where('rosters.user_id', $user_id);
        return $this;
    }

    public function divisionId(int $division_id)
    {
        $this->query->where('sinren_users.division_id', $division_id);
        return $this;
    }

    public function monthId(int $month_id, $month_count = 0)
    {
        $cnt = (int) $month_count;
        if (empty($month_count)) {
            $this->query->where('month_id', $month_id);
            return $this;
        }

        $dt     = new \DateTime();
        $params = [
            (int) $dt->modify("first day of -{$cnt} months")->format('Ym'),
            (int) date('Ym'),
        ];
        $this->query->whereBetween('month_id', $params);
        return $this;
    }

    public function firstDay(string $fist_day)
    {
        $this->query->where('rosters.entered_on', '>=', $fist_day);
        return $this;
    }

    public function lastDay(string $last_day)
    {
        $this->query->where('rosters.entered_on', '<=', $last_day);
        return $this;
    }

    public function rosterId(int $roster_id)
    {
        $this->query->where('rosters.id', $roster_id);
        return $this;
    }

    public function beforeToday()
    {
        $this->query->where('rosters.entered_on', '<=', date('Y-m-d'));
        return $this;
    }

    public function get()
    {
        $columns = [
            'rosters.id',
            'sinren_divisions.division_id',
            'sinren_divisions.division_name',
            'users.last_name',
            'users.first_name',
            'users.email',
            'rosters.month_id',
            'rosters.entered_on',
            'dayofweek(rosters.entered_on) - 1 as week',
            'holidays.holiday_name',
            'rosters.is_plan_entry',
            //            'case when is_plan_entry = true then "入力済み" else "" end as test',
            //            'case when rosters.is_plan_entry = true and rosters.is_actual_entry = false then true else false end as diff',
            'case' .
            '    when rosters.is_plan_entry = true and rosters.is_plan_reject = true then "却下" ' .
            '    when rosters.is_plan_entry = true and rosters.is_plan_accept = true then "承認済み" ' .
            '    when rosters.is_plan_entry then "未承認"' .
            '    else "未入力" ' .
            'end as plan',
            'case' .
            '    when rosters.is_actual_entry = true and rosters.is_actual_reject = true then "却下" ' .
            '    when rosters.is_actual_entry = true and rosters.is_actual_accept = true then "承認済み" ' .
            '    when rosters.is_actual_entry then "未承認"' .
            '    else "未入力" ' .
            'end as actual',
            'case' .
            '    when (dayofweek(rosters.entered_on) - 1) = 0 or holidays.holiday_name is not null then "danger" ' .
            '    when (dayofweek(rosters.entered_on) - 1) = 6 then "info" ' .
            '    else "" ' .
            'end as week_color',
            //            'rosters.is_plan_entry',
            //            'rosters.is_plan_accept',
            //            'rosters.is_actual_entry',
            //            'rosters.is_actual_accept',
        ];

        $having = "(week != 0 and week != 6 and holiday_name is null)     and (plan != '承認済み' or actual != '承認済み') or " .
                  "(week =  0 or  week =  6 or  holiday_name is not null) and (plan != actual)";
        foreach ($columns as $column) {
            $this->query->addSelect(\DB::raw($column));
        }
//        return $this->query->havingRaw('plan <> actual or diff = true')->get();
        $rows = $this->query
            ->whereRaw('rosters.id is not null')
            ->havingRaw($having)
            ->get();

//        dd($rows->toArray());
        return $rows;
    }


}
