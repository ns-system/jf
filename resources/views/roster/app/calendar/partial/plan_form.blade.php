
{{-- modal --}}
@if(!empty($r))
<form method="POST" action="{{route('app::roster::calendar::form::plan_edit', ['ym'=>$ym, 'id'=>$r->id])}}" class="form-inline" style="margin: 0;">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="plan_rest_reason_id" value="{{$r->plan_rest_reason_id}}">

<div class="modal fade" id="plan-{{$r->id}}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary-important">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <h4 class="modal-title">予定入力フォーム</h4>
            </div>
            <div class="modal-body" style="color: #444;">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <label>予定勤務形態</label>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <ul>
                            <li>
                            @if(!empty($r->plan_work_type_id)) {{$types[$r->plan_work_type_id]['name']}} {{$types[$r->plan_work_type_id]['time']}}
                            @else 指定なし @endif
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <label>予定休暇理由</label>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
        {{--                 <p>{{$row->plan_rest_reason_id or ''}}</p> --}}
                        <ul><li>@if(!empty($r->plan_rest_reason_id)) {{$rests[$r->plan_rest_reason_id]}} @else なし @endif</li></ul>
                    </div>
                </div>

                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-10 col-md-offset-1">
                        <label>予定勤務時間</label>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="form-group">
                            <select class="form-control input-sm" name="plan_start_hour" data-toggle="clear">
                                <option>時</option>
                                @for($i = 0; $i < 24; $i++)
                                <option
                                    value="{{$i}}"
                                    @if($i === $times[$r->id]['plan_start_hour']) selected="selected" @endif>{{$i}}</option>
                                @endfor
                            </select>
                            :
                            <select class="form-control input-sm" name="plan_start_time" data-toggle="clear">
                                <option>分</option>
                                @for($i = 0; $i < 60; $i+=5)
                                <option
                                    value="{{$i}}"
                                    @if($i === $times[$r->id]['plan_start_time']) selected="selected" @endif>{{sprintf('%02d', $i)}}</option>
                                @endfor
                            </select>
                            ～
                            <select class="form-control input-sm" name="plan_end_hour" data-toggle="clear">
                                <option value="">時</option>
                                @for($i = 0; $i < 24; $i++)
                                <option
                                    value="{{$i}}"
                                    @if($i === $times[$r->id]['plan_end_hour']) selected="selected" @endif>{{$i}}</option>
                                @endfor
                            </select>
                            :
                            <select class="form-control input-sm" name="plan_end_time" data-toggle="clear">
                                <option value="">分</option>
                                @for($i = 0; $i < 60; $i+=5)
                                <option
                                    value="{{$i}}"
                                    @if($i === $times[$r->id]['plan_end_time']) selected="selected" @endif>{{sprintf('%02d', $i)}}</option>
                                @endfor
                            </select>
                            <button type="button" class="btn btn-sm btn-warning clear-time">時間クリア</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <label>予定残業理由</label>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="form-group" style="width: 100%;">
                            <input type="text" name="plan_overtime_reason" value="{{$r->plan_overtime_reason}}" class="form-control" style="width: 100%;" placeholder="残業する場合、理由を記入してください">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-10 col-md-offset-1 text-right">
                    <button type="submit" class="btn btn-success">更新する</button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endif
