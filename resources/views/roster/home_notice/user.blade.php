@if(!empty($roster_user_cnt) && !$roster_user_cnt->isEmpty())
<div class="col-md-6">
    <div class="panel panel-primary">
        <div class="panel-heading"><h4>勤怠管理情報</h4></div>
        <div class="panel-body">

            @foreach($roster_user_cnt as $cnt)
            <div class="col-md-offset-1 col-md-10">
                <p>
                    <a href="{{route('app::roster::calendar::show', ['ym'=>$cnt->month_id])}}" class="btn btn-primary btn-sm btn-block">{{date('n月', strtotime($cnt->month_id.'01'))}}</a>
                    予定未入力が<b class="text-warning">{{$cnt->plan_total}}件</b>、
                    実績未入力が <b class="text-warning">{{$cnt->actual_total}}件</b>あります。
                </p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif