@extends('layout')

@section('title', 'ユーザー権限')

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
    <h2>
        予定データ承認 <small> - {{$ym}}</small>
    </h2>
</div>
    @include('roster.app.accept.partial.list')
@endsection

@section('footer')
@parent
@endsection