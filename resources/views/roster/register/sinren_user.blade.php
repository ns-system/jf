@extends('layout')

@section('title', 'ホーム')

@section('header')
@parent
    @section('brand', '管理画面')
@endsection


@section('content')
	<div class="col-md-10 col-md-offset-1">
	<h2>ユーザー情報登録</h2>
	@include('roster.register.partial.sinren_user_form')

	<div>
</div>
@endsection

@section('footer')
@parent
@endsection