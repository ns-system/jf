@extends('layout')

@section('title', '勤怠管理')

@section('header')
@parent
@section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('app.partial.sidebar')
</div>
@endsection

@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        <h2>勤怠管理システム　ホーム</h2>
        <p class="btn btn-primary">承認待ち <span class="badge">{{$count}}件</span></p>
        <p>承認済み<span class="badge">{{$count}}</span></p>
        <p>却下<span class="badge">{{$count}}</span></p>
    </div>
</div>
@endsection

@section('footer')
@parent

@endsection