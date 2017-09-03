<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ChiefController extends Controller
{

    public function show() {
//        $div   = \Auth::user()->SinrenUser->division_id;
        $ctl_divs = \App\ControlDivision::where('user_id', \Auth::user()->id)->get();
//        var_dump($ctl_divs);
//        $users = \App\SinrenUser::division($div)->get();
//        foreach ($users as $u) {
//            var_dump($u->User->name);
//        }

        $rows = [];
        foreach ($ctl_divs as $div) {
//            var_dump($div->division_id);
            $users  = \App\SinrenUser::division($div->division_id)
                    ->where('user_id', '!=', \Auth::user()->id)
                    ->orderBy('user_id', 'asc')
                    ->get()
            ;
            $rows[] = [
                'division_name' => $div->Division->division_name,
                'users'         => $users,
            ];
        }
//        var_dump($rows);
        return view('roster.chief.proxy', ['rows' => $rows]);
//        var_dump($users);
    }

    public function edit() {
        $id = \Request::query()['id'];
        $in = \Input::all();
//        var_dump($id);
//        var_dump($in);

        $input = [
            'is_proxy'        => (int) $in['is_proxy'][$id],
            'is_proxy_active' => (int) $in['is_proxy_active'][$id],
        ];


        \DB::connection('mysql_roster')->transaction(function() use ($id, $input) {
            $roster_user = \App\RosterUser::where('user_id', $id)->first();

            $roster_user->is_proxy        = $input['is_proxy'];
            $roster_user->is_proxy_active = $input['is_proxy_active'];
            $roster_user->save();
        });
//        var_dump($input);
//        exit();
        \Session::flash('flash_message', \App\User::find($id)->name.'さんを変更しました。');
        return \Redirect('/roster/chief/proxy');
    }

}
