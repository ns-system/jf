<div class="text-right">
    <form class="form-inline input-sm" role="form" method="GET" action="">
        <div class="form-group">
            <input
            type="text"
            name="last_name"
            class="form-control input-sm"
            value="@if(!empty($params['last_name'])){{$params['last_name']}}@endif"
            placeholder="ユーザー名（姓）"
            data-toggle="tooltip"
            title="検索したい文字を姓に含むユーザーを検索します。"
            >
        </div>

        <div class="form-group">
            <select
            class="form-control input-sm"
            name="user_state"
            data-toggle="tooltip"
            title="ユーザー区分から絞り込んで検索します。">
                <option value="">全て</option>
                <option value="1" @if(isset($params['user_state']) && $params['user_state'] == '1') selected @endif>一般ユーザー</option>
                <option value="2" @if(isset($params['user_state']) && $params['user_state'] == '2') selected @endif>責任者</option>
                <option value="3" @if(isset($params['user_state']) && $params['user_state'] == '3') selected @endif>責任者代理</option>
                <option value="4" @if(isset($params['user_state']) && $params['user_state'] == '4') selected @endif>未登録</option>
            </select>
        </div>

        <div class="form-group">
            <select
            class="form-control input-sm"
            name="division_id"
            data-toggle="tooltip"
            title="所属部署から絞り込んで検索します。">
                <option value="" selected>全て</option>
                @foreach($divs as $div)
                <option value="{{$div->division_id}}" @if(isset($params['division_id']) && $params['division_id'] == $div->division_id) selected @endif>{{$div->division_name}}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success btn-sm">検索する</button>
    </form>
</div>