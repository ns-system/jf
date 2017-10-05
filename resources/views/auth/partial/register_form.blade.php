<form role="form" method="POST" action="/auth/register">
        {{-- CSRF対策--}}
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div style="padding: 20px 60px;">
            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-success-important" style="min-width: 150px;">
                    <small>メールアドレス</small>
                </label>
                <input type="email" class="form-control" name="email" @if(empty(old('email'))) value="@jf-nssinren.or.jp" @else value="{{old('email')}}" @endif placeholder="メールアドレスを入力してください（@jf-nssinren.or.jp）">
            </div>
{{--             <span class="help-block"><small class="text-warning">メールアドレスを入力してください。すでに登録されているメールアドレスは利用できません。</small></span> --}}
            <span class="help-block" style="margin-bottom: 20px;"><small class="text-warning">すでに登録されているメールアドレスはご利用できません。</small></span>

            {{-- LastName --}}
            <div class="col-md-6" style="padding: 0; margin-bottom: 20px;">
            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-success-important" style="min-width: 50px;">
                    <small>姓</small>
                </label>
                <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" placeholder="山田">
            </div>
            </div>
            {{-- FirstName --}}
            <div class="col-md-5 col-md-offset-1" style="padding: 0; margin-bottom: 20px;">
            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-success-important" style="min-width: 50px;">
                    <small>名</small>
                </label>
                <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" placeholder="太郎">
            </div>
            </div>

            {{-- LastName --}}
            <div class="col-md-6" style="padding: 0; margin-bottom: 30px;">
            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-success-important" style="min-width: 50px;">
                    <small>せい</small>
                </label>
                <input type="text" class="form-control" name="last_name_kana" value="{{ old('last_name_kana') }}" placeholder="やまだ">
            </div>
            </div>
            {{-- FirstName --}}
            <div class="col-md-5 col-md-offset-1" style="padding: 0; margin-bottom: 30px;;">
            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-success-important" style="min-width: 50px;">
                    <small>めい</small>
                </label>
                <input type="text" class="form-control" name="first_name_kana" value="{{ old('first_name_kana') }}" placeholder="たろう">
            </div>
            </div>

            <div class="input-group" style="width: 100%; margin-bottom: 20px;">
                <label class="input-group-addon control-label bg-success-important" style="min-width: 150px;">
                    <small>パスワード</small>
                </label>
                <input type="password" class="form-control" name="password" placeholder="パスワードを入力してください">
            </div>
{{--             <span class="help-block"><small class="text-warning">パスワードを入力してください。</small></span> --}}

            <div class="input-group" style="width: 100%;">
                <label class="input-group-addon control-label bg-success-important" style="min-width: 150px;">
                    <small>パスワード（確認）</small>
                </label>
                <input type="password" class="form-control" name="password_confirmation" placeholder="もう一度パスワードを入力してください">
            </div>
            <span class="help-block"><small class="text-warning">パスワードをもう一度入力してください。</small></span>

                <div class="text-right">
                    <button type="submit" class="btn btn-warning" style="min-width: 150px;"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> ユーザーを登録する</button>
                </div>
        </div>

</form>