@if (Session::has('flash_message') > 0 || (isset($info_message) && $info_message != null))
<div class="alert alert-success alert-dismissible fade in" rore="alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <div>
        <p>
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <strong>成功：要修正</strong>
        </p>
        <span>{{Session::get('flash_message')}}</span>
        @if(isset($info_message) && $info_message != null) <span>{{$info_message}}</span> @endif
    </div>
</div>
@endif


@if (Session::has('success_message') > 0 || (isset($success_message) && $success_message != null))
<div class="alert alert-success alert-dismissible fade in alert-fixed" rore="alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <div>
        <p>
            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
            <strong>成功：</strong>
        </p>
        <span>{{Session::get('success_message')}}</span>
        @if(isset($success_message) && $success_message != null) <span>{{$success_message}}</span> @endif
    </div>
</div>
@endif

@if (Session::has('info_message') > 0 || (isset($info_message) && $info_message != null))
<div class="alert alert-info alert-dismissible fade in alert-fixed" rore="alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <div>
        <p>
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <strong>情報：</strong>
        </p>
        <span>{{Session::get('info_message')}}</span>
        @if(isset($info_message) && $info_message != null) <span>{{$info_message}}</span> @endif
    </div>
</div>
@endif

@if(!empty($warn_message) || Session::has('warn_message') > 0)
<div class="alert alert-warning alert-dismissible fade in alert-fixed" rore="alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <strong>注意：</strong>
    </p>
    <p>
        @if(!empty($warn_message)) {{$warn_message}} @endif
        @if(Session::has('warn_message') > 0) {{Session::get('warn_message')}} @endif
    </p>
</div>
@endif

@if (!empty($danger_message) || Session::has('danger_message') > 0)
<div class="alert alert-danger alert-dismissible fade in alert-fixed" rore="alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <p>
        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
        <strong>警告：</strong>
    </p>
    <p>
        @if(!empty($danger_message)) {{$danger_message}} @endif
        @if(Session::has('danger_message') > 0) {{Session::get('danger_message')}} @endif
    </p>
</div>
@endif

@if (count($errors) > 0)
<div class="alert alert-danger alert-dismissible fade in alert-fixed" rore="alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <p><strong>エラー：</strong></p>
    <ul>
        <?php $i = 0; $max_display = 3; /*$max_draw = 10;*/ ?>
        @foreach ($errors->all() as $error)
    @if($i == $max_display) </ul>
    <ul class="collapse" id="hidden-error-list"> @endif
        <li>{{ $error }}</li>
        <?php $i++; ?>
        @endforeach
    </ul>
    @if($i > $max_display)
    <a href="#hidden-error-list" data-toggle="collapse" class="btn btn-danger btn-xs" style="margin-left: 40px; margin-top: 10px;">エラーを全て見る／隠す</a>
    @endif
</div>
@endif