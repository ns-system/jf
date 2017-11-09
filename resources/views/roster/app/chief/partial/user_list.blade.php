<table class="table table-hover">
	<thead>
		<tr>
			<th class="bg-primary">部署</th>
			<th class="bg-primary text-left">名前</th>
			<th class="bg-primary">ユーザー区分</th>
			<th class="bg-primary">登録日</th>
			<th class="bg-primary">更新日</th>
		</tr>
	</thead>
	<tbody>
@foreach($rows as $r)
{{-- 	{{var_dump($r)}} --}}
	<tr>
		<td>{{$r->division_name}}</td>
		@if($r->is_chief)
			<td class="text-left">{{$r->last_name}} {{$r->first_name}}さん</td>
			<td><span class="label label-warning" style="display: inline-block; min-width: 100px;">責任者</span></td>
		@else
			<td class="text-left">
				<a href="" data-toggle="modal" data-target="#form_{{$r->key_id}}">{{$r->last_name}} {{$r->first_name}}さん</a>
				@include('roster.app.chief.partial.form')
			</td>
			<td>
				@if($r->is_proxy)
					@if($r->is_proxy_active)
						<span class="label label-success" data-toggle="tooltip" title="代理機能は有効です。" style="display: inline-block; min-width: 100px;">
							<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span> 責任者代理
						</span>
					@else
						<span class="label label-default" data-toggle="tooltip" title="代理機能は無効です。" style="display: inline-block; min-width: 100px;">
						<span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> 責任者代理
						</span>
					@endif
				@else
					<span class="label label-info">一般ユーザー</span>
				@endif
			</td>
		@endif
		<td><small>@if(!empty($r->create_time)) {{date('Y年n月j日 G:i', strtotime($r->create_time))}} @endif</small></td>
		<td><small>@if(!empty($r->update_time)) {{date('Y年n月j日 G:i', strtotime($r->update_time))}} @endif</small></td>
	</tr>
@endforeach
	</tbody>
</table>