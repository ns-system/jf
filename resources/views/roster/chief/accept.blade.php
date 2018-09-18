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
<div class="col-md-10">

<div class="row">
	<div class="col-md-12">
    <h2 style="margin: 0;">
        <nav style="display: inline-block;">
            <ul class="pager" style="margin: 0; text-align: left;">
                <li style=" font-size: 16px;"><a href="/roster/chief/accept?id={{$pages['prev']}}"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> {{date('Y年n月', strtotime($pages['prev'].'01'))}}</a></li>
		        <span><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
 {{date('Y年n月', strtotime($id.'01'))}} 勤怠管理承認</span>
                <li style=" font-size: 16px;"><a href="/roster/chief/accept?id={{$pages['next']}}">{{date('Y年n月', strtotime($pages['next'].'01'))}} <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    </a></li>
            </ul>
        </nav>
    </h2>
	</div>
</div>
<div class="container-fluid">

<form class="form-inline" role="form" method="POST" action="/roster/chief/accept/edit/all">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="row"><div class="col-md-12 text-right">
    	<button type="submit" class="btn btn-warning">一括で更新する</button>
    </div></div>
	@foreach($rows as $i=>$row)
		@if($i % 3) <div class="row"> @endif
		<div class="col-md-4">
		@include('roster.chief.partial.client')
		</div>
		@if($i % 3) </div> @endif
	@endforeach
    <div class="row"><div class="col-md-12 text-right">
    	<button type="submit" class="btn btn-warning">一括で更新する</button>
    </div></div>

</form>

</div>
</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
	$(function(){
		$('#debug-btn').click(function(){
			$('#debug').toggle();
		});
	});
</script>
@endsection