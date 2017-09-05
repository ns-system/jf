<table class="table table-hover table-small va-middle">
    <thead>
        <tr>
            <th></th>
        </tr>
    </thead>
    <tbody>
@foreach($rosters as $r)
    <tr>
        <td>{{date('Y年n月j日', strtotime($r->entered_on))}}</td>
        <td>
            <p>{{$r->division_name}}</p>
            <p>{{$r->name}}さん</p>
        </td>
        {{-- 予定 --}}
        <td>
            @if($r->is_plan_entry)
                @if($r->is_plan_accept)
                    <p>
                        <span class="label label-success">承認済み</span> （{{\App\User::where('id','=',$r->plan_accept_user_id)->first()->name}}さん）
                    </p>
                    <p>{{date('n月j日 G:i', strtotime($r->plan_accepted_at))}}</p>
                @elseif($r->is_plan_reject)
                    <p>
                        <span class="label label-danger">却下</span> （{{\App\User::where('id','=',$r->plan_reject_user_id)->first()->name}}さん）
                    </p>
                    <p>{{date('n月j日 G:i', strtotime($r->plan_reject_at))}}</p>
                @else
                    <p>
                        <span class="label label-warning">未承認</span>
                    </p>
                @endif
            @else
                    <p>
                        <span class="label label-default">未入力</span>
                    </p>
            @endif
        </td>
        <td>
            <p>@if(!empty($r->plan_work_type_id)) {{$types[$r->plan_work_type_id]['name']}} {{$types[$r->plan_work_type_id]['time']}} @endif</p>
            <p>
                @if(!empty($r->plan_overtime_start_time) && !empty($r->plan_overtime_end_time)) {{date('G:i', strtotime($r->plan_overtime_start_time))}} ～ {{date('G:i', strtotime($r->plan_overtime_start_time))}} @endif
            </p>
            <p>{{$r->plan_overtime_reason}}</p>
        </td>
        {{-- 実績 --}}
    </tr>
@endforeach
    </tbody>
</table>

