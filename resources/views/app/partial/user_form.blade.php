<form class="form-horizontal" role="form" method="POST" action="{{route('app::user::division', ['id'=>$user->id])}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                所属部署
                @if(empty($user->SinrenUser)) <span class="label label-warning">未登録</span>
                @else                         <span class="label label-success">登録済</span> @endif
            </div>
            <div class="panel-body">
                <small><label>部署</label></small>
                <div class="col-md-12">
                    <select class="form-control" name="division_id">
                        @foreach($divs as $div)
                        <option
                        value="{{$div->division_id}}"
                        @if($user->SinrenUser && $user->SinrenUser->division_id == $div->division_id) selected="selected" @endif
                        >{{$div->division_name}}</option>
                        @endforeach
                    </select>
                    <div class="text-right" style="margin-top: 10px;">
                        <button type="submit" name="edit_division" class="btn-primary btn-sm btn">変更する</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" role="form" method="POST" action="{{route('app::user::name', ['id'=>$user->id])}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                名前
            </div>
            <div class="panel-body">
                <div class="col-md-6">
                    <small><label>姓</label></small>
                    <input type="text" name="first_name"      class="form-control" value="{{$user->first_name}}"      placeholder="山田" style="margin-bottom: 10px;">
                </div>
                <div class="col-md-6">
                    <small><label>名</label></small>
                    <input type="text" name="last_name"      class="form-control" value="{{$user->last_name}}"      placeholder="太郎" style="margin-bottom: 10px;">
                </div>
                <div class="col-md-6">
                    <input type="text" name="first_name_kana" class="form-control" value="{{$user->first_name_kana}}" placeholder="やまだ">
                </div>
                <div class="col-md-6">
                    <input type="text" name="last_name_kana" class="form-control" value="{{$user->last_name_kana}}" placeholder="たろう">
                </div>

                <div class="col-md-12">
                    <div class="text-right" style="margin-top: 10px;">
                        <button type="submit" class="btn-primary btn-sm btn">変更する</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" role="form" method="POST" action="{{route('app::user::password', ['id'=>$user->id])}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">パスワード</div>
            <div class="panel-body">
                <div class="col-md-12">
                    <small><label>現在のパスワード</label></small>
                    <input type="password" name="password"            class="form-control" style="margin-bottom: 10px;">

                    <small><label>新しいパスワード</label></small>
                    <input type="password" name="new_password"         class="form-control" style="margin-bottom: 10px;">

                    <small><label>新しいパスワード再入力</label></small>
                    <input type="password" name="new_password_confirmation" class="form-control">

                    <div class="text-right" style="margin-top: 10px;">
                        <button type="submit" class="btn-primary btn-sm btn">変更する</button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" role="form" method="POST" action="{{route('app::user::icon', ['id'=>$user->id])}}" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">アイコン</div>
            <div class="panel-body">
                <div class="col-md-12">
                    <small><label>ユーザーアイコン</label></small>
                    <p id="old-img" class="text-center" style="margin-top: 20px;">
                        <img style="max-height: 250px; border: 1px solid #fff; border-radius: 5px;"
                        @if(\Auth::user()->user_icon != '') src="{{asset('/user_icon/' . \Auth::user()->user_icon)}}"
                        @else src="{{asset('/user_icon/unset.png')}}" @endif
                        >
                    </p>
                    <p id="img" class="text-center"></p>

                    <div class="text-right" style="margin-top: 30px;">
                        <div class="btn-group">
                            <label class="btn btn-info btn-sm">
                                アイコンを選択<input type="file" id="file" class="form-control" name="user_icon" style="display: none;">
                            </label>
                            <button type="submit" class="btn btn-primary btn-sm" onclick="return checkFile();">アイコン変更</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>







{{-- 



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
</form> --}}