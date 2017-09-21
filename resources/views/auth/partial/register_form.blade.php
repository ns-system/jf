<form class="form-horizontal" role="form" method="POST" action="/auth/register">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="col-md-10 col-md-offset-1">
        <div class="form-group">
            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-primary-important">
                    <span class="glyphicon glyphicon-envelope" aria-hidden="false"></span>
                </label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }} @jf-nssinren.or.jp" placeholder="input your e-mail address.">
            </div>
            <span class="help-block"><small class="text-warning">メールアドレスを入力してください。すでに登録されているメールアドレスは利用できません。</small></span>
        </div>
    </div><div class="col-md-1"></div>

    <div class="col-md-10 col-md-offset-1">
        <div class="form-group">
            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-primary-important">
                    <span class="glyphicon glyphicon-user" aria-hidden="false"></span>
                </label>
                <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="input your name.">
            </div>
            <span class="help-block"><small class="text-warning">ユーザー名を入力してください。</small></span>
        </div>
    </div><div class="col-md-1"></div>

    <div class="col-md-10 col-md-offset-1">
        <div class="form-group">
            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-primary-important">
                    <span class="glyphicon glyphicon-search" aria-hidden="false"></span>
                </label>
                <input type="password" class="form-control" name="password" placeholder="input password.">
            </div>
            <span class="help-block"><small class="text-warning">パスワードを入力してください。</small></span>
        </div>
    </div><div class="col-md-1"></div>

    <div class="col-md-10 col-md-offset-1">
        <div class="form-group">
            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-primary-important">
                    <span class="glyphicon glyphicon-search" aria-hidden="false"></span>
                </label>
                <input type="password" class="form-control" name="password_confirmation" placeholder="input password again.">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-warning"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> 登録</button>
                </span>
            </div>
            <span class="help-block"><small class="text-warning">パスワードをもう一度入力してください。</small></span>
        </div>
    </div><div class="col-md-1"></div>

</form>