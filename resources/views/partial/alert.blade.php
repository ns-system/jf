@if (count($errors) > 0)
<div class="alert alert-danger alert-dismissible fade in" rore="alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span>There were some problems with your input.</span>
    </p>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (Session::has('flash_message') > 0 || (isset($info_message) && $info_message != null))
<div class="alert alert-success alert-dismissible fade in" rore="alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span>{{Session::get('flash_message')}}</span>
        @if(isset($info_message) && $info_message != null) <span>{{$info_message}}</span> @endif
    </p>
</div>
@endif

@if(!empty($warn_message) || Session::has('warn_message') > 0)
<div class="alert alert-warning alert-dismissible fade in" rore="alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
        <p><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> @if(!empty($warn_message)) {{$warn_message}} @endif</p>
        <p><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> @if(Session::has('warn_message') > 0) {{Session::get('warn_message')}} @endif</p>
</div>
@endif

@if (!empty($danger_message))
<div class="alert alert-warning alert-dismissible fade in" rore="alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <p>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span>{{$danger_message}}</span>
    </p>
</div>
@endif