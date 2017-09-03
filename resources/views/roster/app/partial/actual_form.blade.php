        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>実勤務形態</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="form-group" style="width: 100%;">
                    <select class="form-control" name="actual_work_type_id" style="width: 100%;">
                        @foreach($types as $type)
                        <option value="{{$type->work_type_id}}"
                            @if($row != null && $type->work_type_id == (int) $row->actual_work_type_id) selected="selected" @endif>{{$type->work_type_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>実休暇理由</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="form-group" style="width: 100%;">
                    <select class="form-control" name="actual_rest_reason_id" style="width: 100%;">
                        <option value="null">休暇の場合選択してください</option>
                        @foreach($rests as $rest)
                        <option value="{{$rest->rest_reason_id}}"
                            @if($row != null && $rest->rest_reason_id == (int) $row->actual_rest_reason_id) selected="selected" @endif>{{$rest->rest_reason_name}}</option>
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
                <select class="form-control" name="actual_start_hour">
                    <option value="null">時</option>
                    @for($i = 0; $i < 24; $i++)
                    <option
                        value="{{sprintf('%02d',$i)}}"
                        @if($i == (int) $times['a_s_hour']) selected="selected" @endif>{{$i}}</option>
                    @endfor
                </select>
                :
                <select class="form-control" name="actual_start_time">
                    <option value="null">分</option>
                    @for($i = 0; $i < 60; $i+=5)
                    <option
                        value="{{sprintf('%02d', $i)}}"
                        @if($i == (int) $times['a_s_time']) selected="selected" @endif>{{sprintf('%02d', $i)}}</option>
                    @endfor
                </select>
                ～
                <select class="form-control" name="actual_end_hour">
                    <option value="null">時</option>
                    @for($i = 0; $i < 24; $i++)
                    <option
                        value="{{sprintf('%02d', $i)}}"
                        @if($i == (int) $times['a_e_hour']) selected="selected" @endif>{{$i}}</option>
                    @endfor
                </select>
                :
                <select class="form-control" name="actual_end_time">
                    <option value="null">分</option>
                    @for($i = 0; $i < 60; $i+=5)
                    <option
                        value="{{sprintf('%02d', $i)}}"
                        @if($i == (int) $times['a_e_time']) selected="selected" @endif>{{sprintf('%02d', $i)}}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>実残業理由</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="form-group" style="width: 100%;">
                    <input type="text" name="actual_overtime_reason" class="form-control" style="width: 100%;" placeholder="残業した場合、理由を記入してください" value="{{$row->actual_overtime_reason or ''}}">
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 15px;">
            <div class="col-md-10 col-md-offset-1 text-right">
                <div class="btn-group">
                    <button class="btn btn-danger">削除</button>
                    <button class="btn btn-primary" formaction="/roster/app/calendar/form/actual/edit">更新</button>
                </div>
            </div>
        </div>
    </div>