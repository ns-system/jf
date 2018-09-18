@extends('layout')

@section('title', 'CSV出力')

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<div class="col-md-10">
  @include('partial.alert')
  <div class="border-bottom"><h2>勤怠管理システム CSV出力 <small> - 月選択</small></h2></div>

  @if(!$months->isEmpty())
  @foreach($months as $m)
  <div class="col-md-3" style="margin-bottom: 10px;">
    @if($m->month_id == $current)
    <a href="{{route('admin::roster::csv::show', ['month'=>$m->month_id])}}" class="btn btn-warning btn-lg btn-block">
      {{date('Y年n月', strtotime($m->month_id . '01'))}}
      <span class="badge">{{$m->cnt}}件</span>
    </a>
    @else
    <a href="{{route('admin::roster::csv::show', ['month'=>$m->month_id])}}" class="btn btn-primary btn-lg btn-block" >
      {{date('Y年n月', strtotime($m->month_id . '01'))}}
      <span class="badge">{{$m->cnt}}件</span>
    </a>
    @endif
  </div>
  @endforeach
  @else
  <div class="alert alert-warning" role="alert">データが見つかりませんでした。</div>
  @endif
  {{-- chart --}}
  <div class="chart col-xs-12">
    <p class="text-right">
      <button onclick="size()" class="btn-primary btn">チャートのサイズを変える</button>
    </p>
    @foreach($chart as $key => $data)

    <div class="panel-group" id="collapse_{{ $key }}">
      <div class="panel panel-default" style="margin-bottom: 10px;">
        <div class="panel-heading">
          <h3 class="panel-title">
            <a data-toggle="collapse" data-parent="#collapse_{{ $key }}" href="#collapse_{{ $key }}_item">{{ date('Y年n月', strtotime($key.'01')) }}</a>
          </h3>
        </div>
        <div id="collapse_{{ $key }}_item" class="panel-collapse collapse in">
          <div class="panel-body">
            {{-- summary --}}
            <div class="row">
              @include('roster.admin.csv.partial.summary',               ['key'=>$key, 'colors'=>$colors, 'chart'=>$data['summary'][0]])
            </div>
            <div class="row">
              @include('roster.admin.csv.partial.summary_with_division', ['key'=>$key, 'colors'=>$colors, 'chart'=>$data['summary_with_division']])
            </div>
            <div class="row">
              @include('roster.admin.csv.partial.summary_with_date',     ['key'=>$key, 'colors'=>$colors, 'chart'=>$data['summary_with_date']])
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach

  </div>
</div>
@endsection

@section('footer')
<script type="text/javascript">
  let isSmall = true
  function size() {
    var add    = (isSmall) ? 'col-xs-12' : 'col-xs-6';
    var remove = (isSmall) ? 'col-xs-6'  : 'col-xs-12';
    isSmall    = !isSmall

    $('.colsize').removeClass(remove).addClass(add);
    $('.colsize').removeClass(remove).addClass(add);
  }
</script>
@parent
@endsection