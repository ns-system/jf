@extends('layout')

@section('title', '勤務リスト')

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
<div class="text-right col-md-12">
    <div class="btn-group">
        <a href="{{route('app::roster::division::index', ['div'=>$div])}}" class="btn btn-primary" style="min-width: 125px;">
            <span class="glyphicon glyphicon-backward" aria-hidden="true"></span> 戻る
        </a><span></span>

        <a href="{{route('app::roster::division::show', ['div'=>$div,'ym'=>$next])}}" class="btn btn-success" style="min-width: 125px;">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> 翌月へ
        </a><span></span>

        <a href="{{route('app::roster::division::show', ['div'=>$div,'ym'=>$prev])}}" class="btn btn-warning" style="min-width: 125px;">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> 前月へ
        </a>
    </div>
</div>
@endsection

@section('footer')
@parent
@endsection