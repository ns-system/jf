@extends('layout')

@section('title', 'ユーザー権限')

@section('header')
@parent
@section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')

</div>
@endsection



@section('content')
<div class="col-md-10">
@include('partial.alert')
<div class="border-bottom"><h2>勤怠管理システム ユーザー情報変更 <small> - {{$user->last_name}} {{$user->first_name}}さん</small></h2></div>

<div class="col-md-8 col-md-offset-2">
    <div class="panel panel-primary">
        <div class="panel-heading">ユーザー情報変更</div>
        <div class="panel-body">
            @include('roster.admin.user.partial.form')
        </div>
    </div>
</div>

</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
$(function(){
    $('#add-division').click(function(){
        var trg = $(this).attr('data-target');
        $('#insert-division').append($(trg).html());
    });
});
</script>
@endsection