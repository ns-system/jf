@if(!empty($roster_chief_cnt) && !$roster_chief_cnt->isEmpty())
<div class="col-md-6">
    <div class="panel panel-primary">
        <div class="panel-heading"><h4>勤怠管理情報 <small> - 責任者</small></h4></div>
        <div class="panel-body">

            @foreach($roster_chief_cnt as $cnt)
            @if($cnt->total > 0)
            <p>
                <div class="row">
                    <div class="col-md-offset-1 col-md-10">
                        {{--                         <a href="{{route('app::roster::accept::calendar', ['ym'=>$cnt->month_id, 'div'=>$cnt->division_id, 'all'=>'part'])}}" class="btn btn-primary btn-block"> --}}
                            <a href="{{route('app::roster::accept::calendar', ['ym'=>$cnt->month_id, 'div'=>$cnt->division_id])}}" class="btn btn-primary btn-block">
                                {{$cnt->division_name}}
                                <span class="badge">{{date('n月', strtotime($cnt->month_id.'01'))}} / {{$cnt->total}}件</span>
                            </a>
                        </div>
                    </div>
                </p>
                @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif