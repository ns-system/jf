<?php $rid = $row->roster_id; ?>

<div class="panel panel-primary">
	<div class="panel-heading">
	<p>{{$row->last_name}}さん <small>（{{$row->entered_on}}）</small></p>

	</div>
	<div class="panel-body small" style="height: 150px; overflow-y: scroll;">
		<input type="hidden" name="id[{{$rid}}]" value="{{$rid}}">
		<table class="table table-small va-middle" style="margin: 0;">
			<tbody>
				<tr>
					@if($row->is_plan_entry == true)
					<th class="bg-primary"><span>予定</span> @if($row->is_plan_accept) <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> @endif</th>
					<td class="text-left">
						@if(array_key_exists($row->plan_work_type_id, $types))
							<p>{{$types[$row->plan_work_type_id]['mame']}}</p>
						@endif
						<p>
						{{date('G:i', strtotime($row->plan_overtime_start_time))}}
						 ～ 
						{{date('G:i', strtotime($row->plan_overtime_end_time))}}
						</p>
						@if(array_key_exists($row->plan_rest_reason_id, $rests))
							<p>{{$rests[$row->plan_rest_reason_id]}}</p>
						@endif
						<p>{{$row->plan_overtime_reason}}</p>
					</td>
					@endif
				</tr>

				<tr>
					@if($row->is_actual_entry == true)
					<th class="bg-primary"><span>実績</span> @if($row->is_actual_accept) <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> @endif</th>
					<td class="text-left">
						@if(array_key_exists($row->actual_work_type_id, $types))
							<p>{{$types[$row->actual_work_type_id]['mame']}}</p>
						@endif
						<p>
						{{date('G:i', strtotime($row->actual_overtime_start_time))}}
						 ～ 
						{{date('G:i', strtotime($row->actual_overtime_end_time))}}
						</p>
						@if(array_key_exists($row->actual_rest_reason_id, $rests))
							<p>{{$rests[$row->actual_rest_reason_id]}}</p>
						@endif
						<p>{{$row->actual_overtime_reason}}</p>
					</td>
					@endif
				</tr>
			</tbody>
		</table>
	</div>
	<div class="panel-footer" style="height: 125px; overflow-y: scroll;">

<table>
	<tbody>
			@if($row->is_plan_accept != true)
		<tr>
			<td width="30%">予定</td>
			<td width="70%" class="text-left">
				<div class="form-group">
					<div class="col-sm-6">
					<div class="radio">
						<label class="text-success"  style="font-weight: bolder;">
							<input type="radio" name="plan[{{$rid}}]" value="1" checked="checked">承認
						</label>
					</div>
					</div>

					<div class="col-sm-6">
					<div class="radio">
						<label class="text-danger"  style="font-weight: bolder;">
							<input type="radio" name="plan[{{$rid}}]" value="0">却下
						</label>
					</div>
					</div>
				</div>
{{-- 			@else
				<p class="bolder">既に承認済みです。</p> --}}
			</td>
			@endif
		</tr>

		@if($row->is_actual_entry == true && $row->is_actual_accept != true)
		<tr>
			<td>実績</td>
			<td class="text-left">
				<div class="form-group">
					<div class="col-sm-6">
					<div class="radio">
						<label class="text-success"  style="font-weight: bolder;">
							<input type="radio" name="actual[{{$rid}}]" value="1" checked="checked">承認
						</label>
					</div>
					</div>

					<div class="col-sm-6">
					<div class="radio">
						<label class="text-danger"  style="font-weight: bolder;">
							<input type="radio" name="actual[{{$rid}}]" value="0">却下
						</label>
					</div>
					</div>
				</div>
			</td>
		</tr>
		@endif

		@if($row->is_actual_accept != true || $row->is_plan_accept != true)
		<tr>
			<td>却下理由</td>
			<td>
				<div class="form-group" style="width: 100%;">
					<input type="text" name="reject_reason[{{$rid}}]" class="form-control input-sm" style="width: 100%;" value="{{$row->reject_reason}}">
				</div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="text-right">
				<button type="submit" class="btn btn-primary btn-sm" formaction="/roster/chief/accept/edit/unit?id={{$row->roster_id}}" style="margin-top: 10px;">更新する</button>
			</td>
		</tr>
		@endif
	</tbody>
</table>

	</div>
</div>