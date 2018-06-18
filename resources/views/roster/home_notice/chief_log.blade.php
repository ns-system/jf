@if(!empty($roster_log) && !$roster_log->isEmpty())
<div class="panel panel-primary">
  <div class="panel-heading">
    <h4>アクティビティログ</h4>
  </div>
  <div class="panel-body" style="padding: 0;">
    <div id="roster-log" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner" role="listbox">
        @foreach($roster_log as $i => $chunk)
        <div class="item @if($i == 0) active @endif">
          @foreach($chunk as $r)
          <div style="border-bottom: 1px solid #ddd; padding: 5px 20px;" class="small">
            <p><span class="label label-info">{{ date('n/j H:i', strtotime($r->timestamp)) }}</span></p>
            <p>{{ $r->last_name }} {{ $r->first_name }} さんが情報を更新しました。</p>
          </div>
          @endforeach
        </div>
        @endforeach
      </div>
      <a class="left carousel-control" href="#roster-log" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">前へ</span>
      </a>
      <a class="right carousel-control" href="#roster-log" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">次へ</span>
      </a>
    </div>
  </div>
</div>
@endif