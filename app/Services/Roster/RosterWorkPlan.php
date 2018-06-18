<?php

namespace App\Services\Roster;

class RosterWorkPlan
{

    use \App\Services\Traits\DateUsable;

    public function updateWorkPlan($input, $user_id, $month_id, $chief_id) {
        \DB::connection('mysql_roster')->transaction(function () use($input, $user_id, $month_id, $chief_id) {
            foreach ($input['entered_on'] as $key_date) {
                if (empty($key_date) || !$this->isDate($key_date))
                {
                    throw new \Exception('データ登録日カラムに異常があったため、処理を中断しました。');
                }
                $roster = \App\Roster::firstOrNew(['user_id' => $user_id, 'month_id' => $month_id, 'entered_on' => $key_date,]);
                $this->edit($roster, $input, $user_id, $key_date, $chief_id);
            }
        });
        return true;
    }

    private function edit($roster, $input, $user_id, $key_date, $chief_id) {
        // すでに入力済みなら更新を行わない
        if ($roster->is_plan_entry || $roster->is_actual_entry)
        {
            return false;
        }

        $rest    = (empty($input['rest'][$key_date])) ? null : \App\Rest::where('rest_reason_id', $input['rest'][$key_date])->first();
        $is_rest = (!empty($rest)) ? true : false;

//        dd($input, $key_date, $rest);
        $is_short_time               = (!empty($rest) && ($rest->rest_reason_name === '遅刻' || $rest->rest_reason_name === '早退')) ? true : false;
//        dd($is_rest, $is_short_time);
        // 予定勤務形態
//        $roster->plan_work_type_id = (!empty($input['work_type'][$key_date])) ? $input['work_type'][$key_date] : 0;
        $roster->plan_work_type_id   = (!$is_rest || $is_short_time) ? $input['work_type'][$key_date] : 0;
        // 予定休暇理由
        $roster->plan_rest_reason_id = ($is_rest) ? $input['rest'][$key_date] : 0;
//        if (!empty($input['rest'][$key_date]))
//        {
//            $roster->plan_rest_reason_id = $input['rest'][$key_date];
//        }
        // 勤務日
        $roster->entered_on          = $key_date;
        // 非登録者ID
        $roster->user_id             = $user_id;
        // 登録者ID
        if (empty($roster->id))
        {
            $roster->create_user_id = $chief_id;
        }
        else
        {
            $roster->edit_user_id = $chief_id;
        }
        $roster->save();
        if (env('APP_DEBUG'))
            \Log::debug(['is_rest' => $is_rest, 'is_short_time' => $is_short_time, $roster->toArray()]);
        return true;
    }

}
