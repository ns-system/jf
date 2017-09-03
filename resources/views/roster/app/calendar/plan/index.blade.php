@extends('layout')

@section('title', '予定入力')

@section('header')
@parent
@section('brand', '勤怠管理システム')

<style type="text/css">
    .calendar th,
    .calendar td{
        border: none;
    }
    .small{ font-weight: bolder; }
</style>
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
            {{date('Y年n月j日', strtotime($date))}}
            <a class="btn btn-danger btn-sm"
                @if(!empty($row->id) && !$row->is_plan_accept && !$row->is_actual_accept)
                    href="{{route('app::roster::calendar::form::delete', ['id'=>$row->id])}}"
                    onclick="return confirm('データを削除してもよろしいですか？');" 
                @else disabled
                @endif
                >削除</a>
        </h2>
    </div>

<div class="col-md-6">
{{--     <div class="border-bottom"><h2>予定入力フォーム<small> - {{date('Y年n月j日', strtotime($date))}}</small></h2></div> --}}
    @if(empty($row->is_plan_accept) || !$row->is_plan_accept)
        <div class="panel panel-primary" @if(!empty($row) && $row->is_plan_accept) @endif>
            <div class="panel-heading">
                <p>勤務予定入力フォーム</p>
            </div>
            <div class="panel-body">
            @include('roster.app.calendar.plan.partial.plan_form')
            </div>
        </div>
    @else <div class="alert alert-success" role="alert">予定データは承認されました。</div> @endif
</div>


<div class="col-md-6">
{{--     <div class="border-bottom"><h2>実績入力フォーム<small> - {{date('Y年n月j日', strtotime($date))}}</small></h2></div> --}}
    @if(empty($row->is_plan_entry) || !$row->is_plan_entry) <div class="alert alert-warning" role="alert">先に予定データを入力してください。</div>
    @elseif(isset($row->is_actual_accept) && !$row->is_actual_accept)
        <div class="panel panel-primary">
            <div class="panel-heading">
                <p>勤務実績入力フォーム</p>
            </div>
            <div class="panel-body">
            @include('roster.app.calendar.plan.partial.actual_form')
            </div>
        </div>
    @else <div class="alert alert-success" role="alert">実績データは承認されました。</div> @endif
</div>

@endsection

@section('footer')
@parent
@endsection