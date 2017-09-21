<a
    role="presentation"
    class="list-group-item collapse list-second"
    href="{{route('admin::roster::index', ['system'=>'Roster','category'=>'Holiday'])}}"
    data-toggle="tooltip"
    title="祝日マスタの登録を行います。"
    data-placement="right">CSVファイル出力</a>

<a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#roster-master"><span class="caret"></span> <small>マスタファイル</small></a>
<div class="collapse" id="roster-master">
    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::roster::index', ['system'=>'Roster','category'=>'WorkType'])}}"
        data-toggle="tooltip"
        title="勤務形態マスタの登録を行います。"
        data-placement="right">勤務形態マスタ</a>

    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::roster::index', ['system'=>'Roster','category'=>'Division'])}}"
        data-toggle="tooltip"
        title="部署マスタの登録を行います。"
        data-placement="right">部署マスタ</a>

    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::roster::index', ['system'=>'Roster','category'=>'Rest'])}}"
        data-toggle="tooltip"
        title="休暇マスタの登録を行います。"
        data-placement="right">休暇マスタ</a>

    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::roster::index', ['system'=>'Roster','category'=>'Holiday'])}}"
        data-toggle="tooltip"
        title="祝日マスタの登録を行います。"
        data-placement="right">祝日マスタ</a>
    <span class="list-group-item collapse list-divider"></span>
</div>

