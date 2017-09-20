@extends('layout')
@section('title', '処理状況')

@section('header')
@parent
@section('brand', '処理状況確認')
@endsection


@section('brand')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')
</div>
@endsection

<div style="margin-top: 100px;"></div>


@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        @include('partial.alert')
        <div class="border-bottom"><h2>処理状況確認 <small> - {{date('Y年n月分', strtotime($id.'01'))}} （{{$count}}件）</small></h2></div>
        <div class="text-right">
            <div class="btn-group">
                <a href="{{route('admin::super::month::export', ['id'=>$id])}}" class="btn btn-primary btn-sm">処理リスト</a>
                <a href="{{route('admin::super::month::export_nothing', ['id'=>$id])}}" class="btn btn-warning btn-sm">処理不能リスト</a>

                <button class="btn btn-success btn-sm"  data-toggle="modal" data-target="#search">検索する</button>
            </div>
        </div>

        @include('admin.month.partial.status_search_form')
        @include('admin.month.partial.status_list')
        @if(isset($parameters))
        {!! $rows->appends($parameters)->render() !!}
        @else {!! $rows->render() !!} @endif
    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
$(function(){

// 	$('.btn input:checked').each(function(){
// 		var btn = $(this).parent('.btn');
// //		console.log(btn.html());
// 			btn.removeClass('btn-default').addClass('btn-primary');
// 	});

// 	$('.btn-group .btn').click(function(){
// 		var obj = $(this).siblings();
// 		obj.each(function(){
// 			$(this).removeClass('btn-primary').addClass('btn-default');
// 		});
// 		$(this).removeClass('btn-default').addClass('btn-primary');
// 	});
});
</script>
@endsection