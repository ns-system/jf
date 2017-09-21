@extends('layout')

@section('title', '管理者ホーム')

@section('header')
@parent
@section('brand', '管理者メニュー')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')
</div>
@endsection


@section('content')
<div class="col-md-6">
	<div class="border-bottom"><h2>管理者用メニュー</h2></div>

	<div class="well">
	    <p>ここから管理用コンソールに接続できます。</p>
	    <p>管理用コンソールからは各種マスタの変更や修正が行えます。</p>
	    <p>なお、権限のないマスタの修正を行うことはできません。</p>
	</div>
</div>
<div class="col-md-4">
<div class="panel panel-default">
	<div class="panel-heading">お知らせ</div>
	<table class="table">
		<tbody>
			<tr>
				<td class="text-left">
					<p><span class="label label-info">お知らせ</span>2017/08/17</p>
					<p>新規ユーザーが追加されました。</p>
				</td>
			</tr>

		</tbody>
	</table>
</div>
</div>

@endsection

@section('footer')
@parent
@endsection