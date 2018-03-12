{{--
  -  inherit: $u as users
  -  inherit  $i as $key
  --}}

  <form method="POST" action="{{ route('app::roster::accept::calendar_accept', ['ym'=>$ym,'div'=>$div,'user_id'=>$user_id]) }}">


    <div class="text-right" data-spy="affix" data-offset-top="120" style="z-index: 1;  top: 120px; right: 15px;">
        <div class="btn-group">
            <a href="{{route('app::roster::accept::index')}}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-backward" aria-hidden="true"></span> 戻る</a>
            <a href="{{ route_with_query('app::roster::accept::calendar', ['ym'=>$ym, 'div'=>$div, 'user'=>$user_id], ['status'=>'all']) }}"  class="btn btn-success btn-sm">全件表示</a>
            <a href="{{ route_with_query('app::roster::accept::calendar', ['ym'=>$ym, 'div'=>$div, 'user'=>$user_id], ['status'=>'part']) }}" class="btn btn-warning btn-sm">未承認のみ</a>
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

            @foreach($rows as $i=>$day)
            <?php $roster = $day['data']; ?>
            @if(!empty($status) && $status !== 'all' && empty($roster))
            @else
            <tr @if($i % 2 === 0) style="background: #fff;" @endif>
                <th class="bg-primary" rowspan="5">
                    <p
                    @if($day['week'] === 0 || $day['holiday'] === 1) class="text-danger-light"
                    @elseif($day['week'] === 6) class="text-info-light"
                    @endif
                    @if($day['holiday'] === 1) data-toggle="tooltip" data-placement="right" title="{{$day['holiday_name']}}" @endif
                    >{{ $day['date'] }} ({{ $day['week_name'] }})</p>
                </th>
                <th class="bg-primary">状態</th>
                <!-- 予定ステータス -->
                <td class="text-left">
                    @if(empty($roster))              <label class="label label-default">データなし</label>
                    @elseif(!$roster->is_plan_entry) <label class="label label-default">未入力</label>
                    @elseif($roster->is_plan_accept) <label class="label label-success">承認済み</label>
                    @elseif($roster->is_plan_reject) <label class="label label-danger">却下</label>
                    @else                            <label class="label label-warning">承認待ち</label>
                    @endif
                </td>
                <!-- 実績ステータス -->
                <td class="text-left">
                    @if(empty($roster))                <label class="label label-default">データなし</label>
                    @elseif(!$roster->is_actual_entry) <label class="label label-default">未入力</label>
                    @elseif($roster->is_actual_accept) <label class="label label-success">承認済み</label>
                    @elseif($roster->is_actual_reject) <label class="label label-danger">却下</label>
                    @else                              <label class="label label-warning">承認待ち</label>
                    @endif
                </td>
                {{--             <td><pre>{{ var_dump($roster) }}</pre></td> --}}
            </tr>
            <tr @if($i % 2 === 0) style="background: #fff;" @endif>
                <!-- 勤務形態 -->
                <th class="bg-primary" style="border-top: none;">勤務形態</th>
                @if(!empty($roster))
                <!-- 予定 -->
                <td class="text-left" style="border-top: none;">
                    @if(empty($roster->is_plan_entry) || empty($roster->plan_work_type_id))
                    @else
                    <p>
                        {{ $types[$roster->plan_work_type_id]['name'] }} 　 
                        {{ $types[$roster->plan_work_type_id]['time'] }}
                    </p>
                    @endif
                </td>
                <!-- 実績 -->
                <td class="text-left" style="border-top: none;">
                    @if(empty($roster->is_actual_entry) || empty($roster->actual_work_type_id))
                    @else
                    <p>
                        {{ $types[$roster->actual_work_type_id]['name'] }} 　 
                        {{ $types[$roster->actual_work_type_id]['time'] }}
                    </p>
                    @endif
                </td>
                @else <td style="border-top: none;"></td><td style="border-top: none;"></td>
                @endif
            </tr>


            <tr @if($i % 2 === 0) style="background: #fff;" @endif>
                <!-- 就業時間 -->
                <th class="bg-primary" style="border-top: none;">就業時間</th>
                @if(!empty($roster))
                <!-- 予定 -->
                <td class="text-left" style="border-top: none;">
                    @if(empty($roster->is_plan_entry) || empty($roster->plan_overtime_start_time) || empty($roster->plan_overtime_end_time))
                    @else
                    <p>
                        {{ date('G:i', strtotime($roster->plan_overtime_start_time)) }} ～ 
                        {{ date('G:i', strtotime($roster->plan_overtime_end_time)) }}
                    </p>
                    <p>{{ $roster->plan_overtime_reason }}</p>
                    @endif
                </td>
                <!-- 実績 -->
                <td class="text-left" style="border-top: none;">
                    @if(empty($roster->is_actual_entry) || empty($roster->actual_overtime_start_time) || empty($roster->actual_overtime_end_time))
                    @else
                    <p>
                        {{ date('G:i', strtotime($roster->actual_overtime_start_time)) }} ～ 
                        {{ date('G:i', strtotime($roster->actual_overtime_end_time)) }}
                    </p>
                    <p>{{ $roster->actual_overtime_reason }}</p>
                    @endif
                </td>
                @else <td style="border-top: none;"></td><td style="border-top: none;"></td>
                @endif {{-- <td class="text-left"><pre>{{ var_dump($roster) }}</pre></td>  --}}
            </tr>

            <tr @if($i % 2 === 0) style="background: #fff;" @endif>
                <!-- 休暇理由 -->
                <th class="bg-primary" style="border-top: none;">休暇理由</th>
                @if(!empty($roster))
                <!-- 予定 -->
                <td class="text-left" style="border-top: none;">
                    @if(empty($roster->is_plan_entry) || empty($roster->plan_rest_reason_id))
                    @else
                    <p>{{ $rests[$roster->plan_rest_reason_id] }}</p>
                    @endif
                </td>
                <!-- 実績 -->
                <td class="text-left" style="border-top: none;">
                    @if(empty($roster->is_actual_entry) || empty($roster->actual_rest_reason_id))
                    @else
                    <p>{{ $rests[$roster->actual_rest_reason_id] }}</p>
                    @endif
                </td>
                @else <td style="border-top: none;"></td><td style="border-top: none;"></td>
                @endif
            </tr>

            <tr @if($i % 2 === 0) style="background: #fff;" @endif>
                <!-- フォーム -->
                <th class="bg-primary" style="border-top: none;"></th>
                @if(!empty($roster))
                <!-- 予定 -->
                <td class="text-left" style="border-top: none;">
                    @if($roster->is_plan_entry)
                    @if(!$roster->is_plan_accept)
                    <span class="btn-group" data-toggle="buttons">
                        <!--  -->
                        <input type="hidden" name="id[{{$roster->key_id}}]" value="{{$roster->key_id}}">
                        <label class="btn btn-sm btn-default activate"   data-color="btn-success" data-target="#actual-form-{{$roster->key_id}}">
                            <input type="checkbox" name="plan[{{$roster->key_id}}]" value="1" @if($roster->is_plan_accept) checked @endif>承認
                        </label>
                        <label class="btn btn-sm btn-default inactivate" data-color="btn-danger"  data-target="#actual-form-{{$roster->key_id}}">
                            <input type="checkbox" name="plan[{{$roster->key_id}}]" value="0" @if($roster->is_plan_reject) checked @endif>却下
                        </label>
                        <!--  -->
                        <span></span>
                        <input class="form-control input-sm" type="text" name="plan_reject[{{$roster->key_id}}]" placeholder="却下理由（任意）" style="display: inline-block; width: 200px; padding: 5px; height: 30px; border-left: none;">
                    </span>
                    @endif
                    @endif
                </td>
                <!-- 実績 -->
                <td class="text-left" style="border-top: none;">
                    @if($roster->is_actual_entry)
                    @if(!$roster->is_actual_accept)
                    <span class="btn-group" data-toggle="buttons">
                        <!--  -->
                        <input type="hidden" name="id[{{$roster->key_id}}]" value="{{$roster->key_id}}">
                        <label class="btn btn-sm btn-default activate"   data-color="btn-success" data-target="#actual-form-{{$roster->key_id}}">
                            <input type="checkbox" name="actual[{{$roster->key_id}}]" value="1" @if($roster->is_actual_accept) checked @endif>承認
                        </label>
                        <label class="btn btn-sm btn-default inactivate" data-color="btn-danger"  data-target="#actual-form-{{$roster->key_id}}">
                            <input type="checkbox" name="actual[{{$roster->key_id}}]" value="0" @if($roster->is_actual_reject) checked @endif>却下
                        </label>
                        <!--  -->
                        <span></span>
                        <input class="form-control input-sm" type="text" name="actual_reject[{{$roster->key_id}}]" placeholder="却下理由（任意）" style="display: inline-block; width: 200px; padding: 5px; height: 30px; border-left: none;">
                    </span>
                    @endif
                    @endif
                </td>
                @else <td style="border-top: none;"></td><td style="border-top: none;"></td>
                @endif
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</form>