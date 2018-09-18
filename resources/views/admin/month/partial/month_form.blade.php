@if($rows->isEmpty())
<div class="alert alert-danger" role="alert">
  <p><b>月別IDが登録されていないようです。</b></p>
  <p>先に新規ID作成から月別IDを作成してください。</p>
</div>
@else
<table class="table va-middle table-small">
  <thead>
    <tr>
      <th class="bg-primary">状態</th>
      <th class="bg-primary">月別ID</th>
      <th class="bg-primary"><p>処理数 / CSVファイル存在数</p><p>/ データ件数</p></th>
      <th class="bg-primary">データ範囲</th>
      <th class="bg-primary"></th>
    </tr>
  </thead>
  <tbody>
    @foreach($rows as $row)
    <tr>
      <td>@if($row->is_current == true) <span class="label label-warning"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> 公開中</span> @endif</td>
      <td>
        <p>{{$row->monthly_id}}</p>
        @if($counts[$row->id] > 0)
        <p><a href="{{route('admin::super::month::status', ['id'=>$row->monthly_id])}}"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> 詳細を見る</a></p>
        @endif
      </td>

      <td class="text-right">
        <p>
          {{number_format($counts[$row->id]['import'])}}件 / 
          {{number_format($counts[$row->id]['exist'])}}件
        </p>
        <p> / {{number_format($counts[$row->id]['all'])}}件
        </p>
      </td>

      <td>{{date('Y年n月', strtotime($row->monthly_id.'01'))}}1日 ～ {{date('Y年n月t日', strtotime($row->monthly_id.'01'))}}</td>
      <td>
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin::super::month::publish',['id'=>$row->id])}}" style="margin-bottom: 0;">
          {{-- CSRF対策--}}
          <input type="hidden" name="_token" value="{{ csrf_token() }}">

          <div {{-- style="margin-bottom: 5px;" --}}>
            @if($row->is_current == true)
            <button type="submit" class="btn btn-warning btn-sm" disabled="" style="min-width: 250px;">公開中</button>
            @else
            <div style="margin-bottom: 5px;">
              <div class="btn-group">
                <a
                href="{{route('admin::super::month::consignor::show', ['id'=>$row->monthly_id])}}"
                class="btn btn-primary btn-sm"
                data-toggle="tooltip"
                title="月次処理後に委託者マスタを更新します。先に月次処理を行ってください。"
                style="width: 110px;"
                >委託者マスタ生成</a>
                <span></span>
                <button type="submit"
                class="btn btn-primary btn-sm"
                data-toggle="tooltip"
                title="指定した月基準で推進支援システムのデータを作成します。"
                style="width: 80px;"
                >公開する</button>
                <span></span>
                <a href="{{route('admin::super::term::delete_list', ['id'=>$row->monthly_id])}}" class="btn btn-danger btn-sm" style="width: 80px;">削除</a>
              </div>
            </div>
            <div>
              <div class="btn-group">
                <a href="{{route('admin::super::term::files_show', ['term_status'=>'daily',   'id'=>$row->monthly_id])}}" class="btn btn-primary btn-sm" style="width: 90px;">日次</a>
                <span></span>
                <a href="{{route('admin::super::term::files_show', ['term_status'=>'weekly',  'id'=>$row->monthly_id])}}" class="btn btn-primary btn-sm" style="width: 90px;">週次</a>
                <span></span>
                <a 
                href="{{route('admin::super::term::files_show', ['term_status'=>'monthly', 'id'=>$row->monthly_id])}}"
                class="btn btn-success btn-sm"
                data-toggle="tooltip"
                title="月次のみ月末時点でのデータになるため、最新のファイルをセットアップしたい場合、前月のIDをから選択する必要があります。"
                style="width: 90px;">月次</a>
                <span></span>
              </div>
            </div>
            @endif
          </div>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endif