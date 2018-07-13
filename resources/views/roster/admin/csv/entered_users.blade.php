@extends('layout')

@section('title', '勤怠管理')

@section('header')
@parent
<style>
.min-width    { min-width: 75px; }
.pa tr td,
.pa tr th { padding: 2px 0; }
.bdr td, .bdr th { border-bottom: 1px solid #ccc !important; }
</style>
@section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
<div class="col-md-2">
  @include('partial.check_sidebar')
</div>
@endsection



@section('content')
<div class="col-md-10">
  <h2 class="border-bottom">
    <a href="{{ route('admin::roster::csv::entered', ['ym'=>$last_month]) }}"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>
    {{ date('Y年n月', strtotime($ym.'01')) }}
    の入力状況
    <a href="{{ route('admin::roster::csv::entered', ['ym'=>$next_month]) }}"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a>
    <a href="#sampleModal" data-toggle="modal"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>
  </h2>
  @if(empty($rows) || $rows->isEmpty())
  <b class="text-danger">データが見つかりませんでした。</b>
  @else
  @include('partial.entered_users_chart', ['rows'=>$rows, 'height'=>1300])

  <div class="modal fade" id="sampleModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary-important">
          <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
          <h4 class="modal-title">タイトル</h4>
        </div>
        <div class="modal-body">
          <table class="table-small pa">
            <thead>
              <tr></tr>
            </thead>
            <tbody>
              @foreach($rows as $r)
              <tr>
                <th class="bg-primary" width="30%">{{ $r->last_name }} {{ $r->first_name }}さん</th>
                <td class="text-muted">{{ $r->予定未入力 or 0 }}</td>
                <td class="text-warning">{{ $r->予定未承認 or 0 }}</td>
                <td class="text-danger">{{ $r->予定却下 or 0 }}</td>
                <td class="text-success">{{ $r->予定承認済 or 0 }}</td>
                <td>{{ $r->予定未入力 + $r->予定未承認 + $r->予定却下 + $r->予定承認済 }}</td>
              </tr>
              <tr class="bdr">
                <th class="bg-primary"><small>{{ $r->division_name or '未所属' }}</small></th>
                <td class="text-muted">{{ $r->実績未入力 or 0 }}</td>
                <td class="text-warning">{{ $r->実績未承認 or 0 }}</td>
                <td class="text-danger">{{ $r->実績却下 or 0 }}</td>
                <td class="text-success">{{ $r->実績承認済 or 0 }}</td>
                <td>{{ $r->実績未入力 + $r->実績未承認 + $r->実績却下 + $r->実績承認済 }}</td>

              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection

@section('footer')
@parent
@endsection
