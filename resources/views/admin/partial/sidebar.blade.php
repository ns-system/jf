<div class="nav nav-pills nav-stacked list-group">

    <?php $auth_user = \Auth::user(); ?>
    <p role="presentation" class="list-group-item collapse bg-primary-important" style="color: #fff;">メニューOLD</p>

    @if($auth_user->SuisinUser && $auth_user->SuisinUser->is_administrator || $auth_user->is_super_user)
    <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#suisin"><span class="caret"></span> 推<small>進支援システム</small></a>
    <div class="collapse" id="suisin">@include('admin.partial.suisin_bar')</div>
    @endif

{{--     @if($auth_user->RosterUser && $auth_user->RosterUser->is_administrator || $auth_user->is_super_user) --}}
    <span></span>
    @if(\App\RosterUser::user($auth_user->id)->exists() && \App\RosterUser::user($auth_user->id)->first()->is_administrator || $auth_user->is_super_user)
    <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#roster""><span class="caret"></span> 勤<small>怠管理システム</small></a>
    <div class="collapse" id="roster">@include('roster.admin.partial.roster_side')</div>
    @endif


    @if($auth_user->is_super_user)
    <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#system" style="border-top: 3px solid #e0e0e0;"><span class="caret"></span> シ<small>ステム設定</small></a>
        <span></span>

    <div class="collapse" id="system">
        <a role="presentation" class="list-group-item collapse list-second" href="{{route('admin::super::month::show')}}">公開年月・処理状況確認</a>
    <span></span>
        <a role="presentation" class="list-group-item collapse list-second" href="{{route('admin::super::user::show')}}">ユーザー権限変更</a>
    <span></span>
    {{--     <a role="presentation" class="list-group-item collapse" href="{{route('admin::super::user::show')}}">処理状況確認</a> --}}


        <a role="presentation" class="list-group-item collapse list-second" data-toggle="collapse" href="#config"><span class="caret"></span> 設定ファイル</a>
        <div class="collapse" id="config">

            <a
                role="presentation"
                class="list-group-item collapse list-second"
                href="{{route('admin::super::config::index', ['system'=>'Admin','category'=>'ZenonCsv'])}}"
                data-toggle="tooltip"
                title="全オンセンターから還元されるCSVデータの登録を行います。"
                data-placement="right">全オン還元CSVファイル設定</a>
    <span></span>

            <a
                role="presentation"
                class="list-group-item collapse list-second"
                href="{{route('admin::super::config::index', ['system'=>'Admin','category'=>'ZenonType'])}}"
                data-toggle="tooltip"
                title="全オン還元データのフォーマットの変更を行います。"
                data-placement="right">全オンカテゴリ名</a>
    <span></span>

            <a
                role="presentation"
                class="list-group-item collapse list-second"
                href="{{route('admin::super::config::index', ['system'=>'Admin','category'=>'ZenonTable'])}}"
                data-toggle="tooltip"
                title="全オン還元情報のMySQL側のカラム設定を行います。"
                data-placement="right">MySQL全オンテーブル設定</a>
    <span></span>

        </div>
    </div>
    @endif

</div>