<form class="form-horizontal" role="form" method="POST" action="{{route('admin::super::user::edit', ['id'=>$user->id])}}">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="well">
        <div class="form-group">
            <div class="col-md-10 col-md-offset-1">

                <label for="InputSelect">スーパーユーザー</label>
                <select class="form-control" name="is_super_user">
                    <option value="0">一般ユーザー</option>
                    <option value="1" @if($user->is_super_user == true) selected="selected" @endif>スーパーユーザー</option>
                </select>
                <span class="help-block bolder"><small class="text-warning">システム全体の管理者になります。スーパーユーザー昇格後はユーザー側システムが利用できなくなります。</small></span>
            </div>
        </div>
    </div>

    <div class="well">
        <div class="form-group">
            <div class="col-md-10 col-md-offset-1">
                <label for="InputSelect">勤怠管理システム ユーザー</label>
                <a class="btn btn-block btn-primary" href="{{route('admin::roster::user::index')}}" style="margin-bottom: 10px;">登録情報を変更する</a>
                <span class="help-block bolder"><small  class="text-warning">
                    @if($user->RosterUser($user->id))
                    勤怠管理システムの管理者になります。昇格後はユーザーシステムの利用ができなくなります。
                    @else
                    勤怠管理システム側のユーザー登録がされていません。
                    @endif
                </small></span>
            </div>
        </div>
    </div>
    <p class="text-right"><button type="submit" class="btn btn-warning">更新する</button></p>
</form>