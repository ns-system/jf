<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Http\Requests\Roster\Chief;
use App\Http\Controllers\Controller;

class RosterChiefController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $divs = \App\ControlDivision::joinUsers(\Auth::user()->id)->get();
        $rows = \DB::connection('mysql_sinren')
                ->table('sinren_users')
                ->join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->join('roster_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->select(\DB::raw('*, roster_users.id as key_id, sinren_users.user_id as user_id, roster_users.created_at as create_time, roster_users.updated_at as update_time'))
                ->where(function($query) use ($divs) {
                    foreach ($divs as $d) {
                        $query->orWhere('sinren_users.division_id', '=', $d->division_id);
                    }
                })
                ->where('users.is_super_user', '<>', true)
                ->where('roster_users.is_administrator', '<>', true)
                // 本当は自分自身も対象外にするべきでは？
                ->orderBy('sinren_users.division_id', 'asc')
                ->orderBy('users.id', 'asc')
                ->paginate(25)
        ;
//        var_dump($rows);
        return view('roster.app.chief.index', ['rows' => $rows]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Chief $request, $user_id, $roster_user_id) {
        $in          = $request->input();
        $roster_user = \App\RosterUser::find($roster_user_id);
        try {
            $roster_user->is_proxy        = $in['proxy'];
            $roster_user->is_proxy_active = $in['active'];

            $roster_user->save();
            // 管理部署の編集
            $div = \App\SinrenUser::where('user_id', '=', $user_id)->first();
            // 現在の管轄部署を一旦クリアする
            \App\ControlDivision::user($user_id)->delete();
            if ($in['proxy'] == true && $in['active'] == true)
            {
                // 責任者代理が有効なときのみ改めて管轄部署を追加する
                \App\ControlDivision::create(['user_id' => $user_id, 'division_id' => $div->division_id]);
            }
        } catch (\Exception $exc) {
            \Session::flash('danger_message', $exc->getMessage());
            return back();
        }

        try {
            $res = [
                'edited_user' => \App\User::where('id', '=', $user_id)->first(),
                'edit_user'   => \Auth::user(),
                'result'      => $in,
            ];
            $this->dispatch(new \App\Jobs\Roster\EditNotice($res));
        } catch (\Exception $e) {
            \Session::flash('danger_message', $e->getMessage());
            return back();
        }

//        dd($roster_user);

        \Session::flash('success_message', "データを更新しました。");
        return redirect(route('app::roster::chief::index'));
    }

    private function makeCalendar($month, $rosters) {
        $str  = date('Y-m-01', strtotime($month));
        $date = new \DateTimeImmutable($str . ' 00:00:00');
        $day  = (int) $date->format('w');
        $week = 0;
        $max  = (int) $date->format('t');
        $key  = new \DateTime($str);
        $rows = [];
        $frm  = ['day' => '', 'key' => '', 'holiday' => '', 'data' => []];

        $holidays = \App\Holiday::whereBetween('holiday', [$date->format('Y-m-01'), $date->format('Y-m-t')])
                ->get()
                ->keyBy('holiday')
                ->toArray()
        ;
        for ($i = $day; $i > 0; --$i) {
            $rows[$week][] = $frm;
        }

        for ($i = 1; $i <= $max; ++$i) {
            if ($day > 6)
            {
                ++$week;
                $day = 0;
            }
            $key_day       = $key->format('Y-m-d');
            $rows[$week][] = [
                'day'     => $i,
                'key'     => $key_day,
                'holiday' => (!empty($holidays[$key_day])) ? $holidays[$key_day]['holiday_name'] : '',
                'data'    => (!empty($rosters[$key_day])) ? $rosters[$key_day] : [],
            ];
            ++$day;
            $key->modify('+1 day');
        }
        for ($i = $day; $i <= 6; $i++) {
            $rows[$week][] = $frm;
        }

        return $rows;
    }

    public function calendarIndex($month = null) {
        $date    = (!empty($month)) ? new \DateTime($month . '01') : new \DateTime();
        $user_id = \Auth::user()->id;

        $rows = \App\ControlDivision::join('sinren_db.sinren_users', 'control_divisions.division_id', '=', 'sinren_users.division_id')
                ->join('roster_db.rosters', 'sinren_users.user_id', '=', 'rosters.user_id')
                ->join('roster_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->whereBetween('rosters.entered_on', [$date->format('Y-m-01'), $date->format('Y-m-t')])
                ->where('control_divisions.user_id', $user_id)
                ->where('roster_users.is_administrator', '!=', true)
                ->where('roster_users.is_chief', '!=', true)
                ->groupBy('entered_on')
                ->select(\DB::raw("entered_on, count(*) as total"))
                ->addSelect(\DB::raw("count(if(is_plan_entry = true, 1, null)) as pEntry"))
                ->addSelect(\DB::raw("count(if(is_actual_entry = true, 1, null)) as aEntry"))
                ->addSelect(\DB::raw("count(if(is_plan_accept = true, 1, null)) as pAccept"))
                ->addSelect(\DB::raw("count(if(is_actual_accept = true, 1, null)) as aAccept"))
                ->get()
                ->keyBy('entered_on')
                ->toArray()
        ;

        $user_count = \App\ControlDivision::join('sinren_db.sinren_users', 'control_divisions.division_id', '=', 'sinren_users.division_id')
                ->join('roster_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->where('control_divisions.user_id', $user_id)
                ->where('roster_users.is_administrator', '!=', true)
                ->where('roster_users.is_chief', '!=', true)
                ->count()
        ;

        $calendar = $this->makeCalendar($date->format('Y-m-d'), $rows);
        $next     = (!empty($month)) ? new \DateTime($month . '01') : new \DateTime();
        $before   = (!empty($month)) ? new \DateTime($month . '01') : new \DateTime();

        $params = [
            'calendar'     => $calendar,
            'this_month'   => $date->format('Y年n月'),
            'user_count'   => $user_count,
            'next_month'   => $next->modify('+1 month')->format('Ym'),
            'before_month' => $before->modify('-1 month')->format('Ym'),
        ];
        return view('roster.chief.calendar', $params);
    }

}
