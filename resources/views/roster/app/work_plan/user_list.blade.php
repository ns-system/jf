@extends('layout')

@section('title', '勤務予定データ')

@section('header')
@parent
    @section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
    <div class="col-md-2">
        @include('app.sidebar.sidebar')
    </div>
@endsection


@section('content')
<div class="col-md-10">

@include('partial.alert')
<div class="border-bottom"><h2>{{date('Y年n月', strtotime($month.'01'))}}分 勤務予定データ作成<small> - ユーザー選択</small></h2></div>
@if(!empty($users))
<table class="table table-hover">
	<thead>
		<tr>
			<th class="bg-primary">部署</th>
			<th class="bg-primary">ユーザー名</th>
			<th class="bg-primary">状態</th>
		</tr>
	</thead>

	<tbody>
@foreach($users as $user)
		<tr>
			<td>{{$user->division_name}}</td>
			<td><a href="{{route('app::roster::work_plan::list', ['month'=>$month, 'id'=>$user->user_id])}}">{{$user->last_name}} {{$user->first_name}}<small>さん</small></a></td>
			<td>
				@if($cnt[$user->user_id] > 0) <span class="label label-success">データ登録済み</span>
				@else                         <span class="label label-default">データ未登録</span> @endif
			</td>
		</tr>
@endforeach
	</tbody>
</table>
@else
<div class="alert alert-warning" role="alert">ユーザーが存在しないようです。</div>
@endif

<div class="text-right">
    <div class="btn-group">
        <a href="{{route('app::roster::work_plan::index')}}" class="btn btn-primary btn-sm" style="min-width: 125px;">
            <span class="glyphicon glyphicon-backward" aria-hidden="true"></span> 戻る
        </a>
        <span></span>
        <a
            href="{{route('app::roster::work_plan::division', ['month'=>$next])}}"
            class="btn btn-success btn-sm"
            style="min-width: 125px;"
        ><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> 翌月へ</a>
        <span></span>
        <a
            href="{{route('app::roster::work_plan::division', ['month'=>$prev])}}"
            class="btn btn-warning btn-sm"
            style="min-width: 125px;"
        ><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> 前月へ</a>
    </div>

</div>
</div>
@endsection

@section('footer')
@parent
@endsection