@extends('layout')
@section('title', '処理確認')

@section('header')
@parent
@section('brand', '月次処理')
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
        <div class="border-bottom"><h2>USBストレージ選択</h2></div>

<form method="POST" action="{{route('admin::super::month::copy',['id'=>$id])}}">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}">


    <div class="form-group">
        <input type="text" name="usb_path" class="form-control" value="F:/">
        <span id="helpBlock" class="help-block"><small class="text-warning">USBストレージのドライブ名を入力してください。</small></span>
    </div>
        <button type="submit">sumbmit</button>
</form>
    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection