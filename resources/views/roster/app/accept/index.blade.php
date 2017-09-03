@extends('layout')

@section('title', 'ユーザー権限')

@section('header')
@parent
@section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('app.sidebar.sidebar')

</div>
@endsection



@section('content')
<div class="col-md-10">
@include('partial.alert')

@foreach($divs as $d)
    <div class="border-bottom"><h2>{{$d->division_name}}</h2></div>
    @foreach($months as $m)
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">{{date('Y年n月',strtotime($m->month_id . '01'))}}</div>
            <div class="panel-body">

            <div class="col-md-10 col-md-offset-1" style="padding-top: 20px;">
                <table class="table table-hover table-small">
                    <thead>
                        <tr>
                            <th class="info" width="20%"></th>
                            <th class="info" width="20%">未承認</th>
                            <th class="info" width="20%">却下</th>
                            <th class="info" width="20%">承認済み</th>
                            <th class="info" width="20%">合計</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="info">予定</td>
                            <td><span class="text-warning"><strong>{{$rows['plan_not_accepts'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                            <td><span class="text-danger" ><strong>{{$rows['plan_rejects'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                            <td><span class="text-success"><strong>{{$rows['plan_accepts'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                            <td><span class="text-info"   ><strong>{{$rows['plan_entry'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                        </tr>
                        <tr>
                            <td class="info">実績</td>
                            <td><span class="text-warning"><strong>{{$rows['actual_not_accepts'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                            <td><span class="text-danger" ><strong>{{$rows['actual_rejects'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                            <td><span class="text-success"><strong>{{$rows['actual_accepts'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                            <td><span class="text-info"   ><strong>{{$rows['actual_entry'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                        </tr>
                        <tr>
                            <td colspan="5"><a href="{{route('app::roster::accept::list', ['ym'=>$m->month_id, 'div'=>$d->division_id])}}" class="btn btn-success btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> 詳細を見る</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
    @endforeach
@endforeach

{{-- <div class="border-bottom"><h2>さん</small></h2></div>

@foreach($rows as $r)
<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">{{$r->division_name}} - {{date('Y年n月',strtotime($r->month_id . '01'))}}</div>
        <div class="panel-body">
            <div class="col-md-6">
                @if($r->is_plan_accept)
                @endif
            </div>
            <div class="col-md-6">
                @if($r->is_plan_reject)
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
</div> --}}
@endsection

@section('footer')
@parent
@endsection