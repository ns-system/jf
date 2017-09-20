@if($rows == [null])
<div class="alert alert-danger" role="alert">月別IDが登録されていないようです。</div>
@else
<table class="table va-middle">
	<thead>
		<tr>
			<th class="bg-primary">状態</th>
			<th class="bg-primary">月別ID</th>
			<th class="bg-primary"><p>処理数／存在数</p><p>データ件数</p></th>
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

			<td class="text-right">
				<p>
                    {{number_format($counts[$row->id]['import'])}}件／
                    {{number_format($counts[$row->id]['exist'])}}件
                </p>
                <p><small>／{{number_format($counts[$row->id]['all'])}}件</small>
                </p>
			</td>

			<td><small>{{date('Y年n月', strtotime($row->monthly_id.'01'))}}1日 ～ {{date('Y年n月t日', strtotime($row->monthly_id.'01'))}}</small></td>
			<td>
				<form class="form-horizontal" role="form" method="POST" action="{{route('admin::super::month::publish',['id'=>$row->id])}}" style="margin-bottom: 0;">
				    {{-- CSRF対策--}}
				    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div {{-- style="margin-bottom: 5px;" --}}>
    				    @if($row->is_current == true)
    				    <button type="submit" class="btn btn-warning btn-sm" disabled="" style="min-width: 250px;">公開中</button>
    				    @else
                        <div class="btn-group">
                            <a href="{{route('admin::super::month::copy_confirm', ['id'=>$row->monthly_id])}}" class="btn btn-primary btn-sm" style="min-width: 125px;">処理する</a>
        				    <button type="submit" class="btn btn-primary btn-sm" style="min-width: 125px;">公開する</button>
                        </div>
    				    @endif
                    </div>
				</form>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
@endif