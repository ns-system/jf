<form method="GET" action="{{route('admin::roster::csv::search', ['ym'=>$ym])}}">

<div class="text-right" data-spy="affix" data-offset-top="115" style="top: 95px; right: 15px; z-index: 1;">
    <div class="text-right" style="margin-bottom: 10px;">
        {!! $rosters->appends($search)->render() !!}
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#search">条件から検索</button>
        <button type="submit" class="btn btn-info btn-sm"    formaction="{{route('admin::roster::csv::export', ['ym'=>$ym, 'type'=>'plan'])}}">予定出力</button>
        <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('admin::roster::csv::export', ['ym'=>$ym, 'type'=>'actual'])}}">実績出力</button>
        <button type="submit" class="btn btn-warning btn-sm">生データを出力</button>
    </div>
</div>
<div style="margin: 10px;"></div>


<!-- モーダル・ダイアログ -->
<div class="modal fade" id="search" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary-important">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <h4 class="modal-title">検索条件</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-10 col-md-offset-1">
{{-- form --}}


<div class="col-md-6" style="padding: 0">
    <label><small>予定</small></label>
    <div class="form-group">
        <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-default btn-xs" data-color="btn-primary"><input type="radio" name="plan" value="0" @if(empty($search['plan'])) checked @endif                         >全て</label>
            <label class="btn btn-default btn-xs" data-color="btn-warning"><input type="radio" name="plan" value="1" @if(!empty($search['plan']) && $search['plan'] == 1) checked @endif>未承認</label>
            <label class="btn btn-default btn-xs" data-color="btn-success"><input type="radio" name="plan" value="2" @if(!empty($search['plan']) && $search['plan'] == 2) checked @endif>承認済み</label>
            <label class="btn btn-default btn-xs" data-color="btn-danger" ><input type="radio" name="plan" value="3" @if(!empty($search['plan']) && $search['plan'] == 3) checked @endif>却下</label>
        </div>
    </div>
</div>

<div class="col-md-6" style="padding: 0">
    <label><small>実績</small></label>
    <div class="form-group">
        <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-default btn-xs" data-color="btn-primary"><input type="radio" name="actual" value="0" @if(empty($search['actual'])) checked @endif                           >全て</label>
            <label class="btn btn-default btn-xs" data-color="btn-warning"><input type="radio" name="actual" value="1" @if(!empty($search['actual']) && $search['actual'] == 1) checked @endif>未承認</label>
            <label class="btn btn-default btn-xs" data-color="btn-success"><input type="radio" name="actual" value="2" @if(!empty($search['actual']) && $search['actual'] == 2) checked @endif>承認済み</label>
            <label class="btn btn-default btn-xs" data-color="btn-danger" ><input type="radio" name="actual" value="3" @if(!empty($search['actual']) && $search['actual'] == 3) checked @endif>却下</label>
        </div>
    </div>
</div>

<label><small>ユーザー名（一部）</small></label>
<div class="form-group">
    <input type="text" class="form-control input-sm" name="name" @if(!empty($search['name'])) value="{{$search['name']}}" @endif>
</div>

<label><small>部署</small></label>
<div class="form-group">
    <select class="form-control input-sm" name="division">
        <option value="">指定しない</option>
        @foreach($divs as $d)
            <option value="{{$d->division_id}}" @if(!empty($serach['division']) && $d->division_id == $serach['division']) selected @endif>{{$d->division_name}}</option>
        @endforeach
    </select>
</div>

<label><small>日付</small></label>
<div class="form-group">
    <input type="date" class="form-control input-sm" name="date" @if(!empty($search['date'])) value="{{$search['date']}}" @endif>
</div>
{{-- form --}}
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-10 col-md-offset-1">
                <div class="btn-group">
                    <a      class="btn btn-warning" style="min-width: 150px" href="{{route('admin::roster::csv::show', ['ym'=>$ym])}}">検索条件クリア</a>
                    <button class="btn btn-primary" style="min-width: 150px" type="submit">検索</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>