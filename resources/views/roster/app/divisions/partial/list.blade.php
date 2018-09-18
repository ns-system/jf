<!-- タブ・メニュー -->
@if(empty($users))
<div class="row">
  <div style="margin-bottom: 50px;"></div>
  <div class="alert alert-warning" role="alert">データが見つかりませんでした。</div>
</div>
@else
<ul class="nav nav-tabs" style="margin-bottom: 10px;">
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">ユーザー選択 <span class="caret"></span></a>
    <ul class="dropdown-menu">
      @foreach($users as $u)
      <li>
        <a href="#user_{{ $u->id }}" data-toggle="tab">
          @include('partial.avatar', ['avatar' => $u->user_icon, 'size' => '48px',])
          {{$u->last_name}} {{ $u->first_name }} <small>さん</small>
        </a>
      </li>
      @endforeach
    </ul>
  </li>
</ul>

<div class="tab-content">

  @foreach($users as $i => $u)
  <div @if($i == 0) class="tab-pane active" @else class="tab-pane" @endif id="user_{{$u->id}}">
    <table class="table table-hover table-small">
      <thead>
        <tr class="bg-primary">
          <th width="30%">
            @include('partial.avatar', ['avatar' => $u->user_icon, 'size' => '40px',])
            {{$u->last_name}} {{$u->first_name}} <small>さん</small>
          </th>
          <th width="35%">予定</th>
          <th width="35%">実績</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $day)
        <tr>
          <?php $r = (!empty($day['data'][$u->user_id])) ? $day['data'][$u->user_id] : null; ?>
          <th class="bg-primary va-middle">
            <p @if($day['holiday']) class="text-danger-light" data-toggle="tooltip" data-placement="right" title="{{$day['holiday_name']}}" @elseif($day['week'] == 0) class="text-danger-light" @elseif($day['week'] == 6) class="text-info-light" @endif>
              <strong>{{date('n月j日', strtotime($day['date']))}} （{{$day['week_name']}}）</strong>
            </p>
          </th>

          <!-- 予定 -->
          <td @if($day['holiday']) class="text-left danger" @elseif($day['week'] == 0) class="text-left danger" @elseif($day['week'] == 6) class="text-left info" @else class="text-left" @endif>
            @if(!empty($r) && $r->is_plan_entry)
            <p>
              @if($r->is_plan_accept)     <span class="label label-success">予定</span>
              @elseif($r->is_plan_reject) <span class="label label-danger">予定</span>
              @else                       <span class="label label-warning">予定</span> @endif
            </p>
            @if(!empty($r->plan_rest_reason_id)) <p>{{$rests[$r->plan_rest_reason_id]}}</p> @endif
            @if(!empty($r->plan_overtime_start_time) && !empty($r->plan_overtime_end_time))     <p>{{date('G:i', strtotime($r->plan_overtime_start_time))}} ～ {{date('G:i', strtotime($r->plan_overtime_end_time))}}</p> @endif
            @if(!empty($r->plan_overtime_reason)) <p>{{$r->plan_overtime_reason}}</p> @endif
            @elseif(!empty($r->entered_on))
            <p><span class="label label-default">予定</span></p>
            @if(!empty($r->plan_rest_reason_id)) <p>{{$rests[$r->plan_rest_reason_id]}}</p> @endif
            @if(!empty($r->plan_work_type_id)) <p>{{$types[$r->plan_work_type_id]->work_type_name}}</p> @endif
            @endif
          </td>

          <!-- 実績 -->
          <td @if($day['holiday']) class="text-left danger" @elseif($day['week'] == 0) class="text-left danger" @elseif($day['week'] == 6) class="text-left info" @else class="text-left" @endif>
            @if(!empty($r) && $r->is_actual_entry)
            <p>
              @if($r->is_actual_accept)     <span class="label label-success">実績</span>
              @elseif($r->is_actual_reject) <span class="label label-danger">実績</span>
              @else                         <span class="label label-warning">実績</span> @endif
            </p>
            @if(!empty($r->actual_rest_reason_id))        <p>{{$rests[$r->actual_rest_reason_id]}}</p> @endif
            @if(!empty($r->actual_overtime_start_time) &&
            !empty($r->actual_overtime_end_time))     <p>{{date('G:i', strtotime($r->actual_overtime_start_time))}} ～ {{date('G:i', strtotime($r->actual_overtime_end_time))}}</p> @endif
            @if(!empty($r->actual_overtime_reason))       <p>{{$r->actual_overtime_reason}}</p> @endif
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endforeach
</div>
@endif
