@extends('layout')

@section('title', '責任者代理設定')

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
    <div class="col-md-9">
<div class="container-fluid">
        @include('partial.alert')
        <div class="border-bottom"><h2>責任者代理設定</h2></div>
        @if(empty($rows))
        	<div class="alert alert-warning" role="alert">ユーザーが見つかりませんでした。</div>
        @else
        	@include('roster.app.chief.partial.user_list')
        @endif
</div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection