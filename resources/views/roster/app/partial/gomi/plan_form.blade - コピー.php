
<!-- モーダル・ダイアログ -->
<div class="modal fade" id="plan_form" tabindex="-1">

    <form class="form-inline" role="form" method="POST" action="/roster/app/calendar/plan/edit">
        {{-- CSRF対策--}}
        <input type="text" name="_token" value="{{ csrf_token() }}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary-important" style="border-top-right-radius: 3px; border-top-left-radius: 3px;">
                    <button type="button" class="close" data-dismiss="modal"><span style="color: #fff;">&times;</span></button>
                    <h4 class="modal-title">予定入力・残業申請 <small class="display_date"></small></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2 col-md-offset-1">
                            <label>予定</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="id" class="roster_id" value="">
                            <input type="text" name="entered_on" class="entered_on" value="">
                            <input type="text" name="month_id" value="{{$id}}">
                            （上長が入力したデータそのまま）
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="form-group" style="width: 100%;">
                                <label>休暇理由</label>
                                <select class="form-control" name="plan_rest_reason_id" id="plan_rest_reason_id" style="width: 100%;">
                                    <option value="" selected="selected"></option>
                                    @foreach($rests as $rest)
                                    <option value="{{$rest->rest_reason_id}}">{{$rest->rest_reason_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <label>予定勤務時間</label>
                        </div>
                        <div class="col-md-10 col-md-offset-1">
                            <div class="form-group">
                                <select class="form-control time"  data-time="hour" data-target="start" data-type="plan">
                                    @for($i = 0; $i < 24; $i++)
                                    <option value="{{sprintf('%02d',$i)}}">{{$i}}</option>
                                    @endfor
                                </select>
                                :
                                <select class="form-control time" data-time="time" data-target="start" data-type="plan">
                                    @for($i = 0; $i <= 60; $i+=5)
                                    <option value="{{sprintf('%02d', $i)}}">{{sprintf('%02d', $i)}}</option>
                                    @endfor
                                </select>
                                ～
                                <select class="form-control time" data-time="hour"  data-target="end" data-type="plan">
                                    @for($i = 0; $i < 24; $i++)
                                    <option value="{{sprintf('%02d', $i)}}">{{$i}}</option>
                                    @endfor
                                </select>
                                :
                                <select class="form-control time" data-time="time"  data-target="end" data-type="plan">
                                    @for($i = 0; $i < 60; $i+=5)
                                    <option value="{{sprintf('%02d', $i)}}">{{sprintf('%02d', $i)}}</option>
                                    @endfor
                                </select>
                                <input type="text" name="plan_overtime_start_time" id="plan_start_time">
                                <input type="text" name="plan_overtime_end_time" id="plan_end_time">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <label>残業理由</label>
                            <div class="form-group" style="width: 100%;">
                                <input type="text" name="plan_overtime_reason" id="plan_overtime_reason" class="form-control" style="width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <button type="submit"
                            class="btn btn-danger"
                            onclick="return confirm('入力データを削除してもよろしいですか？');"
                            formaction="/roster/app/calendar/plan/delete">削除</button>
                        <button type="submit" class="btn btn-primary">更新</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>