<table class="table table-hover va-middle">
	<thead>
		<tr>
			<th class="bg-primary">日付</th>
			@foreach($users as $u)
				<th class="bg-primary">{{$u->name}}さん</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($rows as $row)
			<tr>
				<th class="bg-primary" width="15%">
					<p 
						@if($row['holiday'] == 1 || $row['week'] == 0) class="text-danger-light" data-toggle="tooltip" data-placement="right" title="{{$row['holiday_name']}}"
						@elseif($row['week'] == 6) class="text-info-light"
						@endif
					>
						<strong>{{date('n月j日', strtotime($row['date']))}} （{{$row['week_name']}}）</strong>
					</p>
				</th>
				@foreach($users as $u)
					<td class="text-left">
						<?php $data = $row['data']; ?>
						@if(isset($data[$u->id]))
							<?php $r = $data[$u->id]; ?>
							{{-- 予定 --}}
							@if($r->is_plan_entry)
								<p>
									<span
										data-toggle="tooltip"
										data-placement="right"
										@if($r->is_plan_accept)     class="label label-success" title="承認済みです。"
										@elseif($r->is_plan_reject) class="label label-danger"  title="却下されています。"
										@else                       class="label label-warning" title="承認待ちです。"
										@endif
									>予定</span>
								</p>
								@if(!empty($r->plan_rest_reason_id))
									<p>{{$rests[$r->plan_rest_reason_id]}}</p>
								@endif
								@if($r->plan_overtime_start_time != null && $r->plan_overtime_end_time)
									<p>{{date('G:i', strtotime($r->plan_overtime_start_time))}} ～ {{date('G:i', strtotime($r->plan_overtime_end_time))}}</p>
								@endif
								<p>{{$r->plan_overtime_reason or ''}}</p>
							@endif
							{{-- 実績 --}}
							@if($r->is_actual_entry)
								<p>
									<span
										data-toggle="tooltip"
										data-placement="right"
										@if($r->is_actual_accept)     class="label label-success" title="承認済みです。"
										@elseif($r->is_actual_reject) class="label label-danger"  title="却下されています。"
										@else                         class="label label-warning" title="承認待ちです。"
										@endif
									>実績</span>
								</p>
								@if(!empty($r->actual_rest_reason_id))
									<p>{{$rests[$r->actual_rest_reason_id]}}</p>
								@endif
								@if($r->actual_overtime_start_time != null && $r->actual_overtime_end_time)
									<p>{{date('G:i', strtotime($r->actual_overtime_start_time))}} ～ {{date('G:i', strtotime($r->actual_overtime_end_time))}}</p>
								@endif
								<p>{{$r->actual_overtime_reason or ''}}</p>
							@endif
						@endif
					</td>
				@endforeach
			</tr>
		@endforeach
	</tbody>
</table>