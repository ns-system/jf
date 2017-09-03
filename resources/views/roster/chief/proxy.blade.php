@extends('layout')

@section('title', '勤怠管理')

@section('header')
@parent
@section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('roster.app.partial.sidebar_app')

</div>
@endsection



@section('content')
<div class="col-md-10">
<div class="container-fluid">
        @include('partial.alert')

<form class="form-horizontal" role="form" method="POST" action="/roster/chief/proxy/edit">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

<table class="table">
    <tbody>
        @foreach($rows as $div)
        <tr><th class="bg-primary text-left" colspan="2">{{$div['division_name']}}</th></tr>
        @foreach($div['users'] as $user)
        <tr>
            <td>{{$user->User->name}}さん</td>
            <td>
                <div class="input-group">
                    <select class="form-control" name="is_proxy[{{$user->user_id}}]">
                        <option value="0">一般</option>
                        <option value="1" @if($user->RosterUser->is_proxy == true) selected="selected" @endif>代理人</option>
                    </select>
                    <span class="input-group-addon bg-primary-important">
                        <input type="hidden" name="is_proxy_active[{{$user->user_id}}]" value="0">
                        <input type="checkbox" name="is_proxy_active[{{$user->user_id}}]" @if($user->RosterUser->is_proxy_active == true) checked="checked" @endif value="1">有効
                    </span>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" formaction="/roster/chief/proxy/edit?id={{$user->user_id}}">登録する</button>
                    </span>
                </div>
            </td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>
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