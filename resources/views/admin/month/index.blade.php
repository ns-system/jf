@extends('layout')
@section('title', '月別マスタ')

@section('header')
@parent
@section('brand', '月別マスタ')
@endsection


@section('brand')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')
</div>
@endsection

<div style="margin-top: 100px;"></div>


@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        @include('partial.alert')
        <div class="border-bottom"><h2>月別マスタ選択</h2></div>
        @include('admin.month.partial.month_form')
        {!! $rows->render() !!}
    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection