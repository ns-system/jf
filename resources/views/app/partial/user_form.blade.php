<form class="form-horizontal" role="form" method="POST" action="">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">


    <div class="well">
        <a href="#sinren" class="btn btn-info btn-sm" data-toggle="collapse"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            所属部署</a>
        @if($user->SinrenUser)
        <span class="label label-success">登録済</span>
        @else
        <span class="label label-warning">未登録</span>
        @endif

        <div class="form-group collapse" id="sinren">
            <div class="col-md-10 col-md-offset-1">
                <label for="InputSelect">所属部署</label>
                <div class="input-group">
                    <select class="form-control" name="division_id">
                        @foreach($divs as $div)
                        <option
                            value="{{$div->division_id}}"
                            @if($user->SinrenUser && $user->SinrenUser->division_id == $div->division_id)
                            selected="selected"
                            @endif>{{$div->division_name}}</option>
                        @endforeach
                    </select>
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary" formaction="{{route('app::user::division', ['id'=>$user->id])}}">部署変更</button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(\Auth::user()->id == $user->id)
    <div class="well">
        <a href="#name" class="btn btn-info btn-sm" data-toggle="collapse"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            ユーザー名</a>
        <div class="collapse" id="name">
            <div class="form-group">
                <div class="col-md-10 col-md-offset-1">

                    <label for="InputEmail">ユーザー名</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" value="{{$user->name}}">
                        <span class="input-group-addon" style="border-left: none;">さん</span>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary" formaction="{{route('app::user::name', ['id'=>$user->id])}}">名前変更</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="well">
        <a href="#password" class="btn btn-info btn-sm" data-toggle="collapse"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            パスワード</a>
        <div class="collapse" id="password">
            <div class="form-group">
                <div class="col-md-10 col-md-offset-1">
                    <label for="password">現在のパスワード</label>
                    <input type="password" class="form-control" name="password" placeholder="現在利用しているパスワードを入力してください。">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-10 col-md-offset-1">
                    <label for="new_password">新しいパスワード</label>
                    <input type="password" class="form-control" name="new_password" placeholder="新しいパスワードを入力してください。">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-10 col-md-offset-1">
                    <label for="new_password_confirmation">新しいパスワード（確認用）</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="new_password_confirmation" placeholder="再度、新しいパスワードを入力してください。">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary" formaction="{{route('app::user::password', ['id'=>$user->id])}}">パスワード変更</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</form>


<form class="form-horizontal" role="form" method="POST" action="{{route('app::user::icon', ['id'=>$user->id])}}" enctype="multipart/form-data">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="well">
        <a href="#user_icon" class="btn btn-info btn-sm" data-toggle="collapse"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            ユーザーアイコン</a>
        <div class="collapse" id="user_icon">
            <div class="form-group">
                <div class="col-md-10 col-md-offset-1">

                    <p id="old-img"><img style="max-height: 200px;" @if(\Auth::user()->user_icon != '') src="{{asset('/user_icon/' . \Auth::user()->user_icon)}}" @else src="{{asset('/user_icon/unset.png')}}" @endif></p>
                    <p id="img"></p>
                    <div class="btn-group">
                        <label class="btn btn-info">アイコンを選択
                            <input type="file" id="file" class="form-control" name="user_icon" style="display: none;">
                        </label>
                        <button type="submit" class="btn btn-primary" onclick="return checkFile();">アイコン変更</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>