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
                @if(Auth::check())
                <li>
                    <a href="{{route('app::user::show', ['id'=>\Auth::user()->id])}}" style="margin: 10px 0; font-size: 80%">
                        <img class="cover img-thumbnail" @if(\Auth::user()->user_icon != '') src="{{asset('/user_icon/' . \Auth::user()->user_icon)}}" @else src="{{asset('/user_icon/unset.png')}}" @endif>
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