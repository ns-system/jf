

<?php $r =$day['data'] != [] ? $day['data'] : null; ?>


<div class="panel panel-primary" style="margin: 2px;">
	<div class="panel-heading text-left" style="padding: 5px 10px;">
		<span class="label
			@if($day['week'] == 6) label-info
			@elseif($day['week'] == 0) label-danger
			@else label-primary
			@endif"
			style="display: inline-block; min-width: 30px; padding: 5px;">{{$day['day']}}</span>
	</div>

	<div class="panel-body fix-or-wide" style="height: 175px; overflow-y: scroll; padding: 5px;">

		{{-- plan --}}
		<div class="text-right" style="margin-bottom: 5px;">
			<div class="btn-group">
				@if(!$r || !$r->is_plan_accept)
				<button href="#"
					class          ="btn btn-info btn-xs plan"
					data-toggle    ="modal"
					data-target    ="#plan_form"
					data-id        ="{{$r->id or ''}}"
					data-date      ="{{$day['date'] or ''}}"
					data-rest      ="{{$r->plan_rest_reason_id or ''}}"
					data-overtime  ="{{$r->plan_overtime_reason or ''}}"
					data-type      ="plan"
					data-start-time="@if(!$r) 00:00:00 @elseif ($r->plan_overtime_start_time) {{$r->plan_overtime_start_time}} @else {{$r->planType->work_start_time}} @endif"
					data-end-time="@if(!$r) 00:00:00 @elseif ($r->plan_overtime_end_time) {{$r->plan_overtime_end_time}} @else {{$r->planType->work_end_time}} @endif"
					data-end-time="">予定</button>
				@endif
				{{-- actual --}}
				@if(!$r || !$r->is_actual_accept)
				<button href="#"
					class          ="btn btn-info btn-xs actual"
					data-toggle    ="modal"
					data-target    ="#actual_form"
					data-id        ="{{$r->id or ''}}"
					data-date      ="{{$day['date'] or ''}}"
					data-rest      ="{{$r->actual_rest_reason_id or ''}}"
					data-rest      ="{{$r->actual_overtime_reason or ''}}"
					data-type      ="actual"
					data-start-time="@if(!$r) 00:00:00 @elseif ($r->actual_overtime_start_time) {{$r->actual_overtime_start_time}} @else {{$r->ActualType->work_start_time}} @endif"
					data-end-time="@if(!$r) 00:00:00 @elseif ($r->actual_overtime_end_time) {{$r->actual_overtime_end_time}} @else {{$r->ActualType->work_end_time}} @endif"
					data-end-time="">実績</button>
				@endif
				<a href="#" class="btn btn-success btn-xs">詳細</a>
			</div>
		</div>

		@if($day['data'] != [])

			<div>
				<p class="small text-left">
					@if($r->is_plan_accept == true)
						<span
							class="label label-success"
							data-toggle="tooltip"
							title="承認されました。以降の修正は行えません。"
							data-placement="right">予定</span>
					@elseif($r->is_plan_reject == true)
						<span
						class="label label-danger"
						data-toggle="tooltip"
						title="却下されました。再入力をお願いします。"
						data-placement="right">予定</span>
					@else
						<span
							class="label label-warning"
							data-toggle="tooltip"
							title="所属長の承認待ちです。"
							data-placement="right">予定</span>
					@endif
				</p>
				<p class="small text-center">
					{{date('G:i',strtotime($r->plan_overtime_start_time))}}
					～
					{{date('G:i', strtotime($r->plan_overtime_end_time))}}</p>
				<p class="small text-left">{{$r->plan_overtime_reason}}</p>
				<p class="small text-left">{{$r->PlanRest->rest_reason_name or ''}}</p>
				<p class="small text-left">
					@if($r->is_actual_accept == true)
						<span
							class="label label-success"
							data-toggle="tooltip"
							title="承認されました。以降の修正は行えません。"
							data-placement="right">実績</span>
					@elseif($r->is_acctual_reject == true)
						<span
						class="label label-danger"
						data-toggle="tooltip"
						title="却下されました。再入力をお願いします。"
						data-placement="right">実績</span>
					@else
						<span
							class="label label-warning"
							data-toggle="tooltip"
							title="所属長の承認待ちです。"
							data-placement="right">実績</span>
					@endif
				</p>
				<p class="small text-center">
					{{date('G:i',strtotime($r->actual_overtime_start_time))}}
					～
					{{date('G:i', strtotime($r->actual_overtime_end_time))}}
				</p>
				<p class="small text-left">{{$r->actual_overtime_reason}}</p>

			</div>
		@endif


	</div>
</div>