
<div class="col-md-6">
    <div class="panel panel-primary">
        <div class="panel-heading">
            所属部署
            @if(empty($user->SinrenUser)) <span class="label label-warning">未登録</span>
            @else                         <span class="label label-success">登録済</span> @endif
        </div>
        <div class="panel-body">
            <div class="col-md-12">
                <small><label>部署</label></small>
                @if(empty($user->SinrenUser))
                <p>
                    部署が
                    <a href="{{ route('app::roster::user::show', ['id'=>$user->id]) }}">登録</a>
                    されていません。
                </p>
                @else
                @foreach($divs as $div)
                <p>
                    @if($user->SinrenUser && $user->SinrenUser->division_id == $div->division_id) 
                    {{ $div->division_name }}
                    @endif
                </p>
                @endforeach
                <small class="text-warning">部署を変更する場合、総務課に連絡してください。</small>
                @endif
            </div>
        </div>
    </div>
</div>


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
                    <input type="text" name="last_name"      class="form-control" value="{{$user->last_name}}"      placeholder="山田" style="margin-bottom: 10px;">
                </div>
                <div class="col-md-6">
                    <small><label>名</label></small>
                    <input type="text" name="first_name"      class="form-control" value="{{$user->first_name}}"      placeholder="太郎" style="margin-bottom: 10px;">
                </div>
                <div class="col-md-6">
                    <input type="text" name="last_name_kana" class="form-control" value="{{$user->last_name_kana}}" placeholder="やまだ">
                </div>
                <div class="col-md-6">
                    <input type="text" name="first_name_kana" class="form-control" value="{{$user->first_name_kana}}" placeholder="たろう">
                </div>

                <div class="col-md-12">
                    <div class="text-right" style="margin-top: 10px;">
                        <button type="submit"　name="btn_name" id="btn_name" class="btn-primary btn-sm btn">変更する</button>
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
                            <button type="submit" name= "btn_icon" id= "btn_icon" class="btn btn-primary btn-sm" onclick="return checkFile();">アイコン変更</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>