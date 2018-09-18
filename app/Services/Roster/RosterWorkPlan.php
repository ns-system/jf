<?php

namespace App\Services\Roster;

class RosterWorkPlan
{

    use \App\Services\Traits\DateUsable;

    public function updateWorkPlan($input, $user_id, $month_id, $chief_id)
    {
        \DB::connection('mysql_roster')->transaction(function () use ($input, $user_id, $month_id, $chief_id) {
            foreach ($input['entered_on'] as $key_date) {
                if (empty($key_date) || !$this->isDate($key_date)) {
                    throw new \Exception('データ登録日カラムに異常があったため、処理を中断しました。');
                }
                $roster = \App\Roster::firstOrNew(['user_id' => $user_id, 'month_id' => $month_id, 'entered_on' => $key_date,]);
                $this->edit($roster, $input, $user_id, $key_date, $chief_id);
            }
        });
        return true;
    }

    private function edit($roster, $input, $user_id, $key_date, $chief_id)
    {
        // すでに入力済みなら更新を行わない
        // 20180713 条件変更
        if ($roster->is_plan_accept || $roster->is_actual_accept) {
            return false;
        }

        $rest_id      = (!empty($input['rest'][$key_date])) ? $input['rest'][$key_date] : false;
        $work_type_id = ($input['work_type'][$key_date]) ? $input['work_type'][$key_date] : false;
        $rest         = empty($rest_id) ? null : \App\Rest::where('rest_reason_id', $rest_id)->first();
        $is_holyday   = (!$rest_id && !$work_type_id) ? true : false;

        // 2018-07-31 完全オフ（土日祝日）以外は全て勤務形態の入力を必須とする
        if (!$is_holyday && empty($work_type_id)) {
            throw new \Exception("{$key_date}の勤務形態を入力してください。");
        }

        $is_short_time = ($rest_id && ($rest->rest_reason_name === '遅刻' || $rest->rest_reason_name === '早退')) ? true : false;
        // 予定勤務形態
        $roster->plan_work_type_id = (!$is_holyday) ? $work_type_id : 0;
        // 予定休暇理由
        $roster->plan_rest_reason_id = ($rest_id) ? $input['rest'][$key_date] : 0;
        // 勤務日
        $roster->entered_on = $key_date;
        // 非登録者ID
        $roster->user_id = $user_id;
        // 登録者ID
        if (empty($roster->id)) {
            $roster->create_user_id = $chief_id;
        } else {
            $roster->edit_user_id = $chief_id;
        }
        $roster->save();
//        if (env('APP_DEBUG'))
//            \Log::debug(['is_rest' => $rest_id, 'is_short_time' => $is_short_time, $roster->toArray()]);
        return true;
    }

}
