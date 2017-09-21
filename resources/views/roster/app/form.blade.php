@extends('layout')

@section('title', '勤怠管理')

@section('header')
@parent
@section('brand', '勤怠管理システム')

<style type="text/css">
	.calendar th,
	.calendar td{
		border: none;
	}
</style>
@endsection

@section('sidebar')
<div class="col-md-2">
</div>
@endsection

@section('content')
<div class="col-md-10">
<h2>{{date('Y年n月j日', strtotime($entered_on))}}</h2>
<form class="form-inline" role="form" method="POST" action="/roster/app/calendar/form/full/edit">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="id" value="{{$row->id or null}}">
    <input type="hidden" name="entered_on" value="{{$entered_on}}">
    <input type="hidden" name="month_id" value="{{$month_id}}">


    <div class="col-md-5">
        <div class="panel panel-primary">
            <div class="panel-heading">予定・残業申請</div>
            <div class="panel-body">
                @include('roster.app.partial.plan_form')
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="panel panel-primary">
            <div class="panel-heading">実績</div>
            <div class="panel-body">
                @include('roster.app.partial.actual_form')
            </div>
        </div>
    </div>
</form>
</div>
@endsection

@section('footer')
@parent
@endsection