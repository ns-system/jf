<style type="text/css">
.cover {
    object-fit: contain;
    width: 45px;
    height: 45px;
    background: #eee;
    border-radius: 10%;
}
</style>

<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/" style="padding: 25px 10px;">
                <label class="brand-logo">
                    @if(env('APP_ENV') !== 'product') <small class="label label-warning" style="position: absolute; top: 2px;">{{env('APP_ENV')}}</small>@endif
                </label>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="http://www.jf-nssinren.or.jp/" target="_blank">Official</a></li>
                <li><a href="http://192.1.10.136/myweb10po" target="_blank">Groupware</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> マニュアル <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{ asset('/manuals/01_01_勤怠管理システム_マニュアル_v1.00.pdf') }}" target="_blank">勤怠管理システム</a></li>
                        <li role="presentation" class="divider"></li>

                        <li><a href="{{ asset('/manuals/02_01_推進支援システム_マニュアル_v2.00.pdf') }}" target="_blank">推進支援システム</a></li>
                        <li><a href="{{ asset('/manuals/02_03_推進支援システム_補足_エクセル操作方法.pdf') }}" target="_blank">推進支援システム 補足</a></li>
                    </ul>
                </li>
                @if(Auth::check())
                <li>
                    <a href="{{route('app::user::show', ['id'=>\Auth::user()->id])}}" style="margin: 10px 0; font-size: 80%">

                        @include('partial.avatar', ['avatar'=>\Auth::user()->user_icon, 'size'=>'48px'])
{{--                         <img class="cover img-thumbnail" @if(\Auth::user()->user_icon != '') src="{{asset('/user_icon/' . \Auth::user()->user_icon)}}" @else src="{{asset('/user_icon/unset.png')}}" @endif> --}}
                        {{Auth::user()->last_name}} {{Auth::user()->first_name}}<small>さん</small>
                    </a>
                </li>
                @endif
            </ul>
            @if(Auth::check())
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/auth/logout"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> ログアウト</a></li>
            </ul>
            @endif
        </div>
    </div>
</nav>