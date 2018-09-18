@extends('layout')

@section('title', '勤務予定データ')

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
    <div class="border-bottom"><h2>勤務予定データ作成<small> - {{$user->last_name}} {{$user->first_name}}さん</small></h2></div>

    <form method="POST" action="{{route('app::roster::work_plan::edit', ['month'=>$month, 'id'=>$id])}}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="text-right" style="margin-bottom: 10px">
            <div data-spy="affix" style="top: 100px; right: 30px;" data-offset-top="150">

                <div class="btn-group">
                    <a href="{{route('app::roster::work_plan::division', ['month'=>$month])}}" class="btn btn-primary btn-sm" style="min-width: 100px;">
                        <span class="glyphicon glyphicon-backward" aria-hidden="true"></span> 戻る
                    </a>
                    <span></span>
                    <button type="submit" class="btn btn-success btn-sm" style="min-width: 100px;">更新する</button>
                </div>
            </div>
        </div>

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
                        <strong @if($day['holiday']) class="text-danger-light" data-toggle="tooltip" data-placement="right" title="{{$day['holiday_name']}}"
                        @elseif($day['week'] == 0) class="text-danger-light"
                        @elseif($day['week'] == 6) class="text-info-light"
                        @endif>{{date('n月j日', strtotime($day['date']))}} （{{$day['week_name']}}）</strong>
                        <input type="hidden" name="entered_on[]" value="{{$day['date']}}" style="color: #777;">
                    </td>
                    @if(!empty($day['data']))
                    <?php $data = $day['data']; ?>
                    {{-- 承認済み→入力不可 --}}
                    {{-- @if($data->is_plan_accept || $data->is_actual_accept || $data->is_plan_entry || $data->is_actual_entry) --}}
                    @if($data->is_plan_accept || $data->is_actual_accept)
                    <td><span class="label label-success" data-toggle="tooltip" title="データは承認されています。以降の修正は行えません。">勤務データ承認済</span></td>
                    {{-- <td><span class="label label-warning" data-toggle="tooltip" title="ユーザーが勤務データを入力しているため、修正は行えません。">勤務データ入力済</span></td> --}}
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
                    {{-- 未承認→入力可能 --}}
                    @else
                    @if($data->is_plan_entry)
                    <td><span class="label label-warning" data-toggle="tooltip" title="予定データは入力されていますが、修正できます。">予定データ未承認</span></td>
                    @else
                    <td><span class="label label-info" data-toggle="tooltip" title="予定データは入力されていますが、修正できます。">予定データ入力済</span></td>
                    @endif
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
                </tr>
                @endforeach
            </tbody>

        </table>
    </form>
</div>


@endsection

@section('footer')
@parent
@endsection