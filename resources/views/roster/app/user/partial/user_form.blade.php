
<div class="col-md-10 col-md-offset-1">
  <form class="form-horizontal" role="form" method="POST" action="{{route('app::roster::user::edit', ['$id'=>$id])}}">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @if($not_register || $is_admin)
    <p>
      <label>部署</label>
      <select class="form-control" name="division_id">
        @foreach($divs as $div)
        <option value="{{$div->division_id}}" @if($user->division_id == $div->division_id) selected @endif>{{$div->division_name}}</option>
        @endforeach
      </select>
    </p>
    @endif

    <p>
      <label>標準勤務形態</label>
      <select class="form-control" name="work_type_id">
        <option value="">勤務形態を選択してください（責任者の場合、選択不要です）</option>
        @foreach($types as $type)
        <option value="{{$type->work_type_id}}" @if($user->work_type_id == $type->work_type_id) selected @endif>
          {{$type->work_type_name}}@if($type->display_time) ({{$type->display_time}}) @endif
        </option>
        @endforeach
      </select>
    </p>

    <div>
      <div class="checkbox">
        <label class="text-success"><input type="checkbox" name="is_chief" value="1" @if($user->is_chief) checked @endif> <b>私は部署の責任者です</b></label>
      </div>
      <p class="text-warning"><small>責任者として登録した後は担当部署に連絡をお願いします。</small></p>
    </div>

    @if($is_admin)
    <div class="checkbox">
      <label class="text-danger">
        <input type="checkbox" name="retirement" value="1" @if($user->retirement) checked @endif>
        <b>このユーザーは退職済みです</b>
      </label>
    </div>
    <p class="text-warning"><small>退職済みにすると一般ユーザーからは一切の情報が見えなくなります。責任者の場合、承認できなくなります。</small></p>

    <div class="checkbox">
      <label class="text-warning">
        <input type="checkbox" name="hidden" value="1" @if($user->roster_hidden) checked @endif>
        <b>勤怠管理を行わない</b>
      </label>
    </div>
    <p class="text-warning"><small>チェックを入れると勤怠管理の予定データ作成画面に表示されなくなります。</small></p>
    @endif
    <p class="text-right"><button type="submit" class="btn btn-primary" name="submit">更新する</button></p>

  </form>
</div>