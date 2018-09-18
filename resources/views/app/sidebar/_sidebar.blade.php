<div class="nav nav-pills nav-stacked list-group" id="accordion">

    <?php $auth_user = \Auth::user(); ?>

    <p role="presentation" class="list-group-item collapse bg-primary-important" style="color: #fff;">メニュー</p>

    @if($auth_user->SuisinUser)
    <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#1_suisin"><span class="caret"></span> 推<small>進支援システム</small></a><span></span>
    <div class="collapse" id="1_suisin">@include('app.sidebar.suisin')</div>
    @endif

    <span></span>
{{--     @if(\App\RosterUser::user()->exists()) --}}
        <span class="list-group-item collapse list-divider"></span>
        <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#2_roster"><span class="caret"></span> 勤<small>怠管理システム</small></a>
        <div class="collapse" id="2_roster">@include('app.sidebar.roster')</div>
{{--     @endif --}}

    <span class="list-group-item collapse list-divider"></span>
    <a role="presentation" class="list-group-item collapse list-second" href="{{route('app::user::show', ['id'=>\Auth::user()->id])}}">ユーザー情報変更</a><span></span>

    <a role="presentation" class="list-group-item collapse list-second" href="/auth/logout">ログアウト</a>
</div>