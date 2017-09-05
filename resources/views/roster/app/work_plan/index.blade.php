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
<div class="border-bottom"><h2>勤務予定データ作成<small> - 月選択</small></h2></div>
@foreach($months as $month)
<div class="col-md-4" style="margin-bottom: 20px;">
    <a href="{{route('app::roster::work_plan::division', ['month'=>$month['id']])}}"
        @if($month['id'] == $current) class="btn btn-warning btn-block btn-lg"
        @else                         class="btn btn-success btn-block btn-lg" @endif
    >{{$month['display']}}</a>
</div>
@endforeach

</div>
@endsection

@section('footer')
@parent
@endsection