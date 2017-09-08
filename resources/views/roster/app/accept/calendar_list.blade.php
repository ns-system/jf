@extends('layout')

@section('title', 'ユーザー権限')

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

<div class="border-bottom">
    <h2>
        予定データ承認 <small> - {{date('Y年n月', strtotime($ym.'01'))}}</small>
    </h2>
</div>

{{-- 検索用ボタン --}}
<div class="text-right" data-spy="affix" data-offset-top="85" style="z-index: 1;  top: 120px; right: 15px;">
    <div class="btn-group">
        <a class="btn btn-primary btn-xs" id="plan">予定</a>
        <a class="btn btn-primary btn-xs" id="actual">実績</a>
        <a class="btn btn-success btn-xs" id="reset">全て</a>
        <a class="btn btn-warning btn-xs" href="#submit">更新ボタンへ</a>
    </div>
</div>

<form method="POST" action="{{route('app::roster::accept::calendar_accept')}}">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<!-- タブメニュー -->
<div style="margin-bottom: 20px;">
<ul class="nav nav-tabs">
    @foreach($users as $i => $u)
        <li @if($i == 0) class="active" @endif><a href="#id-{{$u->user_id}}" data-toggle="tab">{{$u->name}}さん</a></li>
    @endforeach
</ul>
</div>

<!-- タブ内容 -->
<div class="tab-content">
    @foreach($users as $i => $u)
        <div id="id-{{$u->user_id}}" @if($i == 0) class="tab-pane active" @else class="tab-pane" @endif>
            @include('roster.app.accept.partial.calendar_list')
        </div>
    @endforeach
</div>
<div class="form-group text-right">
    <div class="btn-group" id="submit">
        <a href="{{route('app::roster::accept::index')}}" class="btn btn-primary"><span class="glyphicon glyphicon-backward" aria-hidden="true"></span> 戻る</a>
        <button type="submit" class="btn btn-warning" onclick="return confirm('チェックしたデータが一括で更新されますがよろしいですか？');"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> 一括で更新する</button>
    </div>
</div>
</form>

@endsection

@section('footer')
@parent
<script type="text/javascript">
$(function(){
    $('#plan').click(function(){
        $('tr').show();
        $('tr[data-plan="false"]').hide();
    });
    // $('#all').click(function(){
    //     $('tr[data-plan="false"][data-actual="false"]').hide();
    // });
    $('#actual').click(function(){
        $('tr').show();
        $('tr[data-actual="false"]').hide();
    });
    $('#reset').click(function(){
        $('tr').show();
    });
});
</script>
@endsection