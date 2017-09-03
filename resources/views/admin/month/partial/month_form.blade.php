@if($rows == [null])
<div class="alert alert-danger" role="alert">月別IDが登録されていないようです。</div>
@else
<table class="table va-middle">
	<thead>
		<tr>
			<th class="bg-primary">状態</th>
			<th class="bg-primary">月別ID</th>
			<th class="bg-primary">データ件数</th>
			<th class="bg-primary">データ範囲</th>
			<th class="bg-primary"></th>
		</tr>
	</thead>
	<tbody>
	@foreach($rows as $row)
		<tr>
			<td>@if($row->is_current == true) <span class="label label-warning"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> 公開中</span> @endif</td>
			<td>
				<p>{{$row->monthly_id}}</p>
				@if($counts[$row->id] > 0)
				<p><small><a href="{{route('admin::super::month::status', ['id'=>$row->monthly_id])}}"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> 詳細を見る</a></small></p>
				@endif
			</td>

			<td>
				<p>{{number_format($counts[$row->id])}}件</p>
			</td>

			<td><small>{{date('Y年n月', strtotime($row->displayed_on))}}1日 ～ {{date('Y年n月j日', strtotime($row->displayed_on))}}</small></td>
			<td>
				<form class="form-horizontal" role="form" method="POST" action="{{route('admin::super::month::publish',['id'=>$row->id])}}">
				    {{-- CSRF対策--}}
				    <input type="hidden" name="_token" value="{{ csrf_token() }}">

				    @if($row->is_current == true)
				    <button type="submit" class="btn btn-block btn-warning" disabled="">公開中</button>
				    @else
				    <button type="submit" class="btn btn-block btn-success">公開する</button>
				    @endif

				</form>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
@endif