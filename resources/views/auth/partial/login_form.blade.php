<form class="form-horizontal" role="form" method="POST" action="/auth/login">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="col-md-10 col-md-offset-1" style="margin-bottom: 20px;">
        <div class="form-group">
            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-primary-important">
                    <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
                </label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="登録されているメールアドレスを入力してください。">
            </div>
        </div>
    </div><div class="col-md-1"></div>

    <div class="col-md-10 col-md-offset-1">
        <div class="form-group">
            <div class="input-group">
                <label class="input-group-addon control-label bg-primary-important">
                    <span class="glyphicon glyphicon-search" aria-hidden="false"></span>
                </label>
                <input type="password" class="form-control" name="password" placeholder="登録されているパスワードを入力してください。">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> ログイン</button>
                </span>
            </div>

            <div>
                <a href="/password/email"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> パスワードを忘れましたか？</a>
            </div>
        </div>
    </div><div class="col-md-1"></div>
</form>