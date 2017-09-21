@extends('layout')

@section('title', '勤務予定データ')

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
<div class="border-bottom"><h2>勤務予定データ作成<small> - {{$user->name}}さん</small></h2></div>

<form method="POST" action="{{route('app::roster::work_plan::edit', ['month'=>$month, 'id'=>$id])}}">
<input type="hidden" name="_token" value="{{ csrf_token() }}">

<table class="table table-hover">
<thead>
	<tr>
		<th class="bg-primary">日付</th>
        <th class="bg-primary">状態</th>
        <th class="bg-primary">勤務形態</th>
        <th class="bg-primary">休暇理由</th>
	</tr>
</thead>

<tbody>
@foreach($days as $day)
	<?php if($day['day'] == 0) { continue; } ?>
	<tr>
		<td class="bg-primary">
			@if($day['holiday'])       <strong class="text-danger-light" data-toggle="tooltip" data-placement="right" title="{{$day['holiday_name']}}">
			@elseif($day['week'] == 0) <strong class="text-danger-light">
			@elseif($day['week'] == 6) <strong class="text-info-light">
			@else                      <strong>
			@endif
			{{date('n月j日', strtotime($day['date']))}} （{{$day['week_name']}}）</strong>
            <input type="hidden" name="entered_on[]" value="{{$day['date']}}" style="color: #777;">
		</td>
        @if(!empty($day['data']))
            <?php $data = $day['data']; ?>
            {{-- 承認済み→入力。削除不可 --}}
            @if($data->is_plan_accept || $data->is_actual_accept || $data->is_plan_entry || $data->is_actual_entry)
                @if($data->is_plan_accept || $data->is_actual_accept)
                    <td><span class="label label-success" data-toggle="tooltip" title="データは承認されています。以降の修正は行えません。">勤務データ承認済</span></td>
                @else
                   <td><span class="label label-warning" data-toggle="tooltip" title="ユーザーが勤務データを入力しているため、修正は行えません。">勤務データ入力済</span></td>
                @endif
                <td>
                    @if(!empty($types[$data->plan_work_type_id]))
                    {{$types[$data->plan_work_type_id]['work_type_name']}}
                    {{$types[$data->plan_work_type_id]['work_time']}}
                    @endif
                </td>
                <td>
                    @if(!empty($types[$data->plan_rest_reason_id]))
                    {{$rests[$data->plan_rest_reason_id]['rest_reason_name']}}
                    @endif
                </td>
            {{-- 未承認→入力・削除可能 --}}
            @else
                <td><span class="label label-info" data-toggle="tooltip" title="予定データは入力されていますが、修正できます。">予定データ入力済</span></td>
                <td>
                    <select class="form-control input-sm" name="work_type[{{$day['date']}}]">
                        <option value="0"></option>
                        @foreach($types as $t)
                            <option
                                value="{{$t['work_type_id']}}"
                                @if(!empty($data->plan_work_type_id) && $data->plan_work_type_id == $t['work_type_id']) selected @endif
                            >{{$t['work_type_name']}} {{$t['work_time']}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="form-control input-sm" name="rest[{{$day['date']}}]">
                        <option value="0">休暇の場合、選択してください</option>
                        @foreach($rests as $r)
                        <option
                            value="{{$r['rest_reason_id']}}"
                            @if(!empty($data->plan_rest_reason_id) && $data->plan_rest_reason_id == $r['rest_reason_id']) selected @endif
                        >{{$r['rest_reason_name']}}</option>
                        @endforeach
                    </select>
                </td>
            @endif
        {{-- 未入力→入力可能 --}}
        @else
            <td><span class="label label-default">勤務データ未入力</span></td>
            <td>
                <select class="form-control input-sm" name="work_type[{{$day['date']}}]">
                    <option value="0"></option>
                    @foreach($types as $t)
                        <option value="{{$t['work_type_id']}}"
                            @if($day['holiday'] || $day['week'] == 6 || $day['week'] == 0)
                            @elseif($t['work_type_id'] == $user->work_type_id) selected
                            @endif
                        >{{$t['work_type_name']}} {{$t['work_time']}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select class="form-control input-sm" name="rest[{{$day['date']}}]">
                    <option value="0">休暇の場合、選択してください</option>
                    @foreach($rests as $r)
                    <option value="{{$r['rest_reason_id']}}">{{$r['rest_reason_name']}}</option>
                    @endforeach
                </select>
            </td>
        @endif
        <td>{{--var_dump($day)--}}</td>
	</tr>
@endforeach
</tbody>

</table>

<div class="text-right">
    <div class="btn-group">
        <a href="{{route('app::roster::work_plan::division', ['month'=>$month])}}" class="btn btn-primary" style="min-width: 125px;">
            <span class="glyphicon glyphicon-backward" aria-hidden="true"></span> 戻る
        </a>
        <span></span>
        <button type="submit" class="btn btn-success" style="min-width: 125px;">更新する</button>
    </div>
</div>


</div>
@endsection

@section('footer')
@parent
@endsection