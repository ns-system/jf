<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Roster\AcceptPlan;
use App\Http\Requests\Roster\CalendarAccept;
use App\Http\Controllers\Controller;
use \App\Services\Roster\RosterAccept;

class RosterAcceptController extends Controller
{

    const INT_MONTH_COUNT = 2;

    private function getQuery() {
        $id = \Auth::user()->id;
        $t  = \App\ControlDivision::joinUsers($id)
                ->joinDivisions()
                // connect to rosters
                ->join('roster_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->where('roster_users.user_id', '<>', $id)
                ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
                ->where('rosters.month_id', '<>', 0)
                ->select(\DB::raw('COUNT(*) AS cnt, sinren_divisions.division_id, rosters.month_id'))
                ->groupBy('rosters.month_id')
                ->orderBy('rosters.month_id', 'desc')
        ;
        return $t;
    }

    private function editKey($rows) {
        if ($rows == null)
        {
            return [];
        }
        $tmp = [];
        foreach ($rows as $row) {
            $tmp[$row->division_id][$row->month_id] = $row->cnt;
        }
        return $tmp;
    }

    public function index() {
        // 月ごとのレコード件数を集計する
        $plan_accept       = $this->getQuery()->groupBy('rosters.is_plan_accept')->where('is_plan_accept', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $plan_reject       = $this->getQuery()->groupBy('rosters.is_plan_reject')->where('is_plan_reject', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $actual_accept     = $this->getQuery()->groupBy('rosters.is_actual_accept')->where('is_actual_accept', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $actual_reject     = $this->getQuery()->groupBy('rosters.is_actual_reject')->where('is_actual_reject', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $plan_entry        = $this->getQuery()->groupBy('rosters.is_plan_entry')->where('is_plan_entry', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $actual_entry      = $this->getQuery()->groupBy('rosters.is_actual_entry')->where('is_actual_entry', '=', true)->take(self::INT_MONTH_COUNT)->get();
        $plan_not_accept   = $this->getQuery()->groupBy('rosters.is_plan_entry')->where(['is_plan_entry' => true, 'is_plan_accept' => false, 'is_plan_reject' => false,])->take(self::INT_MONTH_COUNT)->get();
        $actual_not_accept = $this->getQuery()->groupBy('rosters.is_actual_entry')->where(['is_actual_entry' => true, 'is_actual_accept' => false, 'is_actual_reject' => false,])->take(self::INT_MONTH_COUNT)->get();

        $rows = [
            'plan_accepts'       => $this->editKey($plan_accept),
            'plan_rejects'       => $this->editKey($plan_reject),
            'plan_entry'         => $this->editKey($plan_entry),
            'actual_accepts'     => $this->editKey($actual_accept),
            'actual_rejects'     => $this->editKey($actual_reject),
            'actual_entry'       => $this->editKey($actual_entry),
            'plan_not_accepts'   => $this->editKey($plan_not_accept),
            'actual_not_accepts' => $this->editKey($actual_not_accept),
        ];

        // 管理部署のリストを取得する
        $divs = \App\ControlDivision::joinDivisions()
                ->user(\Auth::user()->id)
                ->orderBy('sinren_divisions.division_id', 'asc')
                ->get()
        ;

        // 最大INT_MONTH_COUNTヶ月分の表示する月を生成
        $next_month = new \DateTime();
//        $next_month->modify('+1 month');
        $months     = \App\Roster::groupBy('month_id')->where('month_id', '<>', 0)->where('month_id', '<=', $next_month->format('Ym'))->take(self::INT_MONTH_COUNT)->orderBy('month_id', 'desc')->get(['month_id']);
        $params     = [
            'rows'          => $rows,
            'divs'          => $divs,
            'plan_accept'   => $plan_accept,
            'plan_reject'   => $plan_reject,
            'actual_accept' => $actual_accept,
            'actual_reject' => $actual_reject,
            'plan_entry'    => $plan_entry,
            'actual_entry'  => $actual_entry,
            'months'        => $months,
        ];
        return view('roster.app.accept.index', $params);
    }

    public function calendarIndex($ym, $div, $user_id = 0) {

        $input       = \Input::get();
//        $user_id         = (empty($input['user'])) ? null : $input['user'];
        $status      = (empty($input['status']) || $input['status'] === 'all') ? 'all' : 'part';
        $is_show_all = ($status === 'all') ? true : false;

        $users = \App\SinrenUser::join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->join('roster_db.roster_users as R_USER', 'users.id', '=', 'R_USER.user_id')
                ->where('sinren_users.division_id', '=', $div)
                ->where('sinren_users.user_id', '<>', \Auth::user()->id)
                ->where('R_USER.is_administrator', '=', false)
                ->where('R_USER.is_chief', '=', false)
                ->get()
        ;

        $rows = (empty($user_id)) ? [] : $this->getCalendar($ym, $user_id, $is_show_all);

        // 未承認 or 却下のみを抽出するクエリを発行
        $unchecked_rows = $this->getUncheckedRows($users, $ym);

        $tmp_types = \App\WorkType::workTypeList()->orderBy('work_type_id')->get();
        $types     = [];
        foreach ($tmp_types as $t) {
            $types[$t->work_type_id] = [
                'name' => $t->work_type_name,
                'time' => $t->display_time,
            ];
        }
        $tmp_rests = \App\Rest::get();
        $rests     = [];
        foreach ($tmp_rests as $r) {
            $rests[$r->rest_reason_id] = $r->rest_reason_name;
        }

        $param = [
            'ym'        => $ym,
            'div'       => $div,
            'rests'     => $rests,
            'types'     => $types,
            'rows'      => $rows,
            'users'     => $users,
            'user_id'   => $user_id,
            'status'    => $status,
            'unchecked' => $unchecked_rows,
        ];
        return view('roster.app.accept.calendar_list', $param);
    }

    private function getUncheckedRows($users, $monthly_id) {
        $ids = [];
        foreach ($users as $u) {
            $ids[] = $u->user_id;
        }
        $serial    = strtotime($monthly_id . '01');
        $first_day = date('Y-m-d', $serial);
        $last_day  = date('Y-m-t', $serial);

        $unchecked_rows = \App\Roster::where(function($query) use($ids) {
                    foreach ($ids as $id) {
                        $query->orWhere('user_id', $id);
                    }
                })
                ->where('entered_on', '>=', $first_day)
                ->where('entered_on', '<=', $last_day)
//                ->where('is_plan_entry', true)
//                ->where('is_plan_accept', false)
                ->where(function($query) {
                    $query->orWhere(function($query) {
                        $query->where('is_plan_entry', true)->where('is_plan_accept', false);
                    })->orWhere(function($query) {
                        $query->where('is_actual_entry', true)->where('is_actual_accept', false);
                    });
                })
                ->orderBy('user_id')
                ->orderBy('entered_on')
                ->with('laraveluser')
                ->get()
        ;
//        var_dump($ids);
//        var_dump($first_day);
//        var_dump($last_day);
//        dd($unchecked_rows);
        return $unchecked_rows;
    }

    public function getCalendar($ym, $user_id, $is_show_all) {
        $first_day = date('Y-m-d', strtotime($ym . '01'));
        $last_day  = date('Y-m-t', strtotime($ym . '01'));
        $obj       = new \App\Services\Roster\Calendar();
        $sql       = \App\Roster::leftJoin('sinren_db.sinren_users as S_USER', 'rosters.user_id', '=', 'S_USER.user_id')
                ->leftJoin('roster_db.roster_users as R_USER', 'rosters.user_id', '=', 'R_USER.user_id')
                ->leftJoin('sinren_db.sinren_divisions', 'S_USER.division_id', '=', 'sinren_divisions.division_id')
                ->leftJoin('laravel_db.users', 'rosters.user_id', '=', 'users.id')
                ->select(\DB::raw('*, rosters.id as key_id'))
                ->where('R_USER.user_id', '=', $user_id)
                ->where('rosters.entered_on', '>=', $first_day)
                ->where('rosters.entered_on', '<=', $last_day)
        ;

        if ($is_show_all === false)
        {
            $sql = $sql->where(function($query) {
                        $query
                        ->orWhere('rosters.is_plan_accept', '=', false)
                        ->orWhere('rosters.is_actual_accept', '=', false)
                        ;
                    })
                    ->where('rosters.is_plan_entry', '=', true)
            ;
        }
        $tmp = $obj->setId($ym)->makeCalendar($sql->get());
        $cal = $obj->convertCalendarToList($tmp);
        return $cal;
    }

    public function calendarAccept(CalendarAccept $request, $monthly_id, $division_id, $user_id) {
        $is_bulk       = (!empty($request['is_bulk'])) ? $request['is_bulk'] : false;
        $is_contain    = false;
        $chief_user_id = \Auth::user()->id;
        // 自分自身を更新しようとしていないかチェック
        if ($chief_user_id == $user_id)
        {
            \Session::flash('danger_message', "自分自身のデータを承認することはできません。");
            return back();
        }
        // 対象ユーザーが管轄部署に含まれているかチェック
        $me  = \App\ControlDivision::user($chief_user_id)->get(['division_id']);
        $you = \App\SinrenUser::where('user_id', $user_id)->first();
        if (empty($you) && !$is_bulk)
        {
            \Session::flash('danger_message', "勤怠管理ユーザーの登録が行われていないようです。");
            return back();
        }
        if (!$is_bulk)
        {
            foreach ($me as $m) {
                $is_contain = ($you->division_id === $m->division_id) ? true : $is_contain;
            }
        }

        if (!$is_contain && !$is_bulk)
        {
            \Session::flash('danger_message', "許可されていない部署のデータを承認しようとしました。");
            return back();
        }

        // メインロジック
        $input = $request->input();
        try {
            \DB::connection('mysql_roster')->transaction(function() use($input) {
                $service = new RosterAccept(\Auth::user()->id);
                $service->updateRoster($input);
            });
        } catch (\Exception $e) {
            \Session::flash('danger_message', $e->getMessage());
            return back();
        }
        \Session::flash('success_message', 'データの一括更新が完了しました。');
        return redirect(route_with_query('app::roster::accept::calendar', ['ym' => $monthly_id, 'div' => $division_id, 'user' => $user_id], ['status' => 'all']));
    }

}
