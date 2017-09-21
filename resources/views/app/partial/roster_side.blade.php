    <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#roster"><span class="caret"></span> 勤怠管理システム</a>
    <div class="collapse" id="roster">


        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('app::roster::calendar::show', ['id'=>date('Ym')])}}"
            data-toggle="tooltip"
            title="勤務表をカレンダーから入力します。"
            data-placement="right">カレンダー</a>

        <a
            role="presentation"
            class="list-group-item collapse list-second"
            @if(\App\SinrenUser::user()->first() != null)
            href="{{route('app::roster::division::index', ['div'=>\App\SinrenUser::user()->first()->division_id])}}"
            @else
            href="{{route('app::roster::user::show')}}"
            @endif
            data-toggle="tooltip"
            title="全オンセンターから還元されるCSVデータの登録を行います。"
            data-placement="right">部署内勤務リスト</a>


        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('app::roster::user::show')}}"
            data-toggle="tooltip"
            title="全オン還元情報のMySQL側のカラム設定を行います。"
            data-placement="right">ユーザー情報変更</a>

    </div>