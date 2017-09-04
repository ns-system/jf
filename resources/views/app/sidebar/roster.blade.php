{{--         <a role="presentation" class="list-group-item collapse list-second" data-toggle="collapse" href="#roster"><span class="caret"></span> 設定ファイル</a> --}}

<a
    role="presentation"
    class="list-group-item collapse list-second"
    href="{{route('app::roster::calendar::show', ['ym'=> date('Ym')])}}"
    data-toggle="tooltip"
    title="カレンダーから勤務表を入力します。"
    data-placement="right">カレンダー表示</a><span></span>

<a
    role="presentation"
    class="list-group-item collapse list-second"
    href="{{route('app::roster::division::check')}}"
{{--     @if(\App\SinrenUser::user()->first() != null)
    href="{{route('app::roster::division::index', ['div'=>\App\SinrenUser::user()->first()->division_id])}}"
    @else
    href="{{route('app::roster::user::show')}}"
    @endif --}}
    data-toggle="tooltip"
    title="部署内の勤務状況を確認します。"
    data-placement="right">部署内勤務データ確認</a><span></span>

<?php $s_user = \App\RosterUser::user()->first(); ?>
@if($s_user != null)
@if($s_user->is_chief || ($s_user->is_proxy && $s_user->is_proxy_active))
<span class="list-group-item collapse list-divider"></span>
<a
    role="presentation"
    class="list-group-item collapse list-second"
    href="{{route('app::roster::accept::index')}}"
    data-toggle="tooltip"
    title="（責任者・責任者代理用機能）勤務データの承認・却下を行います。"
    data-placement="right"><span class="text-success"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> 勤務データ承認</span>
</a><span></span>
@endif

@if($s_user->is_chief)
<a
    role="presentation"
    class="list-group-item collapse list-second"
    href="{{route('app::roster::chief::index')}}"
    data-toggle="tooltip"
    title="（責任者用機能）責任者代理の選任・有効化／無効化を設定します。"
    data-placement="right"><span class="text-success"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> 責任者代理設定</span>
</a><span></span>
@endif
@endif
