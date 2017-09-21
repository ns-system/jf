@extends('layout')

@section('title', 'CSVリスト')

@section('header')
@parent
@section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')

</div>
@endsection



@section('content')
<div class="col-md-10">
@include('partial.alert')
<div class="border-bottom"><h2>勤怠管理システム CSV出力 <small> - リスト</small></h2></div>
@include('roster.admin.csv.partial.search')

@if(!empty($rosters))
    @include('roster.admin.csv.partial.list')
@else
<div class="alert alert-warning" role="alert">データが見つかりませんでした。</div>
@endif
</div>
@endsection

@section('footer')
@parent
@endsection