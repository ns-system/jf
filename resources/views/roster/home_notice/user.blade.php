<div class="panel panel-primary">
  <div class="panel-heading"><h4>勤怠管理情報 <small> - 一般</small></h4></div>
  @if(!empty($roster_user_cnt) && !$roster_user_cnt->isEmpty())
  <table class="table va-middle">
{{--     <thead>
      <tr>
        <th>年月</th>
        <th class="text-left">内容</th>
      </tr>
    </thead> --}}
    <tbody>
      @foreach($roster_user_cnt as $cnt)
      <tr>
        <th>
          <a href="{{route('app::roster::calendar::show', ['ym'=>$cnt->month_id])}}" class="btn btn-primary btn-sm btn-block">{{date('n月', strtotime($cnt->month_id.'01'))}}</a>
        </th>
        <td class="text-left">
          <p>予定未入力が<b class="text-warning">{{$cnt->plan_total}}件</b>、</p>
          <p>実績未入力が <b class="text-warning">{{$cnt->actual_total}}件</b>あります。</p>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div class="panel-body">
    <p>新しいお知らせはありません。</p>
  </div>
  @endif
</div>
