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
	.small{ font-weight: bolder; }
</style>
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('partial.check_sidebar')
</div>
<div class="col-md-10">
</div>
@endsection



@section('content')

<div class="col-md-12">
<p class="btn btn-primary" id="hidden">土日を表示しない</p>
<p class="btn btn-primary" id="show">土日を表示</p>

<a href="#debug" data-toggle="collapse" class="btn btn-danger">Debug</a>
<div id="debug" style="">
	<?php var_dump($rows); ?>
</div>

<table class="table va-middle">
	<thead>
		<tr>
			<th class="bg-primary">日付</th>
			@foreach($rows[1]['users'] as $user)
			<th class="bg-primary">{{$user['name']}}さん</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($rows as $row)
			<tr data-toggle="{{$row['week']}}">
			<th class="bg-primary">
				@if($row['week'] == 6)<span class="text-info-light">{{$row['date']}}</span>
				@elseif($row['week'] == 0 || $row['holiday'] == 1)<span class="text-danger-light" data-toggle="tooltip" title="{{$row['holiday_name']}}" data-placement="right">{{$row['date']}}</span>
				@else <span>{{$row['date']}}</span>
				@endif
				<span>（{{$row['week_name']}}）</span>
			</th>
			@foreach($row['users'] as $id=>$columns)
				<?php $r = $columns['roster']; ?>
				<td style="text-align: left;">
				@if($r != null)
					@if($r->is_plan_entry != false)
						<span class="label label-primary">予定</span>
						{{-- 予定勤務形態 --}}
						@if($r->plan_work_type_id != null)
						@endif

						{{-- 予定残業時間 --}}
						@if($r->plan_overtime_start_time != null)
						<p>{{date('G:i', strtotime($r->plan_overtime_start_time))}} ～ {{date('G:i', strtotime($r->plan_overtime_end_time))}}</p>
						@endif
						
						{{-- 予定休暇理由 --}}
						@if($r->plan_rest_reason_id != null)
						@endif

						{{-- 予定残業理由 --}}
						<p>{{$r->plan_overtime_reason}}</p>
					@endif
					@if($r->is_actual_entry)
						<span class="label label-primary">実績</span>
						{{-- 実勤務形態 --}}
						@if($r->actual_work_type_id != null)
						@endif

						{{-- 実残業時間 --}}
						@if($r->actual_overtime_start_time != null)
						<p>{{date('G:i', strtotime($r->actual_overtime_start_time))}} ～ {{date('G:i', strtotime($r->actual_overtime_end_time))}}</p>
						@endif
						
						{{-- 実休暇理由 --}}
						@if($r->actual_rest_reason_id != null)
						@endif

						{{-- 実残業理由 --}}
						<p>{{$r->actual_overtime_reason}}</p>
					@endif
				@endif
				</td>
			@endforeach
			</tr>
		@endforeach
	</tbody>
</table>

</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
	$(function(){
		$('#fix').click(function(){
			$('.fix-or-wide').each(function(){
				$(this).css('min-width','initial');
			});
		});
		$('#wide').click(function(){
			$('.fix-or-wide').each(function(){
				$(this).css('min-width','250px');
			});
		});

		$('#hidden').click(function(){
			$('tr[data-toggle="6"], tr[data-toggle=0]').hide();
		});
		$('#show').click(function(){
			$('tr[data-toggle="6"], tr[data-toggle=0]').show();
		});

})
</script>
@endsection