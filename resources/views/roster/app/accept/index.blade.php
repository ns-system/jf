<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
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


  @if(!$divs->isEmpty())
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
              <canvas id="plan-{{ $d->division_id }}-{{$m->month_id}}" style="height: 300px;"></canvas>
            </div>
            <div class="col-md-6">
              <p class="border-bottom"><small><b>実績</small></b></p>
              <canvas id="actual-{{ $d->division_id }}-{{$m->month_id}}" style="margin-left: -30px;"></canvas>
            </div>
          </div>

          <div class="col-md-12" style="padding-top: 20px;">
            <table class="table table-hover table-small">
              <thead>
                <tr>
                  <th width="20%"></th>
                  <th width="20%"><span class="label label-warning">未承認</span></th>
                  <th width="20%"><span class="label label-danger" >却下</span></th>
                  <th width="20%"><span class="label label-success">承認済</span></th>
                  <th width="20%"><span class="label label-info"   >合計</span></th>
                  <th width="20%"><span class="label label-default">未入力</span></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th>予定</th>
                  <td><span class="text-warning"><strong>{{ $rows[$d->division_id][$m->month_id]['予定未承認'] or 0 }}件</strong></span></td>
                  <td><span class="text-danger" ><strong>{{ $rows[$d->division_id][$m->month_id]['予定却下'] or 0   }}件</strong></span></td>
                  <td><span class="text-success"><strong>{{ $rows[$d->division_id][$m->month_id]['予定承認済'] or 0 }}件</strong></span></td>
                  <td><span class="text-info"   ><strong>{{ $rows[$d->division_id][$m->month_id]['予定合計'] or 0   }}件</strong></span></td>
                  <td><span class="text-muted"  ><strong>{{ $rows[$d->division_id][$m->month_id]['予定未入力'] or 0 }}件</strong></span></td>
{{--                   <td><span class="text-warning"><strong>{{$rows['plan_not_accepts'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                  <td><span class="text-danger" ><strong>{{$rows['plan_rejects'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                  <td><span class="text-success"><strong>{{$rows['plan_accepts'][$d->division_id][$m->month_id] or 0}}件</strong></span></td>
                  <td><span class="text-info"   ><strong>{{$rows['plan_entry'][$d->division_id][$m->month_id] or 0}}件</strong></span></td> --}}
                </tr>
                <tr>
                  <th>実績</th>
                  <td><span class="text-warning"><strong>{{ $rows[$d->division_id][$m->month_id]['実績未承認'] or 0 }}件</strong></span></td>
                  <td><span class="text-danger" ><strong>{{ $rows[$d->division_id][$m->month_id]['実績却下'] or 0   }}件</strong></span></td>
                  <td><span class="text-success"><strong>{{ $rows[$d->division_id][$m->month_id]['実績承認済'] or 0 }}件</strong></span></td>
                  <td><span class="text-info"   ><strong>{{ $rows[$d->division_id][$m->month_id]['実績合計'] or 0   }}件</strong></span></td>
                  <td><span class="text-muted"  ><strong>{{ $rows[$d->division_id][$m->month_id]['実績未入力'] or 0 }}件</strong></span></td>
                </tr>
                <tr>
                  <td colspan="6">
                    <a href="{{route('app::roster::accept::calendar', ['ym'=>$m->month_id, 'div'=>$d->division_id])}}" class="btn btn-success btn-block">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> 承認メニューへ
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
  @endif

</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">

  @if(!$divs->isEmpty())
  window.onload = function () {
    var bg  = ['rgba({{ $colors['承認'] }}, 0.4)','rgba({{ $colors['未承認'] }}, 0.4)','rgba({{ $colors['却下'] }}, 0.4)','rgba({{ $colors['未入力'] }}, 0.4)',];
    var bdr = ['rgb({{ $colors['承認'] }})','rgb({{ $colors['未承認'] }})','rgb({{ $colors['却下'] }})','rgb({{ $colors['未入力'] }})',];
    var height = 300;
    @foreach($divs as $d)
    @foreach($months as $m)
    var p_ctx_{{ $d->division_id }}_{{ $m->month_id }}      = document.getElementById("plan-{{ $d->division_id }}-{{$m->month_id}}").getContext("2d");
    p_ctx_{{ $d->division_id }}_{{ $m->month_id }}.canvas.height = height;
    var pieChart_{{ $d->division_id }}_{{ $m->month_id }} = new Chart(p_ctx_{{ $d->division_id }}_{{ $m->month_id }}, {
      type : 'pie',
      data : {
        labels : ['承認済','未承認','却下','未入力'],
        datasets : [{
          backgroundColor : bg,
          borderColor     : bdr,
          borderWidth     : 1,
          data : [
          {{ $rows[$d->division_id][$m->month_id]['予定承認済'] or 0 }},
          {{ $rows[$d->division_id][$m->month_id]['予定未承認'] or 0 }},
          {{ $rows[$d->division_id][$m->month_id]['予定却下'] or 0 }},
          {{ $rows[$d->division_id][$m->month_id]['予定未入力'] or 0 }},
          ],
        }],
      },
    });

    var a_ctx_{{ $d->division_id }}_{{ $m->month_id }}      = document.getElementById("actual-{{ $d->division_id }}-{{$m->month_id}}").getContext("2d");
    a_ctx_{{ $d->division_id }}_{{ $m->month_id }}.canvas.height = height;
    var pieChart_{{ $d->division_id }}_{{ $m->month_id }} = new Chart(a_ctx_{{ $d->division_id }}_{{ $m->month_id }}, {
      type : 'pie',
      data : {
        labels : ['承認済','未承認','却下','未入力'],
        datasets : [{
          backgroundColor : bg,
          borderColor     : bdr,
          borderWidth     : 1,
          data : [
          {{ $rows[$d->division_id][$m->month_id]['実績承認済'] or 0 }},
          {{ $rows[$d->division_id][$m->month_id]['実績未承認'] or 0 }},
          {{ $rows[$d->division_id][$m->month_id]['実績却下'] or 0 }},
          {{ $rows[$d->division_id][$m->month_id]['実績未入力'] or 0 }},
          ],
        }],
      },
    });
    @endforeach
    @endforeach

  }
  @endif

</script>
@endsection