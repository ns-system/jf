<table class="table table-hover table-small">
	<thead>
		<tr>
			<th class="bg-primary">日付</th>
			@foreach($users as $u)
				<th class="bg-primary" colspan="2">{{$u->name}}さん</th>
			@endforeach
		</tr>
	</thead>
	<tbody>

@foreach($rows as $day)
<tr>
    <th class="bg-primary va-middle">
        <p
            @if($day['holiday'])       class="text-danger-light" data-toggle="tooltip" data-placement="right" title="{{$day['holiday_name']}}"
            @elseif($day['week'] == 0) class="text-danger-light"
            @elseif($day['week'] == 6) class="text-info-light" @endif
        ><strong>{{date('n月j日', strtotime($day['date']))}} （{{$day['week_name']}}）</strong></p>
    </th>

@foreach($users as $u)
    <?php (!empty($day['data'][$u->user_id])) ? $r = $day['data'][$u->user_id] : null; ?>
    @if(isset($r))
        {{-- 予定 --}}
        <td
            @if($day['holiday'])       class="text-left danger"
            @elseif($day['week'] == 0) class="text-left danger"
            @elseif($day['week'] == 6) class="text-left info"
            @else                      class="text-left" @endif
            style="border-left: 1px solid #ddd;"
        >
            @if($r->is_plan_entry)
                <p>
                    @if($r->is_plan_accept)     <span class="label label-success">予定</span>
                    @elseif($r->is_plan_reject) <span class="label label-danger">予定</span>
                    @else                       <span class="label label-warning">予定</span> @endif
                </p>
                @if(!empty($r->plan_rest_reason_id))        <p>{{$rests[$r->plan_rest_reason_id]}}</p> @endif
                @if(!empty($r->plan_overtime_start_time) &&
                    !empty($r->plan_overtime_end_time))     <p>{{date('G:i', strtotime($r->plan_overtime_start_time))}} ～ {{date('G:i', strtotime($r->plan_overtime_end_time))}}</p> @endif
                @if(!empty($r->plan_overtime_reason))       <p>{{$r->plan_overtime_reason}}</p> @endif
            @elseif(!empty($r->entered_on))
                <p><span class="label label-default">予定</span></p>
                @if(!empty($r->plan_rest_reason_id)) <p>{{$rests[$r->plan_rest_reason_id]}}</p> @endif
                @if(!empty($r->plan_work_type_id)) <p>{{$types[$r->plan_work_type_id]}}</p> @endif
            @endif
        </td>
        {{--実績 --}}
        <td
            @if($day['holiday'])       class="text-left danger"
            @elseif($day['week'] == 0) class="text-left danger"
            @elseif($day['week'] == 6) class="text-left info"
            @else                      class="text-left" @endif
        >
            @if($r->is_actual_entry)
                <p>
                    @if($r->is_actual_accept)     <span class="label label-success">実績</span>
                    @elseif($r->is_actual_reject) <span class="label label-danger">実績</span>
                    @else                         <span class="label label-warning">実績</span> @endif
                </p>
                @if(!empty($r->actual_rest_reason_id))        <p>{{$rests[$r->actual_rest_reason_id]}}</p> @endif
                @if(!empty($r->actual_overtime_start_time) &&
                    !empty($r->actual_overtime_end_time))     <p>{{date('G:i', strtotime($r->actual_overtime_start_time))}} ～ {{date('G:i', strtotime($r->actual_overtime_end_time))}}</p> @endif
                @if(!empty($r->actual_overtime_reason))       <p>{{$r->actual_overtime_reason}}</p> @endif
            @endif
        </td>
        <?php unset($r); ?>
    @else
        <td
            @if($day['holiday'])       class="text-left danger"
            @elseif($day['week'] == 0) class="text-left danger"
            @elseif($day['week'] == 6) class="text-left info"
            @else                      class="text-left" @endif
            style="border-left: 1px solid #ddd;"
        ></td>
        <td
            @if($day['holiday'])       class="text-left danger"
            @elseif($day['week'] == 0) class="text-left danger"
            @elseif($day['week'] == 6) class="text-left info"
            @else                      class="text-left" @endif
        ></td>
    @endif
@endforeach
</tr>
@endforeach

	</tbody>
</table>