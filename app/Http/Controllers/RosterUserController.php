<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Roster\RosterUser;
use App\Http\Requests\Roster\AdminChief;
use App\Http\Controllers\Controller;

class RosterUserController extends Controller
{

    public function index() {
        $id     = \Auth::user()->id;
//        $user   = \App\RosterUser::where('user_id', '=', $id)->get();
        $divs   = \App\Division::orderBy('division_id', 'asc')->get();
        $types  = \App\WorkType::orderBy('work_type_id', 'asc')->get();
        $suisin = \App\SinrenUser::where('user_id', '=', $id);
        $roster = \App\RosterUser::where('user_id', '=', $id);
//        var_dump($suisin);

        if (!$suisin->exists())
        {
            $div_id = '';
        }
        else
        {
            $div_id = $suisin->first()->division_id;
        }

        if (!$roster->exists())
        {
            $type_id = '';
        }
        else
        {
            $type_id = $roster->first()->work_type_id;
        }

//        var_dump($type_id);


        $params = [
            'id'      => $id,
            'divs'    => $divs,
            'types'   => $types,
            'div_id'  => $div_id,
            'type_id' => $type_id,
///            'warn_message' => $warn_message,
        ];
        return view('roster.app.user.index', $params);
    }

    public function edit($id, RosterUser $request) {
        $roster = \App\RosterUser::firstOrNew(['user_id' => $id]);
        $sinren = \App\SinrenUser::firstOrNew(['user_id' => $id]);

        $sinren->user_id     = $id;
        $sinren->division_id = $request['division_id'];
        $sinren->save();

        $roster->user_id      = $id;
        $roster->work_type_id = $request['work_type_id'];
        $roster->save();
        \Session::flash('flash_message', 'ユーザーの更新が完了しました。');
        return back();
    }

    public function indexAdmin() {
        $users = \DB::connection('mysql_roster')
                ->table('roster_users')
                ->join('sinren_data_db.sinren_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->join('sinren_data_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->join('laravel_db.users', 'roster_users.user_id', '=', 'users.id')
                ->orderBy('sinren_divisions.division_id', 'asc')
                ->orderBy('users.id', 'asc')
        ;
//        var_dump($users->first());

        $controls = \DB::connection('mysql_sinren')
                ->table('control_divisions')
                ->join('sinren_data_db.sinren_divisions', 'control_divisions.division_id', '=', 'sinren_divisions.division_id')
                ->select(\DB::raw('control_divisions.id AS id, control_divisions.user_id AS user_id, control_divisions.division_id AS division_id, sinren_divisions.division_name AS division_name'))
                ->get()
//                ->toArray()
        ;
//        var_dump($controls);

        return view('roster.admin.user.index', ['users' => $users->paginate(25), 'controls' => $controls]);
    }

    public function showAdmin($id) {
//        echo $id;
        $user = \DB::connection('mysql_roster')
                ->table('roster_users')
                ->join('sinren_data_db.sinren_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->join('sinren_data_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->join('laravel_db.users', 'roster_users.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->first()
        ;
//        var_dump($user);

        $controls = \DB::connection('mysql_sinren')
                ->table('control_divisions')
                ->join('sinren_data_db.sinren_divisions', 'control_divisions.division_id', '=', 'sinren_divisions.division_id')
                ->select(\DB::raw('control_divisions.id AS id, control_divisions.user_id AS user_id, control_divisions.division_id AS division_id, sinren_divisions.division_name AS division_name'))
                ->where('user_id', '=', $id)
                ->get()
        ;
        $divs     = \App\Division::orderBy('division_id', 'asc')->get();
//        var_dump($controls);
//        var_dump($divs);
        return view('roster.admin.user.detail', ['user' => $user, 'controls' => $controls, 'divs' => $divs, 'id' => $id]);
    }

    public function editAdmin(AdminChief $request) {
//        var_dump(\Input::get());
        $id = $request['id'];
        if ($request['is_chief'] || $request['is_proxy'])
        {
            foreach ($request['control_division'] as $div) {
                $ctrl              = \App\ControlDivision::firstOrNew(['user_id' => $id, 'division_id' => $div]);
                $ctrl->user_id     = $id;
                $ctrl->division_id = $div;
                $ctrl->save();
            }
        }
        else
        {
            $ctrls = \App\ControlDivision::where('user_id', '=', $id)->get();
            foreach ($ctrls as $ctrl) {
                $ctrl->delete();
            }
        }

        $user = \App\RosterUser::where('user_id', '=', $id)->first();
        if ($user == null)
        {
            \Session::flash('warn_message', 'ユーザーが登録されていないようです。');
            return back();
        }
        $user->is_chief        = (int) $request['is_chief'];
        $user->is_proxy        = (int) $request['is_proxy'];
        $user->is_proxy_active = (int) $request['is_proxy_active'];
        $user->save();

//        exit();
        \Session::flash('flash_message', 'ユーザーの更新が完了しました。');
        return redirect(route('admin::roster::user::index'));
    }

    public function deleteAdmin($id) {
        $validate = \Validator::make(['id' => $id], ['id' => 'required|exists:mysql_sinren.control_divisions,id']);
        if ($validate->fails())
        {
            
        }
        $ctrl = \App\ControlDivision::find($id);
//        var_dump($ctrl);

        $ctrl->delete();
        \Session::flash('flash_message', '管轄店舗を削除しました。');
        return back();
    }

}
