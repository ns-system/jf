
<!-- モーダル・ダイアログ -->

<form method="POST" action="{{route('app::roster::chief::update')}}" class="form-horizontal">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="id" value="{{$r->key_id}}">
    <input type="hidden" name="user_id" value="{{$r->user_id}}">

    <div class="modal fade" id="form_{{$r->key_id}}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary-important">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title text-left">ユーザー区分選択</h4>
                </div>

                <div class="modal-body text-left">
                    <div class="col-md-10 col-md-offset-1">
                        <label>責任者代理</label>
                        <div class="form-group">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default btn-sm" data-color="btn-primary" style="min-width: 150px;">
                                    <input type="radio" name="proxy" value="0" @if(!$r->is_proxy) checked @endif>一般ユーザーにする
                                </label>
                                <label class="btn btn-default btn-sm" data-color="btn-success"" style="min-width: 150px;">
                                    <input type="radio" name="proxy" value="1" @if($r->is_proxy)  checked @endif>責任者代理にする
                                </label>
                            </div>
                        </div>

                        <label>代理人機能</label>
                        <div class="form-group">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default btn-sm" data-color="btn-primary" style="min-width: 150px;">
                                    <input type="radio" name="active" value="0" @if(!$r->is_proxy_active) checked @endif>無効化する
                                </label>
                                <label class="btn btn-default btn-sm" data-color="btn-success" style="min-width: 150px;" data-toggle="tooltip" data-placement="right" title="代理人機能を有効にする場合、責任者代理を設定してください。">
                                    <input type="radio" name="active" value="1" @if($r->is_proxy_active)  checked @endif>有効化する
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">閉じる</button>
                            <button type="submit" class="btn btn-success">更新する</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
