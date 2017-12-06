
@if(!empty($serach_columns))
<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#searchForm">検索</button>
@endif

<!-- モーダル・ダイアログ -->
<form method="GET" action="{{$configs['index_route']}}">
    <div class="modal fade" id="searchForm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary-important">
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    <h4 class="modal-title">検索</h4>
                </div>
                <div class="modal-body">
                    @foreach($serach_columns as $key => $column)
                    <div class="row form-group">
                        <div class="col-md-4 col-md-offset-1 text-right"><label class="text-right"><small>{{$column['display']}}</small></label></div>
                        <div class="col-md-4">
                            <input type="text" name="{{$key}}" class="form-control input-sm" value="@if(isset($search_values[$key])){{$search_values[$key]}}@endif">
                        </div>
                        <div class="col-md-2 text-left">
                            <small><label>
                                @if($column['type'] === 'string') を含む
                                @else                             に等しい @endif
                            </label></small>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <a href="{{$configs['index_route']}}" class="btn btn-warning">クリア</a>
                        <button type="submit" class="btn btn-primary">検索する</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
