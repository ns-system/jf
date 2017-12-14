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
    @include('partial.check_sidebar')
</div>
@endsection --}}

<div style="margin-top: 100px;"></div>


@section('content')
<div class="col-md-10 col-md-offset-1">
    <div class="container-fluid">
        @include('partial.alert')
        @include('admin.month.partial.breadcrumbs')

        <div class="border-bottom">
            <h2>CSVファイルコピー処理</h2>
        </div>

        <div class="col-md-10 col-md-offset-1">
            <div class="well" style="padding: 30px 60px;">
                <div class="progress">
                    <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress" role="progressbar" style="width: 100%;">処理中です...しばらくお待ちください</div>
                </div>
                <p>ファイルをコピーしています。</p>
                <p>しばらくそのままでお待ち下さい。</p>
            </div>
        </div>

    </div>
</div>

@include('admin.month.partial.error_box')

@endsection

@section('footer')
@parent
<script type="text/javascript">
var timer;

$(function(){
    var cnt = 0;
    timer = setInterval(function(){
        redirectTo();
        cnt++;
        if(cnt == 10){
        }
    }, 5000);
});


function redirectTo(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type     : 'POST',
        url      : "{{route('admin::super::month::copying', ['id'=>$id, 'job_id'=>$job_id])}}",
        dataType : 'json',
    }).then(
        (data) => {
                console.log(data);
            var s = data['status'];
            if(s['is_copy_end'] == true){
                // redirect
                location.href = "{{route('admin::super::month::import_confirm', ['id'=>$id, 'job_id'=>$job_id])}}";
            }else if(s['is_copy_error'] == true){
                // Error
//                alert('処理に失敗しました。最初から処理を行ってください。');
                setErrorList(data);
//                setErrorList(data['status']);
                $('#progress').removeClass('progress-bar-info active').addClass('progress-bar-danger').html("処理は中断されました");
                clearInterval(timer);
//                location.href = "{{route('admin::super::month::failed')}}";
            }
        },
        (error) => {
            console.log(error);
            alert('エラーが発生しました。処理を最初から行ってください。');
        }
    );
}

</script>
@endsection