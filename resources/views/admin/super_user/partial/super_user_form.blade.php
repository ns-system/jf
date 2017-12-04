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

{{--     <div class="well">
                <div class="form-group">
                    <div class="col-md-10 col-md-offset-1">
                        <label for="InputSelect">推進支援システム ユーザー</label>
                        @if($user->SuisinUser)
                        <select class="form-control" name="suisin_is_administrator">
                            <option value="0">一般ユーザー</option>
                            <option value="1" @if($user->SuisinUser->is_administrator == true) selected="selected" @endif>管理ユーザー</option>
                        </select>
                        <span class="help-block bolder"><small class="text-warning">推進支援システムの管理ユーザーになります。昇格後はユーザーシステムの利用ができなくなります。</small></span>
                        @else
                        <div class="alert alert-warning" role="alert">推進支援システム側のユーザー登録がされていません。</div>
                        @endif
                    </div>
                </div>
        </div> --}}

        <div class="well">
            <div class="form-group">
                <div class="col-md-10 col-md-offset-1">
                    <label for="InputSelect">勤怠管理システム ユーザー</label>
                    @if($user->RosterUser($user->id))
                    <select class="form-control" name="roster_is_administrator">
                        <option value="0">一般ユーザー</option>
                        <option value="1" @if($user->RosterUser($user->id)->is_administrator == true) selected="selected" @endif>管理ユーザー</option>
                    </select>
                    <span class="help-block bolder"><small  class="text-warning">勤怠管理システムの管理者になります。昇格後はユーザーシステムの利用ができなくなります。</small></span>
                    @else
                    <div class="alert alert-warning" role="alert">勤怠管理システム側のユーザー登録がされていません。</div>
                    @endif
                </div>
            </div>
        </div>
        <p class="text-right"><button type="submit" class="btn btn-warning">更新する</button></p>
</form>