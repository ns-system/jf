@extends('layout')
@section('title', '処理状況')

@section('header')
@parent
@section('brand', '処理状況確認')
@endsection


@section('brand')
@endsection

@section('sidebar')
<div class="col-md-2">
  @include('partial.check_sidebar')
</div>
@endsection



@section('content')
<div style="margin-top: 100px;"></div>
<div class="col-md-10">
  <div class="container-fluid">
    @include('partial.alert')
    <div class="border-bottom"><h2>処理状況確認 <small> - {{date('Y年n月分', strtotime($id.'01'))}} （{{$count}}件）</small></h2></div>


    <div data-spy="affix" style="top: 100px; right: 30px;" data-offset-top="110">
      <div class="text-right" style="margin-bottom: 10px;">
        @if(isset($parameters))    {!! $rows->appends($parameters)->render() !!}
        @elseif(!$rows->isEmpty()) {!! $rows->render() !!} @endif
      </div>
      <div class="text-right">
        <div class="btn-group">
          <a href="{{route('admin::super::month::export', ['id'=>$id])}}" class="btn btn-primary btn-sm">処理リスト</a>
          <a href="{{route('admin::super::month::export_nothing', ['id'=>$id])}}" class="btn btn-warning btn-sm">処理不能リスト</a>
          <button class="btn btn-success btn-sm"  data-toggle="modal" data-target="#search">検索する</button>
        </div>
      </div>
    </div>

    @include('admin.month.partial.status_search_form')
    @include('admin.month.partial.status_list')
  </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection