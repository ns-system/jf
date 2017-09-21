@extends('layout')

@section('title', 'ホーム')

@section('header')
@parent
    @section('brand', 'ホーム')
@endsection

@section('sidebar')
    <div class="col-md-2">
        @include('app.sidebar.sidebar')
    </div>
@endsection


@section('content')
    <div class="col-md-9">
<div class="container-fluid">
        @include('partial.alert')
    <h1>main content</h1>
</div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection