

<?php $r = $day['data'] != [] ? $day['data'] : null; ?>


<div class="panel panel-primary" style="margin: 2px;">
    <div class="panel-heading text-left" style="padding: 5px 10px;">
        <span 
              @if($day['week'] == 6) class="text-info-light"
              @elseif($day['week'] == 0 || $day['holiday'] == 1) class="text-danger-light"
              @endif
              data-toggle="tooltip"
              title="{{$day['holiday_name']}}" 
        >@if(date('Ymd', strtotime($ym.sprintf('%02d', (int) $day['day']))) == date('Ymd'))
            <span class="text-warning">
        @else
            <span>
        @endif
                <strong style="font-weight: bold;">{{$day['day']}}</strong>
            </span>
        </span>

            <div style="display: inline-block; margin: 0;">
                <div class="btn-group">
                    @if(!$r || !$r->is_plan_entry)
                        <a href="{{route('app::roster::calendar::form::index', ['ym' => $ym, 'day' => $day['day']])}}"
                            class="btn btn-primary btn-xs"
                            title="入力されていません。"
                            data-toggle="tooltip"
                            data-placement="top"
                        ><span>予定</span></a>
                    @elseif($r->is_plan_reject == true)
                        <a href="{{route('app::roster::calendar::form::index', ['ym' => $ym, 'day' => $day['day']])}}"
                            class="btn btn-primary btn-xs"
                            @if(!empty($r->reject_reason)) title="却下されました。再入力をお願いします。（理由：{{$r->reject_reason}}）"
                            @else title="却下されました。再入力をお願いします。" @endif
                            data-toggle="tooltip"
                            data-placement="top"
                        ><span class="text-danger">予定</span></a>
                    @elseif($r->is_plan_accept == true)
                        <span class="btn btn-primary btn-xs disabled" title="承認されました。以降の修正は行えません。" data-toggle="tooltip" data-placement="top"><span class="text-success">予定</span></span>
                    @else
                        <a href="{{route('app::roster::calendar::form::index', ['ym' => $ym, 'day' => $day['day']])}}"
                            class="btn btn-primary btn-xs" title="所属長の承認待ちです。"
                            data-toggle="tooltip"
                            data-placement="top"><span class="text-warning"
                        >予定</span></a>
                    @endif
                    <span></span>
                    {{-- actual --}}
                    @if(!$r || !$r->is_actual_entry)
                        <a href="{{route('app::roster::calendar::form::index', ['ym' => $ym, 'day' => $day['day']])}}"
                            class="btn btn-primary btn-xs"
                            title="入力されていません。"
                            data-toggle="tooltip"
                            data-placement="top"
                        ><span>実績</span></a>
                    @elseif($r->is_actual_reject == true)
                        <a href="{{route('app::roster::calendar::form::index', ['ym' => $ym, 'day' => $day['day']])}}"
                            class="btn btn-primary btn-xs"
                            @if(!empty($r->reject_reason)) title="却下されました。再入力をお願いします。（理由：{{$r->reject_reason}}）"
                            @else title="却下されました。再入力をお願いします。" @endif
                            data-toggle="tooltip"
                            data-placement="top"
                        ><span class="text-danger">実績</span></a>
                    @elseif($r->is_actual_accept == true)
                        <span class="btn btn-primary btn-xs disabled" title="承認されました。以降の修正は行えません。" data-toggle="tooltip" data-placement="top"><span class="text-success">実績</span></span>
                    @else
                        <a href="{{route('app::roster::calendar::form::index', ['ym' => $ym, 'day' => $day['day']])}}"
                            class="btn btn-primary btn-xs" title="所属長の承認待ちです。"
                            data-toggle="tooltip"
                            data-placement="top"><span class="text-warning"
                        >実績</span></a>
                    @endif
                </div>
            </div>
    </div>

    <div class="panel-body fix-or-wide" style="height: 175px; overflow-y: scroll; padding: 5px;">




        @if($day['data'] != [])

        <div class="text-left">
            {{-- Plan --}}
            @if($r->is_plan_entry)
                <span class="label label-info">予定</span>
                @if(!empty($r->plan_overtime_start_time) && !empty($r->plan_overtime_end_time))
                    <p class="small text-left">{{date('G:i',strtotime($r->plan_overtime_start_time))}} ～ {{date('G:i', strtotime($r->plan_overtime_end_time))}}</p>
                @endif
                @if(!empty($r->plan_rest_reason_id))
                    <p class="small text-left">{{$r->PlanRest->rest_reason_name or ''}}</p>
                @endif
                @if(!empty($r->plan_overtime_reason))
                    <p class="small text-left">{{$r->plan_overtime_reason}}</p>
                @endif
            @endif
            {{-- Actual --}}
            @if($r->is_actual_entry)
                <span class="label label-info">実績</span>
                @if(!empty($r->actual_overtime_start_time) && !empty($r->actual_overtime_end_time))
                    <p class="small text-left">{{date('G:i',strtotime($r->actual_overtime_start_time))}} ～ {{date('G:i', strtotime($r->actual_overtime_end_time))}}</p>
                @endif
                @if(!empty($r->actual_rest_reason_id))
                    <p class="small text-left">{{$r->ActualRest->rest_reason_name or ''}}</p>
                @endif
                @if(!empty($r->actual_overtime_reason))
                    <p class="small text-left">{{$r->actual_overtime_reason}}</p>
                @endif
            @endif

        </div>
        @endif


    </div>
</div>