{{-- modal --}}
@if(!empty($r))
<form method="POST" action="{{route('app::roster::calendar::form::actual_edit', ['ym'=>$ym, 'id'=>$r->id])}}" class="form-inline" style="margin: 0;">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="hidden" name="position" class="myposition">

  <div class="modal fade" id="actual-{{$r->id}}" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary-important">
          <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
          <h4 class="modal-title">実入力フォーム <small> - {{$r->entered_on}}</small></h4>
        </div>
        <div class="modal-body" style="color: #444;"  id="actual-time-{{ $r->id }}">
          <div class="text-center mb">
            <div class="btn-group btn-group-sm mb" role="group">
              <!-- setRestReason -> roster.app.calendar.index -->
              <button type="button" class="btn btn-default" onclick="return setRestReason(1,  99, {{ $r->id }})"><b class="text-success">休暇です</b></button>
              <button type="button" class="btn btn-default" onclick="return setRestReason(11, 99, {{ $r->id }})"><b class="text-success">代休です</b></button>
              <button type="button" class="btn btn-default" onclick="return setRestReason(12, 0,  {{ $r->id }})"><b class="text-warning">遅刻です</b></button>
              <button type="button" class="btn btn-default" onclick="return setRestReason(13, 0,  {{ $r->id }})"><b class="text-warning">早退です</b></button>
              <button type="button" class="btn btn-default" onclick="return setRestReason(15, 0,  {{ $r->id }})"><b class="text-warning">出張です</b></button>
              <button type="button" class="btn btn-default" onclick="return setRestReason(0,  9,  {{ $r->id }})"><b class="text-danger">休出です</b></button>
            </div>
          </div>
          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-10 col-md-offset-1">
              <label>実勤務形態</label>
            </div>
            <div class="col-md-10 col-md-offset-1">
              <div class="form-group" style="display:block;">
                <select class="form-control" name="actual_work_type_id" style="width: 100%;" id="actual-work-type-id-{{ $r->id }}" data-default="{{ $r->plan_work_type_id }}" required>
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
                <select class="form-control" name="actual_rest_reason_id" style="width: 100%;" id="actual-rest-reason-id-{{ $r->id }}" data-default="{{ $r->plan_rest_reason_id or 0 }}">
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
                <select class="form-control input-sm" name="actual_start_hour" data-toggle="clear" required>
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
                <select class="form-control input-sm" name="actual_start_time" data-toggle="clear" required>
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
                <select class="form-control input-sm" name="actual_end_hour" data-toggle="clear" required>
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
                <select class="form-control input-sm" name="actual_end_time" data-toggle="clear" required>
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
                id="actual_overtime_reason_{{ $r->id }}"
                @if(!empty($r->actual_overtime_reason))   value="{{$r->actual_overtime_reason}}"
                @elseif(!empty($r->plan_overtime_reason)) value="{{$r->plan_overtime_reason}}" @endif
                class="form-control"
                style="width: 100%;"
                placeholder="残業した場合、理由を記入してください"
                maxlength="20"
                >
                <p class="text-right">
                  <small>
                    <span id="actual_count_{{ $r->id }}">
                      @if(!empty($r->actual_overtime_reason)) {{ mb_strlen($r->actual_overtime_reason) }}
                      @elseif(!empty($r->plan_overtime_reason)) {{ mb_strlen($r->plan_overtime_reason) }}
                      @else 0
                      @endif
                    </span> / 20
                  </small>
                </p>
                @if($r->is_actual_reject && !empty($r->reject_reason)) <small class="helpBlock text-danger">{{$r->reject_reason}}</small> @endif
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">

          <div class="col-md-10 col-md-offset-1 text-right">
            {{-- <button type="submit" class="btn btn-success pos-btn" onclick="return checkHolidayWork('actual-time-{{ $r->id }}');">更新する</button> --}}
            <button type="submit" class="btn btn-success pos-btn" onclick="return checkActual({{ $r->id }});">更新する</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">
  $("#actual_overtime_reason_{{ $r->id }}").keyup(function () {
    let str = $(this).val()
    $("#actual_count_{{ $r->id }}").html(str.length)
  })
</script>
@endif
