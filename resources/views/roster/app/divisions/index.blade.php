@extends('layout')

@section('title', '部署')

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
    <h2>勤務データ <small> - {{ date('Y年n月分') }}</small></h2>
  </div>
  @foreach($divs as $key => $div)
  <div class="col-md-4">
    <a class="btn btn-primary btn-block" href="{{ route('app::roster::division::show', ['division'=>$div->division_id, 'ym'=>intval(date('Ym'))]) }}">{{$div->division_name}}</a>
  </div>
  @if($key == 2) <div class="col-md-12" style="margin-bottom: 20px;"></div> @endif
  @endforeach
</div>
@endsection

@section('footer')
@parent
@endsection