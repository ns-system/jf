<form role="form" method="POST" action="/auth/login" style="margin-bottom: 0px;">
  {{-- CSRF対策--}}
  <input type="hidden" name="_token" value="{{ csrf_token() }}">

  <div style="padding: 20px 60px;">

    <div class="input-group" style="width: 100%; margin-bottom: 30px;">
      <label class="input-group-addon control-label bg-success-important" style="min-width: 150px;">
        <small>メールアドレス</small>
      </label>
      <input type="email" class="form-control" id="email" name="email" @if(!empty(old('email'))) value="{{ old('email') }}" @else value="" @endif placeholder="登録されているメールアドレスを入力してください">
      <span class="input-group-btn">
        <button type="button" class="btn btn-success" data-toggle="tooltip" title="@以下を自動で入力します" onclick="setDomainName()">@</button>
      </span>
    </div>

    <div class="input-group" style="margin-bottom: 10px;">
      <label class="input-group-addon control-label bg-success-important" style="min-width: 150px;">
        <small>パスワード</small>
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

<script type="text/javascript">
  function setDomainName () {
    var email = $('#email').val()
    email    += "@jf-nssinren.or.jp"
    $('#email').val(email)
  }
</script>