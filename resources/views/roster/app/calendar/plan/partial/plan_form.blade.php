                <form method="POST" action="{{route('app::roster::calendar::form::plan_edit')}}" class="form-inline">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="plan_rest_reason_id" value="{{$row->plan_rest_reason_id or ''}}">
    <input type="hidden" name="month_id" value="{{$id}}">
    <input type="hidden" name="entered_on" value="{{$date}}">

        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>予定勤務形態</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <ul>
                    @if(!empty($row->plan_work_type_id))
                    <li>
                        {{$row->PlanType->work_type_name or ''}}
                        @if(!empty($row->PlanType->work_start_time) && !empty($row->PlanType->work_end_time))
                        （{{date('G:i', strtotime($row->PlanType->work_start_time))}} ～ {{date('G:i', strtotime($row->PlanType->work_end_time))}}）
                        @endif
                    </li>
                    @endif
                </ul>
            </div>
        </div>


        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <label>予定休暇理由</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
{{--                 <p>{{$row->plan_rest_reason_id or ''}}</p> --}}
                <ul><li>{{$row->PlanRest->rest_reason_name or 'なし'}}</li></ul>
            </div>
        </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <label>予定勤務時間</label>
        </div>
        <div class="col-md-10 col-md-offset-1">
            <div class="form-group">
                <select class="form-control input-sm" name="plan_start_hour">
                    <option value="null">時</option>
                    @for($i = 0; $i < 24; $i++)
                    <option
                        value="{{$i}}"
                        @if($i === $plan_start_hour) selected="selected" @endif>{{$i}}</option>
                    @endfor
                </select>
                :
                <select class="form-control input-sm" name="plan_start_time">
                    <option value="null">分</option>
                    @for($i = 0; $i < 60; $i+=5)
                    <option
                        value="{{$i}}"
                        @if($i === $plan_start_time) selected="selected" @endif>{{sprintf('%02d', $i)}}</option>
                    @endfor
                </select>
                ～
                <select class="form-control input-sm" name="plan_end_hour">
                    <option value="null">時</option>
                    @for($i = 0; $i < 24; $i++)
                    <option
                        value="{{$i}}"
                        @if($i === $plan_end_hour) selected="selected" @endif>{{$i}}</option>
                    @endfor
                </select>
                :
                <select class="form-control input-sm" name="plan_end_time">
                    <option value="null">分</option>
                    @for($i = 0; $i < 60; $i+=5)
                    <option
                        value="{{$i}}"
                        @if($i === $plan_end_time) selected="selected" @endif>{{sprintf('%02d', $i)}}</option>
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
                    <button class="btn btn-primary">更新</button>
            </div>
        </div>
    </div>
                </form>