<?php
if(\Auth::check())
{
    $bar_user = \App\User::leftJoin('sinren_db.sinren_users as SINREN_USR','users.id','=','SINREN_USR.user_id')
        ->leftJoin('roster_db.roster_users as ROSTER_USR','users.id','=','ROSTER_USR.user_id')
        ->select(\DB::raw('users.id, users.is_super_user, SINREN_USR.division_id, ROSTER_USR.is_administrator, ROSTER_USR.is_chief'))
        ->where('users.id','=',\Auth::user()->id)
        ->first()
    ;
}
else
{
    $bar_user = null;
}
?>

@if(!empty($bar_user) && ($bar_user->is_super_user || $bar_user->is_administrator))
    @include('admin.sidebar.sidebar')
@else
    @include('app.sidebar._sidebar')
@endif