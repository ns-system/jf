
{{-- modal --}}
@if(!empty($r))
<form method="POST" action="{{route('app::roster::calendar::form::actual_edit', ['ym'=>$ym, 'id'=>$r->id])}}" class="form-inline" style="margin: 0;">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="modal fade" id="actual-{{$r->id}}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary-important">
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    <h4 class="modal-title">実入力フォーム <small> - {{$r->entered_on}}</small></h4>
                </div>
                <div class="modal-body" style="color: #444;">
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-md-10 col-md-offset-1">
                            <label>実勤務形態</label>
                        </div>
                        <div class="col-md-10 col-md-offset-1">
                            <div class="form-group" style="display:block;">
                                <select class="form-control" name="actual_work_type_id" style="width: 100%;">
                                    <option value="0"></option>
                                    @foreach($types as $t_key => $t)
                                    <option
                                    value="{{$t_key}}"
                                    @if(!empty($r->actual_work_type_id) && $r->actual_work_type_id == $t_key) selected
                                    @elseif(!empty($r->plan_work_type_id) && $r->plan_work_type_id == $t_key) selected @endif
                                    >{{$t['name']}} {{$t['time']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-md-10 col-md-offset-1">
                            <label>実休暇理由</label>
                        </div>
                        <div class="col-md-10 col-md-offset-1">
                            <div class="form-group" style="display:block;">
                                <select class="form-control" name="actual_rest_reason_id" style="width: 100%;">
                                    <option value="0">休暇の場合、選択してください</option>
                                    @foreach($rests as $rest_key => $rest)
                                    <option
                                    value="{{$rest_key}}"
                                    @if(!empty($r->actual_rest_reason_id) && $r->actual_rest_reason_id == $rest_key) selected
                                    @elseif(!empty($r->plan_rest_reason_id) && $r->plan_rest_reason_id == $rest_key) selected @endif
                                    >{{$rest}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-md-10 col-md-offset-1">
                            <label>実勤務時間</label>
                        </div>
                        <div class="col-md-10 col-md-offset-1">
                            <div class="form-group" data-toggle="reset-time">
                                <select class="form-control input-sm" name="actual_start_hour" data-toggle="clear">
                                    <option>時</option>
                                    @for($i = 0; $i < 24; $i++)
                                    <option
                                    value="{{$i}}"
                                    @if($times[$r->id]['is_actual_entry'])
                                    @if($times[$r->id]['actual_start_hour'] === $i) selected @endif
                                    @else
                                    @if($times[$r->id]['plan_start_hour'] === $i) selected @endif
                                    @endif
                                    >{{$i}}</option>
                                    @endfor
                                </select>
                                :
                                <select class="form-control input-sm" name="actual_start_time" data-toggle="clear">
                                    <option>分</option>
                                    @for($i = 0; $i < 60; $i+=5)
                                    <option
                                    value="{{$i}}"
                                    @if($times[$r->id]['is_actual_entry'])
                                    @if($times[$r->id]['actual_start_time'] === $i) selected @endif
                                    @else
                                    @if($times[$r->id]['plan_start_time'] === $i) selected @endif
                                    @endif
                                    >{{sprintf('%02d', $i)}}</option>
                                    @endfor
                                </select>
                                ～
                                <select class="form-control input-sm" name="actual_end_hour" data-toggle="clear">
                                    <option value="">時</option>
                                    @for($i = 0; $i < 24; $i++)
                                    <option
                                    value="{{$i}}"
                                    @if($times[$r->id]['is_actual_entry'])
                                    @if($times[$r->id]['actual_end_hour'] === $i) selected @endif
                                    @else
                                    @if($times[$r->id]['plan_end_hour'] === $i) selected @endif
                                    @endif
                                    >{{$i}}</option>
                                    @endfor
                                </select>
                                :
                                <select class="form-control input-sm" name="actual_end_time" data-toggle="clear">
                                    <option value="">分</option>
                                    @for($i = 0; $i < 60; $i+=5)
                                    <option
                                    value="{{$i}}"
                                    @if($times[$r->id]['is_actual_entry'])
                                    @if($times[$r->id]['actual_end_time'] === $i) selected @endif
                                    @else
                                    @if($times[$r->id]['plan_end_time'] === $i) selected @endif
                                    @endif
                                    >{{sprintf('%02d', $i)}}</option>
                                    @endfor
                                </select>
                                <button type="button" class="btn btn-sm btn-warning clear-time">時間クリア</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <label>実残業理由</label>
                        </div>
                        <div class="col-md-10 col-md-offset-1">
                            <div class="form-group" style="width: 100%;">
                                <input
                                type="text"
                                name="actual_overtime_reason"
                                @if(!empty($r->actual_overtime_reason))   value="{{$r->actual_overtime_reason}}"
                                @elseif(!empty($r->plan_overtime_reason)) value="{{$r->plan_overtime_reason}}" @endif
                                class="form-control"
                                style="width: 100%;"
                                placeholder="残業した場合、理由を記入してください"
                                >
                                @if($r->is_actual_reject && !empty($r->reject_reason)) <small class="helpBlock text-danger">{{$r->reject_reason}}</small> @endif
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
