    <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#config"><span class="caret"></span> 勤怠管理システム</a>
    <div class="collapse" id="config">

        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('index', ['system'=>'Admin','category'=>'ZenonCsv'])}}"
            data-toggle="tooltip"
            title="全オンセンターから還元されるCSVデータの登録を行います。"
            data-placement="right">ホーム</a>

        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('app::roster::calendar::show', ['id'=>date('Ym')])}}"
            data-toggle="tooltip"
            title="全オン還元データのフォーマットの変更を行います。"
            data-placement="right">カレンダー</a>

        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('app::roster::user::show')}}"
            data-toggle="tooltip"
            title="全オン還元情報のMySQL側のカラム設定を行います。"
            data-placement="right">MySQL全オンテーブル設定</a>

    </div>