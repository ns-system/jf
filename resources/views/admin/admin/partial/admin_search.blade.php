<form class="form-inline" role="form" method="GET" action="{{route('admin::super::user::search')}}">
    <div class="form-group"><input type="text" name="name" class="form-control input-sm" value="{{$name or ''}}" placeholder="ユーザー名"></div>

    <div class="form-group"><input type="text" name="mail" class="form-control input-sm" value="{{$mail or ''}}" placeholder="メールアドレス"></div>

    <div class="form-group">
        <select name="super" class="form-control input-sm">
            <option value="" @if($super === '') selected="selected" @endif>全て</option>
            <option value="0" @if($super === '0') selected="selected" @endif>一般ユーザー</option>
            <option value="1" @if($super === '1') selected="selected" @endif>スーパーユーザー</option>
        </select>
    </div>

    <div class="form-group">
        <select name="suisin" class="form-control input-sm">
            <option value="" @if($suisin === '') selected="selected" @endif>全て（推進）</option>
            <option value="0" @if($suisin === '0') selected="selected" @endif>一般</option>
            <option value="1" @if($suisin === '1') selected="selected" @endif>管理</option>
        </select>
    </div>

    <div class="form-group">
        <select name="roster" class="form-control input-sm">
            <option value="" @if($roster === '') selected="selected" @endif>全て（勤怠）</option>
            <option value="0" @if($roster === '0') selected="selected" @endif>一般</option>
            <option value="1" @if($roster === '1') selected="selected" @endif>管理</option>
        </select>
    </div>

    <div class="form-group">
        <select name="div" class="form-control input-sm">
            <option value="">部署の指定をしない</option>
            @foreach($divs as $d)
            <option value="{{$d->division_id}}" @if(isset($div) && $d->division_id == $div) selected="selected" @endif>{{$d->division_name}}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-success btn-sm">検索する</button>
</form>