<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <p><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> {{date('n月j日', strtotime($p->entered_on))}} - {{$p->name}}さん</p>
        </div>
        <div class="panel-body">
            <div class="col-md-10 col-md-offset-1">

                @if(!empty($p->plan_rest_reason_id))
                    <label>休暇理由</label>
                    <p>{{$rests[$p->plan_rest_reason_id]['rest_reason_name']}}</p>
                @endif

                @if(!empty($p->plan_overtime_reason))
                    <label>残業理由</label>
                    <p>{{$p->plan_overtime_reason}}</p>
                @endif

                @if(!empty($p->plan_overtime_start_time) && !empty($p->plan_overtime_end_time))
                    <label>勤務時間</label>
                    <p>{{date('G:i', strtotime($p->plan_overtime_start_time))}} 〜 {{date('G:i', strtotime($p->plan_overtime_end_time))}}</p>
                @endif

                <div class="form-group">
                    <div class="btn-group" data-toggle="buttons">
                        <label
                            class="btn btn-default btn-sm"
                            data-color="btn-danger"
                            style="min-width: 100px;">
                            <input
                                type="radio"
                                name="plan[{{$p->form_id}}]"
                                value="0"
                                @if($p->is_plan_reject) checked @endif>却下
                        </label>
                        <label
                            class="btn btn-default btn-sm"
                            data-color="btn-success"
                            style="min-width: 100px;"
                            data-toggle="tooltip"
                            title="一度承認されたデータは更新・修正・取り消しができなくなります。ご注意ください。">
                            <input
                                type="radio"
                                name="plan[{{$p->form_id}}]"
                                value="1"
                                @if($p->is_plan_accept || !$p->is_plan_reject) checked @endif>承認
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="input-group">
                <span class="input-group-addon input-sm">却下理由</span>
                <input type="hidden" name="form_id[{{$p->form_id}}]" value="{{$p->form_id}}">
                <input type="text" name="reject[{{$p->form_id}}]" class="form-control input-sm" placeholder="任意入力項目です。" @if(!empty($p->reject_reason)) value="{{$p->reject_reason}}" @endif>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-warning btn-sm" formaction="{{route('app::roster::accept::part', ['id'=>$p->form_id,'type'=>'plan'])}}" data-toggle="tooltip" title="このデータのみを更新したい場合、個別承認を押してください。">個別承認</button>
                </span>
            </div>
            <p class="text-right"></p>
        </div>
    </div>
</div>