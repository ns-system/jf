@extends('layout')
@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('title', '月別マスタ')

@section('header')
@parent
@section('brand', '月別マスタ')
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
        <div class="border-bottom">
            <h2>CSVファイルコピー処理<small> - 月次処理</small></h2>
        </div>

<form method="POST" action="{{route('admin::super::month::dispatch', ['id'=>$id])}}">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="text-right" data-spy="affix" data-offset-top="130" style="margin-bottom: 10px; top: 110px; right: 135px;">
            <div style="margin-bottom: 10px;">
                <div class="btn-group">
                    <button type="button" id="process" class="btn btn-sm btn-primary" style="min-width: 115px;">処理対象のみ</button>
                    <button type="button" id="reset"   class="btn btn-sm btn-warning" style="min-width: 115px;">条件リセット</button>
                    <button type="button" id="more"    class="btn btn-sm btn-success" style="min-width: 115px;">もっと見る</button>
                </div>
            </div>
            <div>
                <div class="btn-group">
                    <button type="button" id="check"   class="btn btn-sm btn-primary" style="min-width: 115px;">全てにチェック</button>
                    <button type="button" id="uncheck" class="btn btn-sm btn-warning" style="min-width: 115px;">チェックを外す</button>
                    <button type="submit"              class="btn btn-sm btn-success" style="min-width: 115px;" onclick="return validateCheck();">処理を開始する</button>
            </div>
            </div>
        </div>


<table class="table table-hover table-striped table-small va-middle">
    <thead>
        <tr class="bg-primary">
            <th>No</th>
            <th>処理</th>
            <th>処理対象</th>
            <th>還元データ名　CSVファイル名</th>
            <th>還元日</th>
            <th>CSVファイル　処理状態</th>
            <th>目安還元日</th>
            <th>累積　分割　口座変換</th>
        </tr>
    </thead>

    <tbody>
    @foreach($files as $i => $f)
        <tr
            class="display"
            @if($i < 25) data-display="1"
            @else        data-display="0" @endif

            data-process="{{$f->is_process}}"
        >
            <th class="bg-primary">{{$i + 1}}</th>
            <td>
                <input
                    type="checkbox"
                    style="width: 18px;height: 18px;vertical-align: middle; margin:0; margin-bottom: 5px;"
                    name="process[{{$f->key_id}}]"
                    @if($f->is_process)
                        @if(!$f->is_import) checked @endif
                    @else disabled @endif
                >
                <input type="hidden" name="id[{{$f->key_id}}]" value="{{$f->key_id}}">
            </td>
            <td class="va-middle">
                
                <p>
                    @if($f->is_process) <label class="label label-success" style="min-width: 100px;">処理対象</label>
                    @else               <label class="label label-default" style="min-width: 100px;">処理対象外</label> @endif</p>
                </td>
            </td>
            <td>
                <p>{{$f->zenon_format_id}}</p>
                <p>{{$f->zenon_data_name}}</p>
                <P>{{$f->csv_file_name}}</P>
            </td>
            <td class="va-middle">
                <p>{{$f->csv_file_set_on}}</p>
            </td>
            <td class="va-middle">
                <p>
                    @if($f->is_exist) <label class="label label-success" style="min-width: 100px;">ファイルあり</label>
                    @else             <label class="label label-warning" style="min-width: 100px;">ファイルなし</label> @endif
                </p>
                <p>
                    @if($f->is_import)       <label class="label label-warning" style="min-width: 100px;">処理済み</label>
                    @elseif(!$f->is_process) <label class="label label-default" style="min-width: 100px;">処理対象外</label>
                    @else                    <label class="label label-danger" style="min-width: 100px;">未処理</label> @endif
                </p>
            </td>

            <td class="va-middle">{{$f->reference_return_date}}</td>

            <td>
                <p>
                    @if($f->is_cumulative)      <label class="label label-info"    style="min-width: 100px;">累積する</label>
                    @else                       <label class="label label-default" style="min-width: 100px;">累積しない</label> @endif
                </p>
                <p>
                    @if($f->is_split)           <label class="label label-info"    style="min-width: 100px;">分割する</label>
                    @else                       <label class="label label-default" style="min-width: 100px;">分割しない</label> @endif
                </p>
                <p>
                    @if($f->is_account_convert) <label class="label label-info"    style="min-width: 100px;">変換する</label>
                    @else                       <label class="label label-default" style="min-width: 100px;">変換しない</label> @endif
                </p>
            </td>
        </tr>
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
    $('.display').each(function(){
//        console.log($(this).attr('data-display'));
        if($(this).attr('data-display') == false){
            $(this).hide();
        }
    });
    $('#more').click(function(){
        var i = 0;
        $('.display[data-display="0"]').each(function(){
            $(this).show();
            $(this).attr('data-display', true);
            i++;
            if(i > 24){
                return false;
            }
        });
        if($('.display[data-display="0"]').length == 0){
            $('#more').attr('disabled', true);
        }
    });

    $('#process').click(function(){
        $('.display').show();
        $('.display[data-process="0"]').each(function(){
//            console.log($(this));
            $(this).hide();
        });
    });

    $('#reset').click(function(){
            $('#more').attr('disabled', false);
        $('.display').hide().attr('data-display', false);
        var i = 0;
        $('.display').each(function(){
            $(this).show().attr('data-display', true);
            i++;
            if(i > 24){
                return false;
            }
        });
    });

    $('#check').click(function(){
        $('input[type="checkbox"]').not(':disabled').each(function(){
            $(this).prop('checked', true);
        });
    });
    $('#uncheck').click(function(){
        $('input[type="checkbox"]').not(':disabled').each(function(){
            $(this).prop('checked', false);
        });
    });
});

function validateCheck(){
    var check_count = $('input[type="checkbox"]:checked').not(':disabled').length;
    if(check_count <= 0){
        alert('最低でも一つにチェックを入れてください。');
        return false;
    }else{
        return true;
    }
}
</script>
@endsection