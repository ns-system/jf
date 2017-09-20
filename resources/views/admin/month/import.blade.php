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

    <div class="text-right" style="margin-bottom: 10px; display: none;">
        <a href="{{route('admin::super::month::status', ['id'=>$id])}}" class="btn btn-success btn-sm">処理結果を確認する</a>
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
                <span class="text-success">
                    <span class="glyphicon" aria-hidden="true" id="pre_process_{{$r->key_id}}" style="font-size: 24px;"></span>
                </span>
            </td>
            <td>
                <span class="text-success">
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
                    <div class="progress" >
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" id="progress_post_{{$r->key_id}}" style="width: 0%;"></div>
                    </div>
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


</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
var array = JSON.parse('<?php echo json_encode($array); ?>');
//var process_continue = 9999;
var timer;
var tmp_timer;
var is_continue = true;

$(function(){
    timer = setInterval(function(){
        connectAjax(array);
//        console.log(process_continue);
    }, 5000);

    tmp_timer = setInterval(function(){
        if(is_continue == false){
            clearInterval(timer);
            console.log('timer');
            // redirect
        }
    }, 5000);

    // if(process_continue <= 0){
    //     console.log('clear');
    //     clearInterval(timer);
    // }
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
            var s = rows['status'];
            console.log(s);
    //                process_continue = rows['max_cnt'] - rows['now_cnt'];
            if(s['is_import_end'] == true){
                clearInterval(timer);
                clearInterval(tmp_timer);
                is_continue = false;
//                console.log('fin');
            }
            editHtml(rows['rows']);
        },
        (error) => {
//            console.log(error);
            alert('エラーが発生しました。処理を最初から行ってください。');
        }
    );
//    console.log('hi');
}

function editHtml(rows){
//    console.log('call');
    Object.keys(rows).forEach(function(id, index, array){

        var r = rows[id];
        // Progress bar
        if(r['is_pre_process_end'] == true){
            $('#pre_process_'+id).removeClass('glyphicon-repeat rotate').addClass('glyphicon-ok');
        }else if(r['is_pre_process_start'] == true){
            $('#pre_process_'+id).addClass('glyphicon-repeat rotate');
        }

        if(r['is_post_process_end'] == true){
            $('#post_process_'+id).removeClass('glyphicon-repeat rotate').addClass('glyphicon-ok');
        }else if(r['is_post_process_start'] == true){
            $('#post_process_'+id).addClass('glyphicon-repeat rotate');
        }


        if(r['is_post_process_start'] == true){
            var now = r['executed_row_count'];
            var max = r['row_count'];
            var p = Math.round((now / (max+1)) * 100);
            console.log('% = '+p+' - '+now+' / '+max);
            if(r['is_import'] == false){
                if(p > 20){
                    $('#progress_post_'+id).css('width', p+'%').html('処理中...');
                }else{
                    $('#progress_post_'+id).css('width', p+'%');
                }
            }else{
                $('#progress_post_'+id).css('width', '100%').removeClass('active').html('処理終了');
            }
        }

        // Rowカウンタ
        $('#row_count_'+id).html(r['row_count'].toLocaleString());
        $('#executed_row_count_'+id).html(r['executed_row_count'].toLocaleString());
        $('#start_time_'+id).html(r['process_started_at']);
        $('#end_time_'+id).html(r['process_ended_at']);

        // progress
    });
}
</script>
@endsection