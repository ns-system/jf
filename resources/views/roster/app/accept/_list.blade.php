@extends('layout')

@section('title', 'ユーザー権限')

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
    <h2>
        <a href="#plan_form" data-toggle="collapse"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></a>
        {{$div->division_name}} 予定データ承認 <small> - {{$display}}</small>
    </h2>
</div>
<div class="row collapse in" id="plan_form">
@if(!empty($plans))
    <form method="POST" action="{{route('app::roster::accept::all', ['type'=>'plan'])}}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        @foreach($plans as $p)
        @include('roster.app.accept.partial.plan_form')
        @endforeach
        <div class="col-md-10 col-md-offset-1 text-right">
            <button class="btn btn-success btn-block" onclick="return confirm('全ての予定データが更新されますがよろしいですか？');">予定データを一括承認する</button>
        </div>
    </form>
@else
<div class="col-md-10 col-md-offset-1"><div class="alert alert-warning" role="alert">承認していない予定データはありません。</div></div>
@endif
</div>

<div class="border-bottom">
    <h2>
        <a href="#actual_form" data-toggle="collapse"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></a>
        {{$div->division_name}} 実績データ承認<small> - {{$display}}</small>
    </h2>
</div>
<div class="row collapse in" id="actual_form">
@if(!empty($actuals))
    <form method="POST" action="{{route('app::roster::accept::all', ['type'=>'actual'])}}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        @foreach($actuals as $a)
        @include('roster.app.accept.partial.actual_form')
        @endforeach
        <div class="col-md-10 col-md-offset-1 text-right">
            <button class="btn btn-success btn-block" onclick="return confirm('全ての予定データが更新されますがよろしいですか？');">実績データを一括承認する</button>
        </div>
    </form>
@else
<div class="col-md-10 col-md-offset-1"><div class="alert alert-warning" role="alert">承認していない実績データはありません。</div></div>
@endif
</div>

</div>


@endsection

@section('footer')
@parent
@endsection