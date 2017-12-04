@if(!$rosters->isEmpty())
<table class="table table-hover table-small">
    <thead>
        <tr>
            <th class="bg-primary">日付</th>
            <th class="bg-primary">部署・社員番号・氏名</th>
            <th class="bg-primary">予定承認</th>
            <th class="bg-primary">実績承認</th>
            <th class="bg-primary">勤務予定</th>
            <th class="bg-primary">勤務実績</th>
            <th class="bg-primary">残業予定</th>
            <th class="bg-primary">残業実績</th>
            <th class="bg-primary"></th>
        </tr>
    </thead>
    <tbody>
@foreach($rosters as $r)
<?php $day = $calendar[$r->entered_on]; ?>
    <tr
        @if($day['holiday'] == true) class="danger"
        @elseif($day['week'] == 0)   class="danger"
        @elseif($day['week'] == 6)   class="info"
        @endif
    >
        <th class="va-middle bg-primary-important">
            <p
                @if($day['holiday'] == true) class="text-danger-light" data-toggle="tooltip" data-placement="right" title="{{$day['holiday_name']}}"
                @elseif($day['week'] == 0)   class="text-danger-light"
                @elseif($day['week'] == 6)   class="text-info-light"
                @endif
            >{{date('n月j日', strtotime($r->entered_on))}} （{{$day['week_name']}}）</p>
        </th>
        <td class="va-middle text-left">
            @if(!empty($r->staff_number))
{{--                 <p>{{$r->staff_number}}</p> --}}
            @else <p class="text-danger"><a href="{{route('admin::roster::index', ['system'=>'Roster','category'=>'RosterUser'])}}"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 職員番号が登録されていません</a></p> @endif
            <p>{{$r->division_name}}</p>
            <p><b>{{$r->staff_number}} : </b>{{$r->last_name}} {{$r->first_name}}<small>さん</small></p>
        </td>
        {{-- 予定状態ラベル --}}
        <td>
            @if($r->is_plan_entry)
                @if($r->is_plan_accept)
                    <p><span class="label label-success" style="min-width: 75px;">承認済み</span></p>
                    @if(!empty($r->plan_accept_last_name))
                        <p>{{$r->plan_accept_last_name}}さん</p>
                        <p>{{date('n月j日 G:i', strtotime($r->plan_accepted_at))}}</p>
                    @endif
                @elseif($r->is_plan_reject)
                    <p><span class="label label-danger" style="min-width: 75px;">却下</span></p>
                    @if(!empty($r->plan_reject_last_name))
                        <p>{{$r->plan_reject_name}}さん</p>
                        <p>{{date('n月j日 G:i', strtotime($r->plan_rejected_at))}}</p>
                    @endif
                @else
                    <p><span class="label label-warning" style="min-width: 75px;">承認待ち</span></p>
                @endif
            @else
                <label class="label label-default" style="min-width: 75px;">未入力</label>
            @endif
        </td>
        {{-- 予定状態ラベル --}}

        {{-- 実績状態ラベル --}}
        <td>
            @if($r->is_actual_entry)
                @if($r->is_actual_accept)
                    <p><span class="label label-success" style="min-width: 75px;">承認済み</span></p>
                    @if(!empty($r->actual_accept_last_name))
                        <p>{{$r->actual_accept_last_name}}さん</p>
                        <p>{{date('n月j日 G:i', strtotime($r->actual_accepted_at))}}</p>
                    @endif
                @elseif($r->is_actual_reject)
                    <p><span class="label label-danger" style="min-width: 75px;">却下</span></p>
                    @if(!empty($r->actual_reject_last_name))
                        <p>{{$r->actual_reject_last_name}}さん</p>
                        <p>{{date('n月j日 G:i', strtotime($r->actual_rejected_at))}}</p>
                    @endif
                @else
                    <p><span class="label label-warning" style="min-width: 75px;">承認待ち</span></p>
                @endif
            @else
                <label class="label label-default" style="min-width: 75px;">未入力</label>
            @endif
        </td>
        {{-- 実績状態ラベル --}}


        {{-- 勤務予定 --}}
        <td class="text-left">
            @if(!empty($r->plan_work_type_id))
                <p>{{$types[$r->plan_work_type_id]['name']}}</p>
                <p>{{$types[$r->plan_work_type_id]['time']}}</p>
            @endif
            @if(!empty($r->plan_rest_reason_id))
                <p>{{$rests[$r->plan_rest_reason_id]}}</p>
            @endif
        </td>
        {{-- 勤務予定 --}}

        {{-- 勤務実績 --}}
        <td class="text-left">
            @if(!empty($r->actual_work_type_id))
                <p>{{$types[$r->actual_work_type_id]['name']}}</p>
                <p>{{$types[$r->actual_work_type_id]['time']}}</p>
            @endif
            @if(!empty($r->actual_rest_reason_id))
                <p>{{$rests[$r->actual_rest_reason_id]}}</p>
            @endif
        </td>
        {{-- 勤務実績 --}}

        {{-- 残業予定 --}}
        <td class="text-left">
            @if(!empty($r->plan_overtime_start_time) && !empty($r->plan_overtime_end_time))
                <p>{{date('G:i', strtotime($r->plan_overtime_start_time))}} ～ {{date('G:i', strtotime($r->plan_overtime_end_time))}}</p>
                <p>{{$r->plan_overtime_reason}}</p>
            @endif
        </td>
        {{-- 残業予定 --}}


        {{-- 残業実績 --}}
        <td class="text-left">
            @if(!empty($r->actual_overtime_start_time) && !empty($r->actual_overtime_end_time))
                <p>{{date('G:i', strtotime($r->actual_overtime_start_time))}} ～ {{date('G:i', strtotime($r->actual_overtime_end_time))}}</p>
                <p>{{$r->actual_overtime_reason}}</p>
            @endif
        </td>
        {{-- 残業実績 --}}

        <td class="va-middle">
            <a href="{{route('admin::roster::csv::edit', ['id'=>$r->key_id, 'ym'=>$ym])}}"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> 強制変更</a>
        </td>
    </tr>
@endforeach
    </tbody>
</table>

@else

@endif