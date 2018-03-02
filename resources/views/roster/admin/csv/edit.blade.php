@extends('layout')

@section('title', '勤務データ修正')

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
    <div class="border-bottom"><h2>勤務データ修正 <small> - {{date('n月j日', strtotime($roster->entered_on))}} {{$user->last_name}} {{$user->first_name}}さん</small></h2></div>


    <form method="POST" action="{{route('admin::roster::csv::update', ['ym'=>$ym,])}}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="id" value="{{$roster->id}}">

        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    {{date('n月j日', strtotime($roster->entered_on))}} - {{$user->last_name}} {{$user->first_name}}さん
                </div>
                <div class="panel-body">
                    <div class="col-md-6">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>予定勤務形態</label>
                                <select class="form-control" name="plan_work_type_id">
                                    <option value="0">選択しない</option>
                                    @foreach($types as $t)
                                    <option
                                    value="{{$t['id']}}"
                                    @if($t['id'] == $roster->plan_work_type_id) selected @endif
                                    >{{$t['name']}}@if(!empty($t['time']))（{{$t['time']}}）@endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>予定休暇理由</label>
                                <select class="form-control" name="plan_rest_reason_id">
                                    <option value="0">選択しない</option>
                                    @foreach($rests as $r)
                                    <option
                                    value="{{$r->rest_reason_id}}"
                                    @if($r->rest_reason_id == $roster->plan_rest_reason_id) selected @endif
                                    >{{$r->rest_reason_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label>予定残業開始時間</label>
                                <input
                                type="time"
                                step="300"
                                class="form-control"
                                name="plan_overtime_start_time"
                                @if(!empty($roster->plan_overtime_start_time)) value="{{date('H:i', strtotime($roster->plan_overtime_start_time))}}" @endif
                                >
                            </div>
                            <div class="col-md-6">
                                <label>予定残業終了時間</label>
                                <input
                                type="time"
                                step="300"
                                class="form-control"
                                name="plan_overtime_end_time"
                                @if(!empty($roster->plan_overtime_end_time)) value="{{date('H:i', strtotime($roster->plan_overtime_end_time))}}" @endif
                                >
                            </div>
                            <div class="col-md-12"><p><small class="text-warning">時刻は00:00の形式で入力してください。</small></p></div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>予定残業理由</label>
                                <input type="text" class="form-control" name="plan_overtime_reason" value="{{$roster->plan_overtime_reason}}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <p><label>予定承認</label></p>
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default btn-sm" data-color="btn-success"><input type="radio" name="plan_accept" value="1" @if($roster->is_plan_accept) checked @endif>承認する</label>
                                    <label class="btn btn-default btn-sm" data-color="btn-danger" ><input type="radio" name="plan_accept" value="0" @if($roster->is_plan_reject) checked @endif>却下する</label>
                                    <label class="btn btn-default btn-sm" data-color="btn-info"   ><input type="radio" name="plan_accept" value="-1">変更しない</label>
                                    <label class="btn btn-default btn-sm" data-color="btn-warning"><input type="radio" name="plan_accept" value="2">リセットする</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>実勤務形態</label>
                                <select class="form-control" name="actual_work_type_id">
                                    <option value="0">選択しない</option>
                                    @foreach($types as $t)
                                    <option
                                    value="{{$t['id']}}"
                                    @if($t['id'] == $roster->actual_work_type_id) selected @endif
                                    >{{$t['name']}}@if(!empty($t['time']))（{{$t['time']}}）@endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>実休暇理由</label>
                                <select class="form-control" name="actual_rest_reason_id">
                                    <option value="0">選択しない</option>
                                    @foreach($rests as $r)
                                    <option
                                    value="{{$r->rest_reason_id}}"
                                    @if($r->rest_reason_id == $roster->actual_rest_reason_id) selected @endif
                                    >{{$r->rest_reason_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label>実残業開始時間</label>
                                <input
                                type="time"
                                step="300"
                                class="form-control"
                                name="actual_overtime_start_time"
                                @if(!empty($roster->actual_overtime_start_time)) value="{{date('H:i', strtotime($roster->actual_overtime_start_time))}}" @endif
                                >
                            </div>
                            <div class="col-md-6">
                                <label>実残業終了時間</label>
                                <input
                                type="time"
                                step="300"
                                class="form-control"
                                name="actual_overtime_end_time"
                                @if(!empty($roster->actual_overtime_end_time)) value="{{date('H:i', strtotime($roster->actual_overtime_end_time))}}" @endif
                                >
                            </div>
                            <div class="col-md-12"><p><small class="text-warning">時刻は00:00の形式で入力してください。</small></p></div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>実残業理由</label>
                                <input type="text" class="form-control" name="actual_overtime_reason" value="{{$roster->actual_overtime_reason}}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <p><label>実績承認</label></p>
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default btn-sm" data-color="btn-success"><input type="radio" name="actual_accept" value="1" @if($roster->is_actual_accept) checked @endif>承認する</label>
                                    <label class="btn btn-default btn-sm" data-color="btn-danger" ><input type="radio" name="actual_accept" value="0" @if($roster->is_actual_reject) checked @endif>却下する</label>
                                    <label class="btn btn-default btn-sm" data-color="btn-info"   ><input type="radio" name="actual_accept" value="-1">変更しない</label>
                                    <label class="btn btn-default btn-sm" data-color="btn-warning"><input type="radio" name="actual_accept" value="2">リセットする</label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="panel-footer">
                    <div class="text-right">
                        <div class="btn-group">
                            <a href="{{route('admin::roster::csv::show', ['ym'=>$ym])}}" style="width: 100px;" class="btn btn-success">戻る</a>
                            <button type="submit" class="btn btn-warning" style="width: 100px;" onclick="return confirm('データを更新することによって整合性が取れなくなる場合があります。データの強制変更を行いますか？');">更新する</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endsection

    @section('footer')
    @parent
    @endsection