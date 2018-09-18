<?php $exist = false; ?>
<div class="panel panel-primary">
  <div class="panel-heading"><h4>勤怠管理情報 <small> - 責任者</small></h4></div>
  <div class="panel-body">

    @foreach($roster_chief_cnt as $div_id => $div)
    @foreach($div['count'] as $monthly)
    @if(!empty($monthly['month_id']))
    <?php $exist = true; ?>

    <p>
      <div class="row">
        <div class="col-md-offset-1 col-md-10">
          <a href="{{ route('app::roster::accept::calendar', ['ym'=>$monthly['month_id'], 'div'=>$div_id]) }}" class="btn btn-primary btn-block">
            {{ $div['division_name'] }}
            <span class="badge">未承認：{{ date('n月', strtotime($monthly['month_id'].'01')) }} / {{ $monthly['total'] }}件</span>
          </a>
        </div>
      </div>
    </p>
    @endif
    @endforeach
    @endforeach
    @if(!$exist) <p>未承認データはありません。</p> @endif
  </div>
</div>