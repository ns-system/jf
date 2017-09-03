@extends('layout')

@section('title', '管理ユーザー設定')

@section('header')
@parent
    @section('brand', '管理ユーザー設定')
@endsection

@section('sidebar')
    <div class="col-md-2">
    @include('admin.sidebar.sidebar')
    </div>
@endsection


@section('content')
    <div class="col-md-10">
<div class="container-fluid">
        @include('partial.alert')
        <div class="border-bottom"><h2>{{$user->name}}さん</h2></div>

<div class="col-md-10 col-md-offset-1">
<div class="panel panel-primary">
	<div class="panel-heading">フォーム</div>
	<div class="panel-body">
		<div class="col-md-10 col-md-offset-1">
        @include('admin.super_user.partial.super_user_form')
        </div>
	</div>
</div>
</div>

</div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection