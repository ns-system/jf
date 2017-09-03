                <form method="POST" action="{{route('app::roster::calendar::form::actual_edit')}}" class="form-inline">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="month_id" value="{{$id}}">
    <input type="hidden" name="entered_on" value="{{$date}}">

        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>実勤務形態</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="form-group" style="width: 100%;">
                    <select class="form-control" name="actual_work_type_id" style="width: 100%;">
                        @foreach($types as $type)
                        <option
                        value="{{$type->work_type_id}}"
                        @if(!empty($row->actual_work_type_id) && $row->actual_work_type_id == $type->work_type_id) selected
                        @elseif(!empty($row->plan_work_type_id) && $row->plan_work_type_id == $type->work_type_id) selected
                        @elseif(\App\RosterUser::user()->first()->work_type_id == $type->work_type_id) selected
                        @endif
                        >
                            {{$type->work_type_name}}
                            @if(!empty($type->work_start_time) && !empty($type->work_end_time))
                                （{{date('G:i', strtotime($type->work_start_time))}} ～ {{date('G:i', strtotime($type->work_end_time))}}）
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>休暇理由</label>
                <div class="form-group" style="width: 100%;">
                    <select class="form-control" name="actual_rest_reason_id" style="width: 100%;">
                        <option value="">休暇の場合、選択してください</option>
                        @foreach($rests as $rest)
                        <option value="{{$rest->rest_reason_id}}">{{$rest->rest_reason_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-10 col-md-offset-1">

            </div>
        </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <label>実勤務時間</label>
        </div>
        <div class="col-md-10 col-md-offset-1">
            <div class="form-group">
                <select class="form-control input-sm" name="actual_start_hour">
                    <option value="null">時</option>
                    @for($i = 0; $i < 24; $i++)
                    <option
                        value="{{$i}}"
                        @if($i === $actual_start_hour) selected="selected" @endif>{{$i}}</option>
                    @endfor
                </select>
                :
                <select class="form-control input-sm" name="actual_start_time">
                    <option value="null">分</option>
                    @for($i = 0; $i < 60; $i+=5)
                    <option
                        value="{{$i}}"
                        @if($i === $actual_start_time) selected="selected" @endif>{{sprintf('%02d', $i)}}</option>
                    @endfor
                </select>
                ～
                <select class="form-control input-sm" name="actual_end_hour">
                    <option value="null">時</option>
                    @for($i = 0; $i < 24; $i++)
                    <option
                        value="{{$i}}"
                        @if($i === $actual_end_hour) selected="selected" @endif>{{$i}}</option>
                    @endfor
                </select>
                :
                <select class="form-control input-sm" name="actual_end_time">
                    <option value="null">分</option>
                    @for($i = 0; $i < 60; $i+=5)
                    <option
                        value="{{$i}}"
                        @if($i === $actual_end_time) selected="selected" @endif>{{sprintf('%02d', $i)}}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>残業理由</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="form-group" style="width: 100%;">
                    <input type="text" name="actual_overtime_reason" value="{{$row->actual_overtime_reason or ''}}" class="form-control" style="width: 100%;" placeholder="残業した場合、理由を記入してください">
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 15px;">
            <div class="col-md-10 col-md-offset-1 text-right">
                    <button class="btn btn-primary">更新</button>
            </div>
        </div>
    </div>
                </form>