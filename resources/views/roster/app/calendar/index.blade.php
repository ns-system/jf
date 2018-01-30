@extends('layout')

@section('title', '勤怠管理')

@section('header')
@parent
@section('brand', '勤怠管理システム')

<style type="text/css">
.calendar th,
.calendar td{
    border: none;
}
/*    .small{ font-weight: bolder; }*/
</style>
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('partial.check_sidebar')
</div>
<div class="col-md-10">
    <h2 style="margin: 10px;">
        <nav style="display: inline-block;">
            <ul class="pager" style="margin: 0; text-align: left;">
                <li style=" font-size: 16px;">
                    <a href="{{(route('app::roster::calendar::show', ['ym' => $prev]))}}">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> {{date('Y年n月', strtotime($prev.'01'))}}
                    </a>
                </li>
                <span><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> {{date('Y年n月', strtotime($ym.'01'))}} 勤怠管理カレンダー</span>
                <li style=" font-size: 16px;">
                    <a href="{{(route('app::roster::calendar::show', ['ym' => $next]))}}">
                        {{date('Y年n月', strtotime($next.'01'))}} <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    </a>
                </li>
            </ul>
        </nav>
    </h2>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="well well-sm">
                <small>
                    <p>責任者が作成した勤務予定データに対して実績を入力してください。</p>
                    <p>この画面から残業申請を上げることもできます。</p>
                </small>
            </div>
            
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="col-md-12">
    <div class="container-fluid">
        @include('partial.alert')

        <table class="calendar">
            <thead>
                <tr>
                    <th width="14.286%" class="text-danger">日</th>
                    <th width="14.286%">月</th>
                    <th width="14.286%">火</th>
                    <th width="14.286%">水</th>
                    <th width="14.286%">木</th>
                    <th width="14.286%">金</th>
                    <th width="14.286%" class="text-info">土</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calendars as $i => $day)
                @if($day['week'] == 0) <tr> @endif
                    <td>
                        @if($day['day'] != 0)
                        <?php $r = $day['data'] != [] ? $day['data'] : null; ?>
                        <div class="panel panel-primary" style="margin: 2px;">
                            <div class="panel-heading text-left" style="padding: 5px 10px;">
                                {{-- 日付 --}}
                                <strong
                                @if($day['week'] == 6)                             class="text-info-light"
                                @elseif($day['week'] == 0 || $day['holiday'] == 1) class="text-danger-light"
                                @endif
                                data-toggle="tooltip"
                                title="{{$day['holiday_name']}}" 
                                >{{$day['day']}}</strong>
                                {{-- 日付 --}}

                                {{-- モーダル呼び出しボタン --}}
                                <div class="btn-group">
                                    @if(!empty($r))
                                    <button
                                    type="button"
                                    class="btn btn-primary btn-xs"
                                    data-toggle="modal"
                                    data-target="#plan-{{$r->id}}"
                                    @if(!empty($r->is_plan_entry))
                                    @if($r->is_plan_accept)     disabled><span class=" text-success"
                                    @elseif($r->is_plan_reject)         ><span class=" text-danger"
                                    @else                               ><span class=" text-warning" @endif
                                    @else ><span @endif
                                    >予定</span>
                                </button>
                                <button
                                type="button"
                                class="btn btn-primary btn-xs"
                                @if(!empty($r->is_plan_entry))
                                data-toggle="modal"
                                data-target="#actual-{{$r->id}}"
                                @if(!empty($r->is_actual_entry))
                                @if($r->is_actual_accept)      disabled><span class=" text-success"
                                @elseif($r->is_actual_reject)          ><span class=" text-danger"
                                @else                                  ><span class=" text-warning" @endif
                                @else ><span @endif
                                @else onclick="alert('先に予定を登録してください。');"><span
                                @endif
                                >実績</span>
                            </button>
                            <a
                            href="{{route('app::roster::calendar::form::delete', ['id'=>$r->id])}}"
                            class="btn btn-primary btn-xs"
                            @if(!$r->is_plan_entry && !$r->is_actual_entry)     disabled onclick="return false;"
                            @elseif($r->is_plan_accept || $r->is_actual_accept) disabled onclick="alert('承認されているため、削除は行えません。'); return false;"
                            @else                                               onclick="return confirm('予定・実績データが削除されますが本当によろしいですか？');" @endif
                            ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>　</a>
                            @else
                            <button type="button" class="btn btn-primary btn-xs" disabled>予定</button>
                            <button type="button" class="btn btn-primary btn-xs" disabled>実績</button>
                            <button type="button" class="btn btn-primary btn-xs" disabled>　<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                            @endif
                        </div>
                        {{-- モーダル呼び出しボタン --}}

                        {{-- モーダルフォーム --}}
                        @include('roster.app.calendar.partial.plan_form')
                        @include('roster.app.calendar.partial.actual_form')
                        {{-- モーダルフォーム --}}

                    </div>

                    <div
                    @if(date('Ymd', strtotime($ym.sprintf('%02d', (int) $day['day']))) == date('Ymd')) class="panel-body bg-info"
                    @else class="panel-body" @endif
                    style="height: 175px; overflow-y: scroll; padding: 5px;"
                    >
                    {{-- パネル内容 --}}
                    @if($day['data'] != [])
                    <div class="text-left">
                        {{-- Plan --}}
                        @if($r->is_plan_entry)
                        @if($r->is_plan_reject)     <span class="label label-danger">予定</span>
                        @elseif($r->is_plan_accept) <span class="label label-success">予定</span>
                        @else                       <span class="label label-warning">予定</span> @endif
                        @else <span class="label label-default">予定</span> @endif
                        @if(!empty($r->plan_work_type_id))      <p class="small">{{$types[$r->plan_work_type_id]['name']}} {{$types[$r->plan_work_type_id]['time']}}</p> @endif
                        @if(!empty($r->plan_overtime_start_time) &&
                        !empty($r->plan_overtime_end_time))     <p class="small">{{date('G:i', strtotime($r->plan_overtime_start_time))}} ～ {{date('G:i', strtotime($r->plan_overtime_end_time))}}</p> @endif
                        @if(!empty($r->plan_rest_reason_id))        <p class="small">{{$rests[$r->plan_rest_reason_id]}}</p> @endif
                        @if(!empty($r->plan_overtime_reason))       <p class="small">{{$r->plan_overtime_reason}}</p> @endif

                        {{-- Actual --}}
                        @if($r->is_actual_entry)
                        @if($r->is_actual_reject)     <span class="label label-danger">実績</span>
                        @elseif($r->is_actual_accept) <span class="label label-success">実績</span>
                        @else                         <span class="label label-warning">実績</span> @endif
                        @if(!empty($r->actual_work_type_id))      <p class="small">{{$types[$r->actual_work_type_id]['name']}} {{$types[$r->actual_work_type_id]['time']}}</p> @endif
                        @if(!empty($r->actual_overtime_start_time) &&
                        !empty($r->actual_overtime_end_time))     <p class="small">{{date('G:i',strtotime($r->actual_overtime_start_time))}} ～ {{date('G:i', strtotime($r->actual_overtime_end_time))}}</p> @endif
                        @if(!empty($r->actual_rest_reason_id))        <p class="small">{{$rests[$r->actual_rest_reason_id]}}</p> @endif
                        @if(!empty($r->actual_overtime_reason))       <p class="small">{{$r->actual_overtime_reason}}</p> @endif
                        @endif
                    </div>
                    @endif
                    {{-- パネル内容 --}}
                </div>
            </div>
            <?php unset($r); ?>
            @endif
        </td>
    @if($day['week'] == 6) </tr> @endif
    @endforeach
</tbody>
</table>

</div>
</div>

@endsection

@section('footer')
@parent
<script type="text/javascript">
    $(function () {
        $('#fix').click(function () {
            $('.fix-or-wide').each(function () {
                $(this).css('min-width', 'initial');
            });
        });
        $('#wide').click(function () {
            $('.fix-or-wide').each(function () {
                $(this).css('min-width', '250px');
            });
        });

        $('.clear-time').click(function(){
            $(this).parent().children('*[data-toggle="clear"]').each(function(){
                $(this).children('option').attr('selected', false);
            });
        });
    })
</script>
@endsection