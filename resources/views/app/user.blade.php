@extends('layout')

@section('title', 'ユーザー情報変更')

@section('header')
@parent
@section('brand', 'ユーザー情報変更')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')
</div>
@endsection


@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        @include('partial.alert')
        <div class="border-bottom"><h2>{{$user->last_name}} {{$user->first_name}}<span style="font-size: 80%;">さんのプロフィール</span></h2></div>

        @include('app.partial.user_form')
    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
    $(function() {
        $('input[type=file]').after('<span></span>');

    // アップロードするファイルを選択
    $('input[type=file]').change(function() {
        var file = $(this).prop('files')[0];

            // 画像以外は処理を停止
            if (! file.type.match('image.*')) {
              // クリア
              $(this).val('');
              $('span').html('');
              return;
          }

            // 画像表示
            var reader = new FileReader();
            reader.onload = function() {
                var img_src = $('<img style="max-height: 250px; border: 1px solid #fff; border-radius: 5px;">').attr('src', reader.result);
                $('#img').html(img_src);
                $('#old-img').remove();
            }
            reader.readAsDataURL(file);
        });
});

function checkFile(){
    var trg = $('#file')[0].files[0];
    if(trg != null){
        return true;
    }
    alert('ファイルがセットされていません。');
    return false;
}
</script>
@endsection