{{--
  -  inherit: $u as users
  -  inherit  $i as $key
  --}}


<table class="table table-hover table-small">
{{--     <thead>
        <tr>
            <th class="bg-primary" width="20%">日付</th>
            <th class="bg-primary" width="40%">予定</th>
            <th class="bg-primary" width="40%">実績</th>
        </tr>
    </thead> --}}
    <tbody>

@foreach($rows as $day)
<?php (!empty($day['data'][$u->user_id])) ? $r = $day['data'][$u->user_id] : null; ?>
<tr
    id="{{$i}}-{{$day['day']}}"
    width="16%"
    @if(!empty($r) && $r->is_plan_entry)
        @if($r->is_plan_accept)       data-plan="false"
        @elseif($r->is_plan_reject)   data-plan="true"
        @else                         data-plan="true" @endif
    @else                             data-plan="false" @endif
    @if(!empty($r) && $r->is_actual_entry)
        @if($r->is_actual_accept)     data-actual="false"
        @elseif($r->is_actual_reject) data-actual="true"
        @else                         data-actual="true" @endif
    @else                             data-actual="false" @endif

    @if($day['holiday'])       class="warning"
    @elseif($day['week'] == 0) class="warning"
    @elseif($day['week'] == 6) class="info" @endif
>
    <th class="bg-primary-important va-middle">
        <p
            @if($day['holiday'])       class="text-danger-light" data-toggle="tooltip" data-placement="right" title="{{$day['holiday_name']}}"
            @elseif($day['week'] == 0) class="text-danger-light"
            @elseif($day['week'] == 6) class="text-info-light" @endif
        ><strong>{{date('n月j日', strtotime($day['date']))}} （{{$day['week_name']}}）</strong></p>
    </th>

{{-- 予定 --}}
<td class="text-left" width="42%">
    <div class="panel panel-primary">
        <div class="panel-heading">
            {{-- 状態ラベル --}}
            <span data-toggle="tooltip"
                @if(isset($r->is_plan_accept) && $r->is_plan_accept)     class="label label-success" title="承認されました。変更は行えません。"
                @elseif(isset($r->is_plan_reject) && $r->is_plan_reject) class="label label-danger"  title="却下されました。"
                @elseif(isset($r->is_plan_entry) && $r->is_plan_entry)   class="label label-warning" title="承認されていません。"
                @else                                                    class="label label-default" title="入力されていません。" @endif
            >予定</span> - {{$u->name}}さん
        </div>
        <div class="panel-body">
            {{-- データ内容 --}}
            @if(!empty($r->plan_rest_reason_id))                                            <label>予定休暇理由</label><p>{{$rests[$r->plan_rest_reason_id]}}</p> @endif
            @if(!empty($r->plan_work_type_id))                                              <label>予定勤務形態</label><p>{{$types[$r->plan_work_type_id]['name']}} {{$types[$r->plan_work_type_id]['time']}}</p> @endif
            @if(!empty($r->plan_overtime_start_time) && !empty($r->plan_overtime_end_time)) <label>予定就業時間</label><p>{{date('G:i', strtotime($r->plan_overtime_start_time))}} ～ {{date('G:i', strtotime($r->plan_overtime_end_time))}}</p> @endif
            @if(!empty($r->plan_overtime_reason))                                           <label>予定残業理由</label><p>{{$r->plan_overtime_reason}}</p> @endif
            @if(empty($r->is_plan_entry))                                                   <label>データが入力されていません。</label> @endif
        </div>
        <div class="panel-footer">
            @if(isset($r->is_plan_accept) && !$r->is_plan_accept && $r->is_plan_entry) @include('roster.app.accept.partial.calendar_plan') @else 　 @endif
        </div>
    </div>
</td>
{{-- 実績 --}}
<td class="text-left" width="42%">
    <div class="panel panel-primary">
        <div class="panel-heading">
            {{-- 状態ラベル --}}
            <span data-toggle="tooltip"
                @if(isset($r->is_plan_accept) && $r->is_plan_accept)
{{--                     @if($r->is_actual_accept)                                  class="label label-success" title="承認されました。変更は行えません。"
                    @elseif($r->is_actual_reject)                              class="label label-danger"  title="却下されました。"
                    @elseif(isset($r->is_actual_entry) && $r->is_actual_entry) class="label label-warning" title="承認されていません。"
                    @else                                                      class="label label-default" title="入力されていません。" @endif --}}
                    @if($r->is_actual_accept)                                  class="label label-success" title="承認されました。変更は行えません。"
                    @elseif($r->is_actual_reject)                              class="label label-danger"  title="却下されました。"
                    @elseif(isset($r->is_actual_entry) && $r->is_actual_entry) class="label label-warning" title="承認されていません。" @endif
                @else
                    @if(isset($r->is_actual_entry) && $r->is_actual_entry)     class="label label-warning" title="先に予定の承認を行ってください。"
                    @else                                                      class="label label-default" title="入力されていません。" @endif
                @endif
            >予定</span> - {{$u->name}}さん
        </div>
        <div class="panel-body">
            {{-- データ内容 --}}
            @if(!empty($r->actual_rest_reason_id))                                            <label>予定休暇理由</label><p>{{$rests[$r->actual_rest_reason_id]}}</p> @endif
            @if(!empty($r->actual_work_type_id))                                              <label>予定勤務形態</label><p>{{$types[$r->actual_work_type_id]['name']}} {{$types[$r->actual_work_type_id]['time']}}</p> @endif
            @if(!empty($r->actual_overtime_start_time) && !empty($r->actual_overtime_end_time)) <label>予定就業時間</label><p>{{date('G:i', strtotime($r->actual_overtime_start_time))}} ～ {{date('G:i', strtotime($r->actual_overtime_end_time))}}</p> @endif
            @if(!empty($r->actual_overtime_reason))                                           <label>予定残業理由</label><p>{{$r->actual_overtime_reason}}</p> @endif
            @if(empty($r->is_actual_entry))                                                   <label>データが入力されていません。</label> @endif
        </div>
        <div class="panel-footer">
            @if(isset($r->is_actual_accept) && $r->is_plan_accept && !$r->is_actual_accept && $r->is_actual_entry) @include('roster.app.accept.partial.calendar_actual') @else 　 @endif
        </div>
    </div>
</td>

</tr>
<?php unset($r); ?>
@endforeach

    </tbody>
</table>