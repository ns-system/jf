<a
role="presentation"
class="list-group-item collapse list-second"
href="{{route('admin::roster::csv::index')}}"
data-toggle="tooltip"
title="登録データを給与奉行で取り込める形に出力します。"
data-placement="right">CSVファイル出力</a>

<a
role="presentation"
class="list-group-item collapse list-second"
href="{{route('admin::roster::user::index')}}"
data-toggle="tooltip"
title="責任者ユーザーの選任他、ユーザーの管理を行います。"
data-placement="right">ユーザー権限変更</a>

<a
role="presentation"
class="list-group-item collapse list-second"
href="/notifications"
data-toggle="tooltip"
data-placement="right"
title="お知らせを追加することができます。">お知らせ作成</a>
<span></span>

<a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#2-1_roster_master"><span class="caret"></span> <small>マスタファイル</small></a>
<div class="collapse" id="2-1_roster_master">
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
  data-placement="right">祝日マスタ</a><span></span>

  <a
  role="presentation"
  class="list-group-item collapse list-second"
  href="{{route('admin::roster::index', ['system'=>'Roster','category'=>'RosterUser'])}}"
  data-toggle="tooltip"
  title="社員番号の設定を行います。"
  data-placement="right">社員番号</a><span></span>
</div>

