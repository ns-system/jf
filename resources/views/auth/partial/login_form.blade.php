<form role="form" method="POST" action="/auth/login" style="margin-bottom: 0px;">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div style="padding: 20px 60px;">

        <div class="input-group" style="width: 100%; margin-bottom: 30px;">
            <label class="input-group-addon control-label bg-primary-important">
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
            </label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="登録されているメールアドレスを入力してください">
        </div>

        <div class="input-group" style="margin-bottom: 10px;">
            <label class="input-group-addon control-label bg-primary-important">
                <span class="glyphicon glyphicon-search" aria-hidden="false"></span>
            </label>
            <input type="password" class="form-control" name="password" placeholder="登録されているパスワードを入力してください">
        </div>

        <div>
            <a href="/password/email"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> パスワードを忘れましたか？</a>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-success" style="width: 150px;"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> ログイン</button>
        </div>

    </div>
</form>
