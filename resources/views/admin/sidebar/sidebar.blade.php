<div class="nav nav-pills nav-stacked list-group" id="accordion">

    <?php $auth_user = \Auth::user(); ?>

    <p role="presentation" class="list-group-item collapse bg-primary-important" style="color: #fff;">メニュー</p>

    @if($auth_user->SuisinUser && $auth_user->SuisinUser->is_administrator || $auth_user->is_super_user)
    <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#1_suisin"><span class="caret"></span> 推<small>進支援システム</small></a>{{-- <span></span> --}}
    <div class="collapse" id="1_suisin">@include('admin.sidebar.suisin')</div>
    @endif

{{--     @if($auth_user->RosterUser && $auth_user->RosterUser->is_administrator || $auth_user->is_super_user) --}}
    <span></span>
    @if(\App\RosterUser::user($auth_user->id)->exists() && \App\RosterUser::user($auth_user->id)->first()->is_administrator || $auth_user->is_super_user)
        <span class="list-group-item collapse list-divider"></span>
        <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#2_roster"><span class="caret"></span> 勤<small>怠管理システム</small></a>
        <div class="collapse" id="2_roster">@include('admin.sidebar.roster')</div>
    @endif


    @if($auth_user->is_super_user)
        <span class="list-group-item collapse list-divider"></span>
        <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#3_system"><span class="caret"></span> シ<small>ステム設定</small></a>
        <div class="collapse" id="3_system">
            <a
                role="presentation"
                class="list-group-item collapse list-second"
                href="{{route('admin::super::month::show')}}"
                data-toggle="tooltip"
                data-placement="right"
                title="（スーパーユーザー用機能）公開する年月およびその処理状況を確認できます。">
                <span class="text-danger"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> 年月・処理状況確認</span>
            </a><span></span>
            
            <a
                role="presentation"
                class="list-group-item collapse list-second"
                href="{{route('admin::super::user::show')}}"
                data-toggle="tooltip"
                data-placement="right"
                title="（スーパーユーザー用機能）一般ユーザーと管理ユーザーの昇格・降格などが行えます。">
                <span class="text-danger"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> ユーザー権限変更</span></a>
        <span></span>
    {{--     <a role="presentation" class="list-group-item collapse" href="{{route('admin::super::user::show')}}">処理状況確認</a> --}}


        <a role="presentation" class="list-group-item collapse list-second" data-toggle="collapse" href="#4_config"><span class="caret"></span> 設定ファイル</a>
        <div class="collapse" id="4_config">

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