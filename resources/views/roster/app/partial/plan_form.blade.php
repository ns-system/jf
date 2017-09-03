        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>予定勤務形態</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <ul>
                    @if($row != [null])
                    <li><p>{{$row->plan_work_type_id or ''}}</p></li>
                    <li><p>{{$row->PlanType->work_type_name or ''}}</p></li>
                    <li><p>{{$row->PlanType->work_start_time or ''}} ～ {{$row->PlanType->work_end_time or ''}}</p></li>
                    @endif
                </ul>
            </div>
        </div>


        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>予定休暇理由</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <p>{{$row->plan_rest_reason_id or ''}}</p>
                <p>{{$row->PlanRest->rest_reason_name or ''}}</p>
            </div>
        </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <label>予定勤務時間</label>
        </div>
        <div class="col-md-10 col-md-offset-1">
            <div class="form-group">
                <select class="form-control" name="plan_start_hour">
                    <option value="null">時</option>
                    @for($i = 0; $i < 24; $i++)
                    <option
                        value="{{sprintf('%02d',$i)}}"
                        @if($i == (int) $times['p_s_hour']) selected="selected" @endif>{{$i}}</option>
                    @endfor
                </select>
                :
                <select class="form-control" name="plan_start_time">
                    <option value="null">分</option>
                    @for($i = 0; $i < 60; $i+=5)
                    <option
                        value="{{sprintf('%02d', $i)}}"
                        @if($i == (int) $times['p_s_time']) selected="selected" @endif>{{sprintf('%02d', $i)}}</option>
                    @endfor
                </select>
                ～
                <select class="form-control" name="plan_end_hour">
                    <option value="null">時</option>
                    @for($i = 0; $i < 24; $i++)
                    <option
                        value="{{sprintf('%02d', $i)}}"
                        @if($i == (int) $times['p_e_hour']) selected="selected" @endif>{{$i}}</option>
                    @endfor
                </select>
                :
                <select class="form-control" name="plan_end_time">
                    <option value="null">分</option>
                    @for($i = 0; $i < 60; $i+=5)
                    <option
                        value="{{sprintf('%02d', $i)}}"
                        @if($i == (int) $times['p_e_time']) selected="selected" @endif>{{sprintf('%02d', $i)}}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>予定残業理由</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="form-group" style="width: 100%;">
                    <input type="text" name="plan_overtime_reason" value="{{$row->plan_overtime_reason or ''}}" class="form-control" style="width: 100%;" placeholder="残業する場合、理由を記入してください">
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 15px;">
            <div class="col-md-10 col-md-offset-1 text-right">
                <div class="btn-group">
                    <button class="btn btn-danger">削除</button>
                    <button class="btn btn-primary" formaction="/roster/app/calendar/form/plan/edit">更新</button>
                </div>
            </div>
        </div>
    </div>