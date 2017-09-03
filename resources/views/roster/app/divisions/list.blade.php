@extends('layout')

@section('title', 'リスト')

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
    <div class="border-bottom">
        <h2>{{$div_name}} <small> - {{$date}}</small></h2>
    </div>
    @include('roster.app.divisions.partial.list')
    </div>
</div>
@endsection

@section('footer')
@parent
@endsection