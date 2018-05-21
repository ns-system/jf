<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Roster\RosterUser;
use App\Http\Requests\Roster\AdminChief;
use App\Http\Controllers\Controller;

class RosterUserController extends Controller
{

    public function index($user_id) {
        // 自分自身もしくは管理ユーザーで入った場合のみ処理を通す
        $u        = \Auth::user();
        $r        = \App\RosterUser::user($u->id)->first();
        $is_admin = ($u->is_super_user || (!empty($r) && $r->is_administrator)) ? true : false;


        if ($u->id != $user_id && !$is_admin)
        {
            return redirect()->route('permission_error');
        }

        $divs         = \App\Division::orderBy('division_id', 'asc')->get();
        $types        = \App\WorkType::workTypeList()->get();
        $user         = \App\User::where('users.id', '=', $user_id)
                ->leftJoin('sinren_db.sinren_users', 'users.id', '=', 'sinren_users.user_id')
                ->leftJoin('roster_db.roster_users', 'users.id', '=', 'roster_users.user_id')
                ->select(\DB::raw('*, users.id as id, sinren_users.id as sinren_id, roster_users.id as roster_id'))
                ->first()
        ;
        // $not_register = (empty($user) || empty($user->user_id) || empty($user->sinren_id) || empty($user->roster_id)) ? true : false;
        $not_register = (empty($user) || empty($user->division_id)) ? true : false;
        if (!empty($user->is_administrator) && $user->is_administrator == true)
        {
            $not_register = false;
        }

        $params = [
            'id'           => $user_id,
            'divs'         => $divs,
            'types'        => $types,
            'user'         => $user,
            'is_admin'     => $is_admin,
            'not_register' => $not_register,
        ];
        return view('roster.app.user.index', $params);
    }

    public function edit($user_id, RosterUser $request) {
        $u = \Auth::user();
        $r = \App\RosterUser::user($u->id)->first();
        if ($u->id != $user_id && !$u->is_super_user && (empty($r) || !$r->is_administrator))
        {
            return redirect()->route('permission_error');
        }
        $roster = \App\RosterUser::firstOrNew(['user_id' => $user_id]);
        $sinren = \App\SinrenUser::firstOrNew(['user_id' => $user_id]);

        $sinren->user_id = $user_id;
        if (empty($sinren->division_id))
        {
            $sinren->division_id = $request['division_id'];
        }
        $sinren->save();

        \DB::connection('mysql_roster')->transaction(function() use($user_id, $roster, $request) {
            $roster->user_id      = $user_id;
            $roster->work_type_id = $request['work_type_id'];
            if (empty($request['work_type_id']) && $request['is_chief'])
            {
                $roster->work_type_id = 0;
            }
            $roster->save();
        });

        // 責任者フラグが立っておらず、申請があった場合はメール通知を行う
        if (!$roster->is_chief && $request['is_chief'])
        {
            $this->dispatch(new \App\Jobs\Roster\ChiefNotice($user_id));
        }
        \Session::flash('success_message', 'ユーザーの更新が完了しました。');
        return back();
    }

    public function indexAdmin() {
        $params    = \Input::get();
        // ユーザー情報の取得
        $tmp_users = \App\User::leftJoin('sinren_db.sinren_users', 'users.id', '=', 'sinren_users.user_id')
                ->leftJoin('roster_db.roster_users', 'users.id', '=', 'roster_users.user_id')
                ->leftJoin('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->select(\DB::raw('*, users.id as user_id'))
                ->orderBy('sinren_divisions.division_id', 'desc')
                ->orderBy('users.id', 'asc')
        ;
        $users     = $this->selectRosterUsers($params, $tmp_users);

        // 管轄部署情報の取得
        $controls = \DB::connection('mysql_sinren')
                ->table('control_divisions')
                ->join('sinren_db.sinren_divisions', 'control_divisions.division_id', '=', 'sinren_divisions.division_id')
                ->select(\DB::raw('control_divisions.id AS id, control_divisions.user_id AS user_id, control_divisions.division_id AS division_id, sinren_divisions.division_name AS division_name'))
                ->get()
        ;
        // 部署情報の取得
        $divs     = \App\Division::orderBy('division_id', 'asc')->get();
//        dd($divs);
        return view('roster.admin.user.index', ['users' => $users->paginate(25), 'controls' => $controls, 'divs' => $divs, 'params' => $params]);
    }

    private function selectRosterUsers($params, $users) {
        if (isset($params['user_state']))
        {
            if ($params['user_state'] == '1')
            {
                $users->where('roster_users.is_administrator', '<>', true)
                        ->where('roster_users.is_chief', '<>', true)
                        ->where('roster_users.is_proxy', '<>', true)
                ;
            }
            elseif ($params['user_state'] == '2')
            {
                $users->where('roster_users.is_chief', '=', true);
            }
            elseif ($params['user_state'] == '3')
            {
                $users->where('roster_users.is_proxy', '=', true);
            }
            elseif ($params['user_state'] == '4')
            {
                $users->whereNull('roster_users.is_administrator');
            }
        }
        if (!empty($params['last_name']))
        {
            $users->where('users.last_name', 'like', "%{$params['last_name']}%");
        }
        if (!empty($params['division_id']))
        {
            $users->where('sinren_users.division_id', '=', $params['division_id']);
        }
        return $users;
    }

    public function showAdmin($id) {
        // ユーザーが存在しなければエラー
        if (empty(\App\user::find($id)))
        {
            \Session::flash('warn_message', 'ユーザーが存在しません。');
            return redirect()->route('permission_error');
        }

        // 勤怠管理ユーザー情報が登録されていなければ登録ページへリダイレクト
        $u = \App\RosterUser::user($id)->first();
        if (empty($u))
        {
            \Session::flash('warn_message', '先にユーザー登録をしてください。');
            return redirect()->route('app::roster::user::show', ['id' => $id]);
        }

        // ユーザー情報の取得
        $user     = \DB::connection('mysql_roster')
                ->table('roster_users')
                ->leftJoin('sinren_db.sinren_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->leftJoin('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->leftJoin('laravel_db.users', 'roster_users.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->first()
        ;
//        dd($user);
        // 管轄部署情報の取得
        $controls = \DB::connection('mysql_sinren')
                ->table('control_divisions')
                ->join('sinren_db.sinren_divisions', 'control_divisions.division_id', '=', 'sinren_divisions.division_id')
                ->select(\DB::raw('control_divisions.id AS id, control_divisions.user_id AS user_id, control_divisions.division_id AS division_id, sinren_divisions.division_name AS division_name'))
                ->where('user_id', '=', $id)
                ->get()
        ;
        $divs     = \App\Division::orderBy('division_id', 'asc')->get();
        return view('roster.admin.user.detail', ['user' => $user, 'controls' => $controls, 'divs' => $divs, 'id' => $id]);
    }

    public function editAdmin(AdminChief $request, $user_id) {
        try {
            $user = \App\RosterUser::where('user_id', '=', $user_id)->firstOrFail();
        } catch (\Exception $e) {
            \Session::flash('warn_message', 'ユーザーが登録されていないようです。');
            return back();
        }
        if ($request['is_chief'] || $request['is_proxy'])
        {
            foreach ($request['control_division'] as $div) {
                if (empty($div))
                {
                    continue;
                }
                $ctrl              = \App\ControlDivision::firstOrNew(['user_id' => $user_id, 'division_id' => $div]);
                $ctrl->user_id     = $user_id;
                $ctrl->division_id = $div;
                $ctrl->save();
            }
        }
        else
        {
            $ctrls = \App\ControlDivision::where('user_id', '=', $user_id)->get();
            foreach ($ctrls as $ctrl) {
                $ctrl->delete();
            }
        }

        $user->is_chief        = (int) $request['is_chief'];
        $user->is_proxy        = (int) $request['is_proxy'];
        $user->is_proxy_active = (int) $request['is_proxy_active'];
        $user->save();

        \Session::flash('success_message', 'ユーザーの更新が完了しました。');
        return redirect(route('admin::roster::user::index'));
    }

    public function deleteAdmin($id) {
        $validate = \Validator::make(['id' => $id], ['id' => 'required|exists:mysql_sinren.control_divisions,id']);
        if ($validate->fails())
        {
            return back()->withErrors($validate);
        }
        $ctrl = \App\ControlDivision::find($id);

        $ctrl->delete();
        \Session::flash('success_message', '管轄店舗を削除しました。');
        return back();
    }

}
