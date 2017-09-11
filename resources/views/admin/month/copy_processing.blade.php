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
<div class="col-md-8 col-md-offset-2">
    <div class="container-fluid">
        @include('partial.alert')
        <div class="border-bottom">
            <h2>CSVファイルコピー処理</h2>
        </div>

        <div class="well">
            <div class="progress">
                <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress" role="progressbar" style="width: 100%;">処理中です...しばらくお待ちください</div>

            </div>
            <p>ファイルをUSBからコピーしています。</p>
            <p>しばらくそのままでお待ち下さい。</p>
{{--             <button class="btn btn-primary" id="ajax">ajax</button> --}}
        </div>

    </div>
</div>

@endsection

@section('footer')
@parent
<script type="text/javascript">
$(function(){

    var cnt = 0;
    setInterval(function(){
        var per = $('#progress').css('width');
        redirectTo();
        cnt++;
        if(cnt == 4){
//            alert();
        }
    }, 4000);
});


function redirectTo(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type     : 'POST',
//        url      : '/admin/super_user/month/copy_processing/201709',
        url      : "{{route('admin::super::month::copying', ['id'=>$id])}}",
        dataType : 'json',
    }).then(
        (data) => {
            if(data['status'] != true){
//                console.log('still runnning...');
            }else{
//                alert('complete!');
                location.href = "{{route('admin::super::month::confirm', ['id'=>$id])}}";
            }
        },
        () => {
            alert('エラーが発生しました。処理を最初から行ってください。');
        }
    );
}
</script>
@endsection