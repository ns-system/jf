
<div class="alert alert-danger alert-dismissible fade in alert-fixed" rore="alert" style="right: 20px; left: auto; display: none;">
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
    </button>
        <strong class="text-danger"  id="danger-list-title"  style="display: none;">警告：</strong>
        <ul class="text-danger-light" id="danger-list"></ul>
        <strong class="text-warning" id="warning-list-title" style="display: none;">注意：</strong>
        <ul class="text-warning-light" id="warning-list" style="margin-bottom: 0;"></ul>

</div>
{{-- <button class="btn btn-primary btn-xs" id="error-box-list-visiblity" data-visible="true">表示／非表示</button> --}}

<style type="text/css">
/*#error-list-box{
    width: 400px;
    position: fixed;
    background: rgba(0, 0, 0, 0.8);
    padding: 5px 20px;
    padding-bottom: 24px;
    border-radius: 3px;
    box-shadow: 0 0 3px rgba(0,0,0,0.8);
    right: 30px;
    bottom: 50px;
    display: none;
    max-height: 300px;
    overflow-y: auto;
}
#error-box-list-visiblity{
    position: fixed;
    right: 40px;
    bottom: 60px;
    display: none;
}
#error-list-box::-webkit-scrollbar{
    width: 0;
}*/
</style>

<script type="text/javascript">

/**
 * 2次元配列まで対応
 * エラーメッセージを表示する関数
 * 配列で引き渡す場合、必ず連想配列のキーに'error_message'を指定すること
 * JSON形式くらいしか想定してません
 */
 function setErrorList(error_messages){
    $('#warning-list').html('');
    var error_counter = 0;

    if(error_messages['danger_message']){
        // console.log('1st');
        $('#danger-list-title').show();
        $('#danger-list').append('<li>'+error_messages['danger_message']+'</li>');
        error_counter++;
    }
    if(error_messages['warning_message']){
        $('#warning-list-title').show();
        $('#warning-list').append('<li>'+error_messages['warning_message']+'</li>');
        error_counter++;
    }

    $.each(error_messages, function(array_key, array_val){
        // console.log('2nd');
        if($.isPlainObject(error_messages[array_key])){
            $.each(error_messages[array_key], function(key, val){
                if(key == 'danger_message' && val){
                    $('#danger-list-title').show();
                    $('#danger-list').append('<li>'+val+'</li>');
                    error_counter++;
                }
                if(key == 'warning_message' && val){
                    $('#warning-list-title').show();
                    $('#warning-list').append('<li>'+val+'</li>');
                    error_counter++;
                }
            });
        }
    });

    // if($('#error-box-list-visiblity').attr('data-visible') == 'true' && error_counter > 0){
    if(error_counter > 0){
        $('#error-list-box').show();
    //     $('#error-box-list-visiblity').show();
    }
}

$(function(){
    $('#error-box-list-visiblity').click(function(){
        var flag = $(this).attr('data-visible');
        var $trg = $('#error-list-box');
        if(flag == 'true'){
            $(this).attr('data-visible', 'false');
            $trg.hide();
            return true;
        }
        $(this).attr('data-visible', 'true');
        $trg.show();
        return true;
    });
})
</script>