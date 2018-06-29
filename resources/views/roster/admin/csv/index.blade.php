@extends('layout')

@section('title', 'CSV出力')

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
    <div class="border-bottom"><h2>勤怠管理システム CSV出力 <small> - 月選択</small></h2></div>

    @if(!$months->isEmpty())
    @foreach($months as $m)
    <div class="col-md-3" style="margin-bottom: 10px;">
        @if($m->month_id == $current)
        <a href="{{route('admin::roster::csv::show', ['month'=>$m->month_id])}}" class="btn btn-warning btn-lg btn-block">
            {{date('Y年n月', strtotime($m->month_id . '01'))}}
            <span class="badge">{{$m->cnt}}件</span>
        </a>
        @else
        <a href="{{route('admin::roster::csv::show', ['month'=>$m->month_id])}}" class="btn btn-primary btn-lg btn-block" >
            {{date('Y年n月', strtotime($m->month_id . '01'))}}
            <span class="badge">{{$m->cnt}}件</span>
        </a>
        @endif
    </div>
    @endforeach
    @else
    <div class="alert alert-warning" role="alert">データが見つかりませんでした。</div>
    @endif
</div>
@endsection

@section('footer')
@parent
@endsection