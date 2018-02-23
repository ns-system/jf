@extends('layout')

@section('title', '部署')

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
    <div class="border-bottom">
        <h2>{{$div->division_name}}<small> - 月を選択</small></h2>
    </div>

    @if(!empty($month))
    @foreach($month as $m)
    <div class="col-md-3" style="margin-bottom: 20px; ">
        <a href="{{route('app::roster::division::show', ['division'=>$id, 'ym'=>$m['id']])}}"
            @if($m['id'] == $this_month) class="btn btn-lg btn-success btn-block"
            @else class="btn btn-lg btn-primary btn-block"
            @endif
            >{{$m['display']}} <span class="badge">{{$m['count']}}件</span></a>
        </div>
        @endforeach
        @else
        <div class="alert alert-warning" role="alert">データが見つかりませんでした。</div>
        @endif
    </div>
</div>
@endsection

@section('footer')
@parent
@endsection