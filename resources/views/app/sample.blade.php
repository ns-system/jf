@extends('layout')

@section('title', 'ホーム')

@section('header')
@parent
    @section('brand', '管理画面')
@endsection

@section('sidebar')
    <div class="col-md-2">
    @include('partial.sidebar_admin')
    </div>
@endsection


@section('content')
	<div class="col-md-10">
	<form class="form-horizontal" role="form" method="POST" action="/sample">
    {{-- CSRF対策--}}
    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		@include('/partial/search_store')
    	<button type="submit">選択する</button>
    	<button type="submit" formaction="/sample/export">export</button>
    </form>
	<div>
</div>


<div class="container-fluid">
<?php $group_names = \App\ConsignorGroup::get(['id','group_name']); ?>
<table class="table table-small table-hover">
	<thead>
		<tr>
			<th class="bg-primary">顧客番号</th>
			<th class="bg-primary text-left">顧客名</th>
			@foreach($group_headers as $group)
			<th class="bg-primary small">{{$group->group_name}}</th>
			@endforeach
			<th class="bg-primary">合計</th>
		</tr>
	</thead>
	<tbody>
		@foreach($customers as $row)
		<tr>
			<td>
				<a href="#">{{sprintf('%08d', $row->customer_number)}}</a>
			</td>
			<td class="text-left">{{$row->kanji_name or 'nothing'}}</td>
			@foreach($group_headers as $grp)
			<?php $cst_grps = $customer_groups[$row->customer_number]; ?>
			<td>
					@foreach($cst_grps as $cst_grp)
					@if($cst_grp->consignor_group_id == $grp->id) {{$cst_grp->count}}件 @endif
					@endforeach
			</td>
			@endforeach
			<td>{{$row->total_count}}件</td>
		</tr>
		@endforeach
	</tbody>
</table>
{!! $customers->appends(['control'=>$control,'small'=>$small])->render() !!}
</div><!-- .container-fluid -->
</div>



@endsection

@section('footer')
@parent
<script type="text/javascript">
$(function(){
	$('#control_stores').change(function(){
		var control_store = $(this).val();
		$('#small_stores option').each(function(){
			$(this).attr('selected', false);
			if(control_store == 0){
				$(this).show();
			}else{
				$(this).hide();
				var value = $(this).attr('data-value');
				if(value == control_store || value == 0) $(this).show();
				if(value == 0) $(this).attr('selected', true);
			}
		});
	});
});
</script>
@endsection