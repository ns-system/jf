<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<!DOCTYPE html>

@extends('layout')

@section('title', 'ユーザー権限')

@section('header')
@parent
@section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('partial.check_sidebar')

</div>
@endsection



@section('content')
<div class="col-md-10">
    @include('partial.alert')

    @if($divs->isEmpty())
    <div class="alert alert-warning" role="alert">
        <p><b>管轄部署が登録されていないようです。</b></p>
        <p><small>担当部署に部署の登録を行ってもらうよう連絡してください。</small></p>
    </div>
    @else
    <p class="margin-bottom"><b>担当部署</b></p>
    <ul class="list-group" style="width: 300px;">
        @foreach($divs as $d) <li class="list-group-item"><small>{{$d->division_name}}</small></li> @endforeach
    </ul>
    @endif

    @foreach($divs as $d)
    <div class="border-bottom">
        <h2>{{$d->division_name}}</h2>
    </div>
    @if($months->isEmpty()) <div class="alert alert-warning" role="alert">データが登録されていないようです。</div> @endif
    <div class="row">
        @foreach($months as $m)
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">{{date('Y年n月',strtotime($m->month_id . '01'))}}</div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-6">
                            <p class="border-bottom"><small><b>予定</small></b></p>
                            <canvas id="plan-{{$m->month_id}}"></canvas>
                        </div>
                        <div class="col-md-6">
                            <p class="border-bottom"><small><b>実績</small></b></p>
                            <canvas id="actual-{{$m->month_id}}" style="margin-left: -30px;"></canvas>
                        </div>
                    </div>

                    <div class="col-md-10 col-md-offset-1" style="padding-top: 20px;">
                        <table class="table table-hover table-small">
                            <thead>
                                <tr>
                                    <th width="20%"></th>
                                    <th width="20%"><span class="label label-warning">未承認</span></th>
                                    <th width="20%"><span class="label label-danger" >却下</span></th>
                                    <th width="20%"><span class="label label-success">承認済み</span></th>
                                    <th width="20%"><span class="label label-info"   >合計</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>予定</th>
                                    <td><span class="text-warning"><strong>{{$rows['plan_not_accepts'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                                    <td><span class="text-danger" ><strong>{{$rows['plan_rejects'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                                    <td><span class="text-success"><strong>{{$rows['plan_accepts'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                                    <td><span class="text-info"   ><strong>{{$rows['plan_entry'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                                </tr>
                                <tr>
                                    <th>実績</th>
                                    <td><span class="text-warning"><strong>{{$rows['actual_not_accepts'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                                    <td><span class="text-danger" ><strong>{{$rows['actual_rejects'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                                    <td><span class="text-success"><strong>{{$rows['actual_accepts'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                                    <td><span class="text-info"   ><strong>{{$rows['actual_entry'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <a href="{{route('app::roster::accept::calendar', ['ym'=>$m->month_id, 'div'=>$d->division_id])}}" class="btn btn-success btn-block">
                                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> リストから承認
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</div>
@endsection

@section('footer')
@parent
<script>
    @foreach($months as $m)
    var plan_{{$m->month_id}} = [
    {
        value: {{$rows['plan_not_accepts'][$d->division_id][$m->month_id] or 0}},
        color: "#f39c12",
        label: "未承認",
    },
    {
        value : {{$rows['plan_rejects'][$d->division_id][$m->month_id] or 0}},
        color : "#e74c3c",
        label: "却下",
    },
    {
        value : {{$rows['plan_accepts'][$d->division_id][$m->month_id] or 0}},
        color : "#18bc9c",
        label: "承認済み",
    },
    ];
    var actual_{{$m->month_id}} = [
    {
        value: {{$rows['actual_not_accepts'][$d->division_id][$m->month_id] or 0}},
        color: "#f39c12",
        label: "未承認",
    },
    {
        value : {{$rows['actual_rejects'][$d->division_id][$m->month_id] or 0}},
        color : "#e74c3c",
        label: "却下",
    },
    {
        value : {{$rows['actual_accepts'][$d->division_id][$m->month_id] or 0}},
        color : "#18bc9c",
        label: "承認済み",
    },
    ];
    @endforeach
    window.onload = function(){
        @foreach($months as $m)
        var ctx = document.getElementById("plan-{{$m->month_id}}").getContext("2d");
        window.myDoughnut = new Chart(ctx).Doughnut(plan_{{$m->month_id}}, {
            responsive : true
        });
        var ctx = document.getElementById("actual-{{$m->month_id}}").getContext("2d");
        window.myDoughnut = new Chart(ctx).Doughnut(actual_{{$m->month_id}}, {
            responsive : true
        });
        @endforeach
    }
    // var myDoughnut = new Chart(document.getElementById("sample").getContext("2d")).Doughnut(doughnutData);
</script>
@endsection