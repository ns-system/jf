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
        $divs = \App\ControlDivision::user()->get();
        $rows = \DB::connection('mysql_sinren')
                ->table('sinren_users')
                ->join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                ->join('laravel_db.users', 'sinren_users.user_id', '=', 'users.id')
                ->join('roster_db.roster_users', 'sinren_users.user_id', '=', 'roster_users.user_id')
                ->select(\DB::raw('*, roster_users.id as key_id, roster_users.created_at as create_time, roster_users.updated_at as update_time'))
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
    public function update(Chief $request) {
        $in   = $request->input();
        $user = \App\RosterUser::find($in['id']);

        $user->is_proxy        = $in['proxy'];
        $user->is_proxy_active = $in['active'];
        $user->save();
        
        \Session::flash('success_message', "データを更新しました。");
        return redirect(route('app::roster::chief::index'));
    }

}
