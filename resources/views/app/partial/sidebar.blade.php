<div class="nav nav-pills nav-stacked list-group">

    <?php $auth_user = \Auth::user(); ?>
    <p role="presentation" class="list-group-item collapse bg-primary-important" style="color: #fff;">ユーザーメニュー OLD</p>

    @if(!\App\RosterUser::where('user_id', '=', $auth_user->id)->exists())
    @include('suisin.app.partial.side')
{{--     @else
    noting --}}
    @endif

    @if(!empty($auth_user->RosterUser($auth_user->id)))
    @include('app.partial.roster_side')
    @endif

    <!--{{--     @if($auth_user->RosterUser($auth_user->id) && $auth_user->RosterUser($auth_user->id)->is_administrator || $auth_user->is_super_user)
        <a role="presentation" class="list-group-item collapse" href="/admin/roster/Roster">勤怠管理システム</a>
        @endif --}}-->

    @if($auth_user->is_super_user)
    <a role="presentation" class="list-group-item collapse" href="{{route('admin::month::show')}}">公開年月選択</a>
    <a role="presentation" class="list-group-item collapse" href="{{route('super::show')}}">ユーザー権限変更</a>
    <a role="presentation" class="list-group-item collapse" href="{{route('super::show')}}">処理状況確認</a>


    <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#config"><span class="caret"></span> 設定ファイル</a>
    <div class="collapse" id="config">

        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('admin::index', ['system'=>'Admin','category'=>'ZenonCsv'])}}"
            data-toggle="tooltip"
            title="全オンセンターから還元されるCSVデータの登録を行います。"
            data-placement="right">全オン還元CSVファイル設定</a>

        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('admin::index', ['system'=>'Admin','category'=>'ZenonType'])}}"
            data-toggle="tooltip"
            title="全オン還元データのフォーマットの変更を行います。"
            data-placement="right">全オンカテゴリ名</a>

        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('admin::index', ['system'=>'Admin','category'=>'ZenonTable'])}}"
            data-toggle="tooltip"
            title="全オン還元情報のMySQL側のカラム設定を行います。"
            data-placement="right">MySQL全オンテーブル設定</a>

    </div>
    @endif

</div>