<form method="POST" action="{{route('admin::roster::user::edit')}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="id" value="{{$id}}">

    <div class="col-md-10 col-md-offset-1">
        <label>責任者</label>
        <div class="form-group">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-default btn-sm active" data-color="btn-default" style="min-width: 175px;">
                    <input type="radio" name="is_chief" value="0" @if(empty($user) || !$user->is_chief) checked @endif> 責任者ではありません
                </label>
                <label class="btn btn-default btn-sm"        data-color="btn-danger"  style="min-width: 175px;" data-toggle="tooltip" title="責任者は勤務予定の作成・承認・代理人の選任が行えます。必ず管轄部署も選択してください。">
                    <input type="radio" name="is_chief" value="1" @if(!empty($user) && $user->is_chief) checked @endif> 責任者です
                    </label>
            </div>
        </div>


        <label>責任者代理</label>
        <div class="form-group">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-default btn-sm active" data-color="btn-default" style="min-width: 175px;">
                    <input type="radio" name="is_proxy" value="0"  @if(empty($user) || !$user->is_proxy) checked @endif> 責任者代理ではありません
                </label>
                <label class="btn btn-default btn-sm"        data-color="btn-success" style="min-width: 175px;"  data-toggle="tooltip" title="責任者代理は承認が行えます。必ず管轄部署も選択してください。">
                    <input type="radio" name="is_proxy" value="1"  @if(!empty($user) && $user->is_proxy) checked @endif> 責任者代理です
                </label>
            </div>
        </div>

        <label>責任者代理機能を有効にする</label>
        <div class="form-group">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-default btn-sm active" data-color="btn-default" style="min-width: 175px;">
                    <input type="radio" name="is_proxy_active" value="0" @if(empty($user) || !$user->is_proxy_active) checked @endif> 無効にする
                </label>
                <label class="btn btn-default btn-sm"        data-color="btn-success" style="min-width: 175px;" data-toggle="tooltip" title="代理人機能を有効にする場合は「有効」を選択してください。">
                    <input type="radio" name="is_proxy_active" value="1" @if(!empty($user) && $user->is_proxy_active) checked @endif > 有効にする
                </label>
            </div>
        </div>

        <label>
            管轄部署
            <a data-target="#division" class="btn btn-success btn-sm" id="add-division" data-toggle="tooltip" title="複数部署を追加する場合、クリックしてください。">部署の追加</a>
        </label>
        @foreach($controls as $i => $c)
        <div class="form-group">
            <div class="input-group">
                <select class="form-control input-sm" name="control_division[]">
                    <option value="0">選択してください</option>
                    @foreach($divs as $div)
                    <option value="{{$div->division_id}}" @if($div->division_id == $c->division_id) selected @endif>{{$div->division_name}}</option>
                    @endforeach
                </select>
                <div class="input-group-btn">
                    <a
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('この部署を削除してもよろしいですか？');"
                        href="{{route('admin::roster::user::delete', ['$id'=>$c->id])}}"
                        data-toggle="tooltip"
                        title="管轄部署を削除する場合、押してください。なお、責任者もしくは責任者代理から外れた場合、管轄部署は全て削除されます。">削除</a>
                </div>
            </div>
        </div>
        @endforeach


        <div id="division">
        <div class="form-group">
            <select class="form-control input-sm" name="control_division[]">
                <option value="0">選択してください</option>
                @foreach($divs as $div)
                <option value="{{$div->division_id}}">{{$div->division_name}}</option>
                @endforeach
            </select>
        </div>
        </div>

        <div id="insert-division"></div>

        <div class="form-group text-right"><button type="submit" class="btn btn-warning">更新する</button></div>
    </div>

</form>