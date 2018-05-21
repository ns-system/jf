@extends('layout')

@section('title', '勤怠管理システム')

@section('header')
@parent
    @section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
@parent
<div class="col-md-2">
    @include('partial.check_sidebar')
</div>
@endsection

@section('content')
  <div class="col-md-10">
    @include('partial.alert')
    <div class="border-bottom"><h2>勤怠管理システム ユーザー情報</h2></div>

	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-primary">
			<div class="panel-heading">ユーザー情報更新 <small>{{ $user->last_name }} {{ $user->first_name }} さん</small></div>
			<div class="panel-body">
			    @include('roster.app.user.partial.user_form')
			</div>
		</div>
	</div>

  </div>
        
@endsection

@section('footer')
@parent
@endsection