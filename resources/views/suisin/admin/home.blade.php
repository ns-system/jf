@extends('layout')

@section('title', 'ホーム')

@section('header')
@parent
@section('brand', '管理画面')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')
</div>
@endsection


@section('content')
<div class="col-md-6">
    <div class="border-bottom"><h2>推進支援システム マスタ編集</h2></div>

	<div class="well">
		<p>推進支援システムに利用しているマスタの編集を行います。</p>
		<p>マスタを編集する場合、一度CSVファイルに出力した上で修正したものを取り込んで更新してください。</p>
		<p>一度登録したマスタは<span class="bolder text-danger">削除できません</span>。</p>
		<p>一度に取り込めるレコード件数には上限があるため、多すぎる場合は分割して取り込んでください。（目安：1,000件程度）</p>
	</div>
</div>


<div class="col-md-4">
<div class="panel panel-default">
	<div class="panel-heading">お知らせ</div>

	@if($rows == null)
	<div class="palel-body">お知らせはありません。</div>
	@else
	<table class="table">
		<tbody>
		@foreach($rows as $row)
			<tr>
				<td class="text-left">
					<p><span class="label label-info">お知らせ</span> <small>{{ date('Y-n-j G:i:s', strtotime($row->updated_at)) }}</small></p>
					<p>{{$row->User->name}}さんが{{$row->table_name_kanji}}を更新しました。</p>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	@endif
</div>

@endsection

@section('footer')
@parent
@endsection