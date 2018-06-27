@extends('layout')

@section('title', '勤怠管理')

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
<style>
.outline { background: none; border: 1px solid #ccc; color: #666; }
.headline { font-size: 36px; }
</style>
<?php $jp_week = ['日','月','火','水','木','金','土',]; ?>
<div class="col-md-10">

  <div class="row">
    <div class="col-md-12">

      <span class="headline">{{ $this_month }}</span>
      <span>ユーザー数：{{ $user_count }}人</span>
      <div class="btn-group" role="group" style="margin-bottom: 20px;">
        <a class="btn btn-warning btn-sm" href="{{ route('app::roster::chief_index', ['month' => $before_month]) }}"><i class="glyphicon glyphicon-chevron-left"></i>前月</a>
        <a class="btn btn-info btn-sm"    href="{{ route('app::roster::chief_index', ['month' => $next_month]) }}">翌月<i class="glyphicon glyphicon-chevron-right"></i></a>
      </div>

      {{-- {{ dd($calendar) }} --}}
      <table class="table table-hover">
        <thead>
          @foreach($jp_week as $w => $week)
          <th class="bg-primary"><span @if($w == 0) class="text-danger-light" @elseif($w == 6) class="text-info-light" @endif>{{ $week }}</span></th>
          @endforeach
        </thead>
        <tbody>
          @foreach($calendar as $c)
          <tr>
            @foreach($c as $w => $d)
            <td class="text-left">
              @if(!empty($d['day']))
              @include('roster.chief.partial.dialog', ['key'=>$d['key'], 'eusers' => $entered_users[$d['key']]])
              <p>
                <small class="label @if(!empty($d['holiday'] || $w == 0)) label-danger @elseif($w == 6) label-info @else label-primary @endif">{{ $d['day'] }} ({{ $jp_week[$w] }})</small>
                <button type="button" class="btn btn-link btn-xs" data-toggle="modal" data-target="#{{ $d['key'] }}">
                  <span class="glyphicon glyphicon-search text-success" aria-hidden="true"></span>
                </button>

              </p>
              @endif

              @if(!empty($d['data']))
              <?php $r = $d['data']; ?>
{{--               <div>
                @if($w == 0 || $w == 6 || !empty($d['holiday']))
                <span class="label label-warning">入力</span> <small>{{ $r['pEntry'] }} / {{ $r['aEntry'] }}</small>
                @else
                <span class="label outline">入力</span> <small>{{ $r['pEntry'] }} / {{ $r['aEntry'] }}</small>
                @endif
              </div>
              <div>
                <span class="label outline">承認</span> <small>{{ $r['pAccept'] }} / {{ $r['aAccept'] }}</small>
              </div>
              <div>
                <span class="label outline">合計</span> <small>@if($w == 0 || $w == 6 || !empty($d['holiday'])) 0 @else {{ $r['total'] }} @endif 人</small>
              </div> --}}

              @if($w == 0 || $w == 6 || !empty($d['holiday'])) {{-- holiday --}}
              <div>
                <p><span @if($r['aEntry'] == 0)  class="label outline" @else class="label label-warning" @endif>入力</span> <small>{{ $r['aEntry'] }}件</small></p>
                <p><span @if($r['aAccept'] == 0) class="label outline" @else class="label label-warning" @endif>承認</span> <small>{{ $r['aAccept'] }}件</small></p>
              </div>
              @else {{-- is not holiday --}}
              <div>
                <p><span @if($r['aEntry'] == $r['total']  || $r['aEntry'] == 0)  class="label outline" @else class="label label-warning" @endif>入力</span> <small>{{ $r['aEntry'] }}件</small></p>
                <p><span @if($r['aAccept'] == $r['total'] || $r['aAccept'] == 0) class="label outline" @else class="label label-warning" @endif>承認</span> <small>{{ $r['aAccept'] }}件</small></p>
              </div>
              @endif {{-- holiday end --}}

              @endif
            </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>

    </div>
  </div>
</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
</script>
@endsection