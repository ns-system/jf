@extends('layout')

@section('title', 'ホーム')

@section('header')
@parent
@section('brand', 'ホーム')
@endsection

@section('sidebar')
<div class="col-md-2">
  @include('partial.check_sidebar')
</div>
@endsection


@section('content')
<div class="col-md-10">
  <div class="container-fluid">
    @include('partial.alert')
    <div class="well" style="padding: 0;">
      @include('auth.partial.notification')
  </div>
  <div class="row">
      @if($is_chief)
      <div class="col-md-4">
        @include('roster.home_notice.chief')
    </div>
    @endif
    <div class="col-md-4">
        @include('roster.home_notice.user')
    </div>
    <div class="col-md-4">
        @include('roster.home_notice.chief_log')
    </div>

    <div class="col-md-12 well">
        <h2 class="border-bottom">入力状況</h2>
        @if(!empty($rows) && !$rows->isEmpty())
        @include('partial.entered_users_chart', ['rows'=>$rows, 'height'=>'300'])
        @else
        <b class="text-danger">データが見つかりませんでした。</b>
        @endif
    </div>
</div>

{{--     <h1>main content</h1> --}}
</div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection