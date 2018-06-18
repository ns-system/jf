<?php $jp_week = ['日','月','火','水','木','金','土']; ?>

@if(empty($unchecked) || $unchecked->isEmpty())
<p>未承認データは見つかりませんでした。</p>
@else
<form method="POST" action="{{ route('app::roster::accept::calendar_accept', ['ym'=>$ym,'div'=>$div,'user_id'=>$user_id]) }}">
  <input type="hidden" name="is_bulk" value="true">
  <div class="text-right" data-spy="affix" data-offset-top="120" style="z-index: 1;  top: 120px; right: 15px;">
    <div class="btn-group">
      <button type="button" class="btn btn-primary btn-sm" onclick="checkAll('.plan-accept')"   data-toggle="tooltip" title="予定を承認状態にします。この段階では更新されないので、チェック後は変更を全て更新をクリックしてください。">予定をチェック</button>
      <button type="button" class="btn btn-primary btn-sm" onclick="checkAll('.actual-accept')" data-toggle="tooltip" title="実績を承認状態にします。この段階では更新されないので、チェック後は変更を全て更新をクリックしてください。">実績をチェック</button>
      <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('チェックしたデータが一括で更新されますがよろしいですか？');"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> 変更を全て更新</button>
    </div>
  </div>
  <div class="margin-bottom"></div>
  {{ csrf_field() }}
  <table class="table table-small none-border va-middle">
    <thead>
      <tr>
        <th class="bg-primary" width="10%">日付</th>
        <th class="bg-primary" width="10%"></th>
        <th class="bg-primary" width="35%">予定</th>
        <th class="bg-primary" width="35%">実績</th>
      </tr>
    </thead>
    <tbody>
      @foreach($unchecked as $un)
       <?php $week = date('w', strtotime($un->entered_on)); ?>
      <tr @if($week == 0) class="bg-danger" @elseif($week == 6) class="bg-info" @endif>
        <th class="bg-primary" rowspan="5">
          <p>{{ $un->laraveluser->last_name }}さん</p>
          <p @if($week == 0) class="text-danger-light" @elseif($week == 6) class="text-info-light" @endif>{{ $un->entered_on }}<br>（{{ $jp_week[$week] }}）</p>
        </th>
        <th class="bg-primary">状態</th>
        <!-- 予定ステータス -->
        <td class="text-left">
          @if(empty($un))              <label class="label label-default">データなし</label>
          @elseif(!$un->is_plan_entry) <label class="label label-default">未入力</label>
          @elseif($un->is_plan_accept) <label class="label label-success">承認済み</label>
          @elseif($un->is_plan_reject) <label class="label label-danger">却下</label>
          @else                            <label class="label label-warning">承認待ち</label>
          @endif
        </td>
        <!-- 実績ステータス -->
        <td class="text-left">
          @if(empty($un))                <label class="label label-default">データなし</label>
          @elseif(!$un->is_actual_entry) <label class="label label-default">未入力</label>
          @elseif($un->is_actual_accept) <label class="label label-success">承認済み</label>
          @elseif($un->is_actual_reject) <label class="label label-danger">却下</label>
          @else                              <label class="label label-warning">承認待ち</label>
          @endif
        </td>
        {{--             <td><pre>{{ var_dump($un) }}</pre></td> --}}
      </tr>
      <tr>
        <!-- 勤務形態 -->
        <th class="bg-primary" style="border-top: none;">勤務形態</th>
        @if(!empty($un))
        <!-- 予定 -->
        <td class="text-left" style="border-top: none;">
          @if(empty($un->is_plan_entry) || empty($un->plan_work_type_id))
          @else
          <p>
            {{ $types[$un->plan_work_type_id]['name'] }} 　 
            {{ $types[$un->plan_work_type_id]['time'] }}
          </p>
          @endif
        </td>
        <!-- 実績 -->
        <td class="text-left" style="border-top: none;">
          @if(empty($un->is_actual_entry) || empty($un->actual_work_type_id))
          @else
          <p>
            {{ $types[$un->actual_work_type_id]['name'] }} 　 
            {{ $types[$un->actual_work_type_id]['time'] }}
          </p>
          @endif
        </td>
        @else <td style="border-top: none;"></td><td style="border-top: none;"></td>
        @endif
      </tr>


      <tr @if($i % 2 === 0) style="background: #fff;" @endif>
        <!-- 就業時間 -->
        <th class="bg-primary" style="border-top: none;">就業時間</th>
        @if(!empty($un))
        <!-- 予定 -->
        <td class="text-left" style="border-top: none;">
          @if(empty($un->is_plan_entry) || empty($un->plan_overtime_start_time) || empty($un->plan_overtime_end_time))
          @else
          <p>
            {{ date('G:i', strtotime($un->plan_overtime_start_time)) }} ～ 
            {{ date('G:i', strtotime($un->plan_overtime_end_time)) }}
          </p>
          <p>{{ $un->plan_overtime_reason }}</p>
          @endif
        </td>
        <!-- 実績 -->
        <td class="text-left" style="border-top: none;">
          @if(empty($un->is_actual_entry) || empty($un->actual_overtime_start_time) || empty($un->actual_overtime_end_time))
          @else
          <p>
            {{ date('G:i', strtotime($un->actual_overtime_start_time)) }} ～ 
            {{ date('G:i', strtotime($un->actual_overtime_end_time)) }}
          </p>
          <p>{{ $un->actual_overtime_reason }}</p>
          @endif
        </td>
        @else <td style="border-top: none;"></td><td style="border-top: none;"></td>
        @endif {{-- <td class="text-left"><pre>{{ var_dump($un) }}</pre></td>  --}}
      </tr>

      <tr @if($i % 2 === 0) style="background: #fff;" @endif>
        <!-- 休暇理由 -->
        <th class="bg-primary" style="border-top: none;">休暇理由</th>
        @if(!empty($un))
        <!-- 予定 -->
        <td class="text-left" style="border-top: none;">
          @if(empty($un->is_plan_entry) || empty($un->plan_rest_reason_id))
          @else
          <p>{{ $rests[$un->plan_rest_reason_id] }}</p>
          @endif
        </td>
        <!-- 実績 -->
        <td class="text-left" style="border-top: none;">
          @if(empty($un->is_actual_entry) || empty($un->actual_rest_reason_id))
          @else
          <p>{{ $rests[$un->actual_rest_reason_id] }}</p>
          @endif
        </td>
        @else <td style="border-top: none;"></td><td style="border-top: none;"></td>
        @endif
      </tr>

      <tr @if($i % 2 === 0) style="background: #fff;" @endif>
        <!-- フォーム -->
        <th class="bg-primary" style="border-top: none;"></th>
        @if(!empty($un))
        <!-- 予定 -->
        <td class="text-left" style="border-top: none;">
          @if($un->is_plan_entry)
          @if(!$un->is_plan_accept)
          <span class="btn-group" data-toggle="buttons">
            <!--  -->
            <input type="hidden" name="id[{{$un->id}}]" value="{{$un->id}}">
            <label class="btn btn-sm btn-default activate"   data-color="btn-success" data-target="#actual-form-{{$un->id}}">
              <input type="checkbox" name="plan[{{$un->id}}]" value="1" class="plan-accept" @if($un->is_plan_accept) checked @endif>承認
            </label>
            <label class="btn btn-sm btn-default inactivate" data-color="btn-danger"  data-target="#actual-form-{{$un->id}}">
              <input type="checkbox" name="plan[{{$un->id}}]" value="0" @if($un->is_plan_reject) checked @endif>却下
            </label>
            <!--  -->
            <span></span>
            <input class="form-control input-sm" type="text" name="plan_reject[{{$un->id}}]" placeholder="却下理由（任意）" style="display: inline-block; width: 200px; padding: 5px; height: 30px; border-left: none;">
          </span>
          @endif
          @endif
        </td>
        <!-- 実績 -->
        <td class="text-left" style="border-top: none;">
          @if($un->is_actual_entry)
          @if(!$un->is_actual_accept)
          <span class="btn-group" data-toggle="buttons">
            <!--  -->
            <input type="hidden" name="id[{{$un->id}}]" value="{{$un->id}}">
            <label class="btn btn-sm btn-default activate"   data-color="btn-success" data-target="#actual-form-{{$un->id}}">
              <input type="checkbox" name="actual[{{$un->id}}]" value="1" class="actual-accept" @if($un->is_actual_accept) checked @endif>承認
            </label>
            <label class="btn btn-sm btn-default inactivate" data-color="btn-danger"  data-target="#actual-form-{{$un->id}}">
              <input type="checkbox" name="actual[{{$un->id}}]" value="0" @if($un->is_actual_reject) checked @endif>却下
            </label>
            <!--  -->
            <span></span>
            <input class="form-control input-sm" type="text" name="actual_reject[{{$un->id}}]" placeholder="却下理由（任意）" style="display: inline-block; width: 200px; padding: 5px; height: 30px; border-left: none;">
          </span>
          @endif
          @endif
        </td>
        @else <td style="border-top: none;"></td><td style="border-top: none;"></td>
        @endif
      </tr>
      @endforeach
    </tbody>
  </table>
</form>
@endif

<script type="text/javascript">
  function checkAll (selector) {
    $(selector).each(function (i, e) {
      console.log('hoge')
      $(e).prop('checked', true).attr('checked', 'checked')
      $(e).parent().removeClass('btn-default').addClass('btn-success')
    })
  }
</script>