<?php

namespace App\Services\Roster;

class RosterAccept
{

//    use \App\Services\Traits\DateUsable;

    protected $chief_user_id;
    protected $control_divisions;

//    protected $inputs;
//
    public function __construct($chief_user_id) {
        \App\User::findOrFail($chief_user_id);

        $ctl_divs = \App\ControlDivision::joinUsers($chief_user_id)->groupBy('control_divisions.division_id')->select('control_divisions.division_id')->get();
        if ($ctl_divs->isEmpty())
        {
            throw new \Exception('責任者の管轄部署が存在しません。');
        }
        $control_divisions = [];
        foreach ($ctl_divs as $div) {
            $control_divisions[] = $div->division_id;
        }
        $this->control_divisions = $control_divisions;
        $this->chief_user_id     = $chief_user_id;
    }

    public function updateRoster($input) {
        if (empty($input['id']))
        {
            throw new \Exception('勤務データIDがセットされていないようです。');
        }
        foreach ($input['id'] as $id) {
            if (empty($id))
            {

                continue;
            }
            $roster = \App\Roster::leftJoin('sinren_db.sinren_users', 'rosters.user_id', '=', 'sinren_users.user_id')
                    ->select(\DB::raw('*, rosters.id as id, rosters.user_id as user_id'))
                    ->findOrFail($id)
            ;
            if (!in_array($roster->division_id, $this->control_divisions))
            {
                throw new \Exception('許可されていない部署のデータを承認しようとしました。');
            }
            if ($roster->user_id == $this->chief_user_id)
            {
                throw new \Exception('自分自身のデータを承認しようとしました。');
            }
            $is_plan_entered   = (isset($input['plan'][$id])) ? true : false;
            $is_actual_entered = (isset($input['actual'][$id])) ? true : false;
            if ($is_plan_entered)
            {
                $is_plan_accept = ($input['plan'][$id]) ? true : false;
                $this->updatePlan($roster, $input, $id, $is_plan_accept);
            }
            if ($is_actual_entered)
            {
                $is_actual_accept = ($input['actual'][$id]) ? true : false;
                $this->updateActual($roster, $input, $id, $is_actual_accept);
            }
            $roster->save();

            unset($roster);
        }
    }

    private function updatePlan($roster, $input, $id, $is_plan_accept) {
        if (!$roster->is_plan_entry)
        {
            throw new \Exception("{$roster->entered_on}の予定データが入力されていないようです。");
        }
        if ($roster->is_plan_accept || $roster->is_actual_accept)
        {
            throw new \Exception("すでに{$roster->entered_on}のデータは承認されています。");
        }
        $reject_reason = (isset($input['plan_reject'][$id])) ? $input['plan_reject'][$id] : '';
        if ($is_plan_accept)
        {
            $roster->is_plan_accept      = true;
            $roster->is_plan_reject      = false;
            $roster->plan_accepted_at    = date('Y-m-d H:i:s');
            $roster->plan_accept_user_id = $this->chief_user_id;
            $roster->reject_reason       = $reject_reason;
        } else
        {
            $roster->is_plan_accept      = false;
            $roster->is_plan_reject      = true;
            $roster->plan_rejected_at    = date('Y-m-d H:i:s');
            $roster->plan_reject_user_id = $this->chief_user_id;
            $roster->reject_reason       = $reject_reason;
        }
    }

    private function updateActual($roster, $input, $id, $is_actual_accept) {
        if (!$roster->is_plan_entry)
        {
            throw new \Exception("{$roster->entered_on}の予定データが入力されていないようです。");
        }
        if (!$roster->is_plan_accept)
        {
            throw new \Exception("{$roster->entered_on}の予定データが承認されていないようです。");
        }
        if (!$roster->is_actual_entry)
        {
            throw new \Exception("{$roster->entered_on}の実績データが入力されていないようです。");
        }
        if ($roster->is_actual_accept)
        {
            throw new \Exception("すでに{$roster->entered_on}のデータは承認されています。");
        }
        $reject_reason = (isset($input['actual_reject'][$id])) ? $input['actual_reject'][$id] : '';
        if ($is_actual_accept)
        {
            $roster->is_actual_accept      = true;
            $roster->is_actual_reject      = false;
            $roster->actual_accepted_at    = date('Y-m-d H:i:s');
            $roster->actual_accept_user_id = $this->chief_user_id;
            $roster->reject_reason         = $reject_reason;
        } else
        {
            $roster->is_actual_accept      = false;
            $roster->is_actual_reject      = true;
            $roster->actual_rejected_at    = date('Y-m-d H:i:s');
            $roster->actual_reject_user_id = $this->chief_user_id;
            $roster->reject_reason         = $reject_reason;
        }
    }

}
