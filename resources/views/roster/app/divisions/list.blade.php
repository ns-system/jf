@extends('layout')

@section('title', '勤務リスト')

@section('header')
@parent
@section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('partial.check_sidebar')
</div>
@endsection

@section('content')
<div class="col-md-10">
    @include('partial.alert')
    <div class="border-bottom">
        <h2>{{$div_name}} <small> - {{$date}}</small></h2>
    </div>
    <div class="text-right col-md-12">
        <div data-spy="affix" data-offset="100" style="right: 30px; top: 120px; margin-bottom: 10px;">
            <div class="btn-group">
                <a href="{{route('app::roster::division::index', ['div'=>$div])}}" class="btn btn-primary btn-sm" style="min-width: 100px;">
                    <span class="glyphicon glyphicon-backward" aria-hidden="true"></span> 戻る
                </a><span></span>

                <a href="{{route('app::roster::division::show', ['div'=>$div,'ym'=>$next])}}" class="btn btn-success btn-sm" style="min-width: 100px;">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> 翌月へ
                </a><span></span>

                <a href="{{route('app::roster::division::show', ['div'=>$div,'ym'=>$prev])}}" class="btn btn-warning btn-sm" style="min-width: 100px;">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> 前月へ
                </a>
            </div>
        </div>
    </div>
    @include('roster.app.divisions.partial.list')
</div>
@endsection

@section('footer')
@parent
@endsection