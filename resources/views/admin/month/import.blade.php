@extends('layout')
@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('title', '月別マスタ')

@section('header')
@parent
@section('brand', '月別マスタ')
<style type="text/css">
.rotate{ animation: rotation 2s linear infinite; }
@keyframes rotation{
    0%{ transform: rotateZ(0deg) }
    100%{ transform: rotateZ(360deg); }
}
</style>
@endsection


@section('brand')
@endsection

{{-- @section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')
</div>
@endsection --}}

<div style="margin-top: 100px;"></div>


@section('content')
<div class="col-md-10 col-md-offset-1">
    <div class="container-fluid">
        @include('partial.alert')
        @include('admin.month.partial.breadcrumbs')
        <div class="border-bottom">
            <h2>データベース セットアップ処理<small> - {{$rows->count()}}件</small></h2>
        </div>
    </div>

<table class="table table-hover table-striped table-small va-middle">
    <thead>
        <tr class="bg-primary">
            <th width="4%">No</th>
            <th width="5%">事前</th>
            <th width="5%">DB</th>
            <th width="30%">データ名</th>
            <th width="6%">処理区分</th>
            <th width="30%"><p>事前チェック</p><p>DBセット処理</p></th>
            <th width="10%">データ件数</th>
            <th width="10%">処理時刻</th>
        </tr>
    </thead>
    <tbody>
        <?php $array = []; ?>
        @foreach($rows as $i => $r)
        <tr>

            <th class="bg-primary">{{$i + 1}}</th>
            <td>
                <span class="text-success animate">
                    <span class="glyphicon" aria-hidden="true" id="pre_process_{{$r->key_id}}" style="font-size: 24px;"></span>
                </span>
            </td>
            <td>
                <span class="text-success animate">
                    <span class="glyphicon" aria-hidden="true" id="post_process_{{$r->key_id}}" style="font-size: 24px;"></span>
                </span>
            </td>
            <td class="text-left">
                <p>{{$r->zenon_format_id}}：{{$r->zenon_data_name}}</p>
                <p>{{$r->csv_file_name}}</p>
                <p>@if(!empty($r->table_name)) {{$r->database_name}}.{{$r->table_name}} @endif</p>
            </td>
            <td>
                <p>
                    @if($r->is_cumulative)      <label class="label label-info"   >累積</label>
                    @else                       <label class="label label-default">累積</label> @endif
                </p>
                <p>
                    @if($r->is_split)           <label class="label label-info"   >分割</label>
                    @else                       <label class="label label-default">分割</label>  @endif
                </p>
                <p>
                    @if($r->is_account_convert) <label class="label label-info"   >変換</label>
                    @else                       <label class="label label-default">変換</label> @endif
                </p>
            </td>
            <td>
                <div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" id="progress_post_{{$r->key_id}}" style="width: 0%;"></div>
                    </div>
                    <p class="error_message text-left text-warning"></p>
                </div>

            </td>

            <td class="text-right">
                <p><span id="executed_row_count_{{$r->key_id}}">0</span>件</p>
                <p><span id="row_count_{{$r->key_id}}">0</span>件</p>
            </td>
            <td>
                <p><span id="start_time_{{$r->key_id}}">-</span></p>
                <p><span id="end_time_{{$r->key_id}}">-</span></p>
            </td>
        </tr>
        <?php $array[] = $r->key_id; ?>
        @endforeach
    </tbody>
</table>

@include('admin.month.partial.error_box')
<div style="display: none; position: fixed; right: 40px; bottom: 15px;" id="process-list" class="text-right">
    <a href="{{route('admin::super::month::status', ['id'=>$id])}}">処理結果を確認する</a>
</div>

</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
var array = JSON.parse('<?php echo json_encode($array); ?>');
var timer;

$(function(){
    timer = setInterval(function(){
        connectAjax(array);
    }, 5000);

});

function connectAjax(array){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type     : 'POST',
        data     : {'input' : array},
        url      : "{{route('admin::super::month::importing', ['id'=>$id, 'job_id'=>$job_id])}}",
        dataType : 'json',
    }).then(
        (rows) => {
            console.log(rows);
            editHtml(rows['rows']);
            var s = rows['status'];
            if(s['is_import_error']){
                setErrorList(s);
                clearInterval(timer);
                stopAllAnimation();
                changeProgressBarToError();
            }
            if(s['is_import_end'] == true){
                clearInterval(timer);
                $('#process-list').show();
            }
        },
        (error) => {
//            alert('エラーが発生しました。処理を最初から行ってください。');
            console.log('エラーが発生しました。処理を最初から行ってください。');
            clearInterval(timer);
            location.reload();
        }
    );
//    console.log('hi');
}

function editHtml(rows){
//    console.log('call');
    Object.keys(rows).forEach(function(id, index, array){

        var r = rows[id];
        if(r['is_pre_process_start']){  $('#pre_process_'+id).addClass('glyphicon-repeat rotate'); }
        if(r['is_pre_process_end']){    $('#pre_process_'+id).removeClass('glyphicon-repeat rotate').addClass('glyphicon-ok'); }
        if(r['is_pre_process_error']){  $('#pre_process_'+id).removeClass('glyphicon-repeat rotate').addClass('glyphicon-remove').parent().removeClass('text-success').addClass('text-warning'); }

        if(r['is_post_process_start']){ $('#post_process_'+id).addClass('glyphicon-repeat rotate'); }
        if(r['is_post_process_end']){   $('#post_process_'+id).removeClass('glyphicon-repeat rotate').addClass('glyphicon-ok'); }
        if(r['is_post_process_error']){ $('#post_process_'+id).removeClass('glyphicon-repeat rotate').addClass('glyphicon-remove').parent().removeClass('text-success').addClass('text-warning'); }

        if(r['is_post_process_start'] == true){
            var now = r['executed_row_count'];
            var max = r['row_count'];
            var p = Math.round((now / (max+1)) * 100);
//            console.log('% = '+p+' - '+now+' / '+max);
            if(!r['is_import']){
                if(p > 20){ $('#progress_post_'+id).css('width', p+'%').html('処理中...'); }
                else{       $('#progress_post_'+id).css('width', p+'%'); }
            }else{
                $('#progress_post_'+id).css('width', '100%').removeClass('active').html('処理終了');
            }
        }
        // エラーメッセージ
        if(r['is_pre_process_error'] || r['is_post_process_error']){
            $('#progress_post_'+id).css('width', '100%').removeClass('active progress-bar-success').addClass('progress-bar-warning').html('処理中断');
            console.log(r['key_id'],r['is_pre_process_error'],r['is_post_process_error'],r['warning_message']);
            $('#progress_post_'+id).parent().next('.error_message').html(r['warning_message']);
        }

        // Rowカウンタ
        $('#row_count_'+id).html(r['row_count'].toLocaleString());
        $('#executed_row_count_'+id).html(r['executed_row_count'].toLocaleString());
        $('#start_time_'+id).html(r['process_started_at']);
        $('#end_time_'+id).html(r['process_ended_at']);

        // progress
    });
    setErrorList(rows);
}

function stopAllAnimation(){
    $('.animate').each(function(){
        $(this).children().removeClass('rotate');
    });
}

function changeProgressBarToError(){
    $('.progress').each(function(){
        $(this).children().removeClass('active').html('処理中断');
        if($(this).children().hasClass('progress-bar-warning')){
        }else{
            $(this).children().removeClass('progress-bar-success').addClass('progress-bar-danger').css('width','100%');
        }
    });
}
</script>
@endsection