
<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#update-{{ $data->id }}">更新</button>

<!-- モーダル・ダイアログ -->
<div class="modal fade text-left" id="update-{{ $data->id }}" tabindex="-1">
  <form action="/notifications/{{ $data->id }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="PUT">

    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          <h4 class="modal-title">お知らせ更新</h4>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <label for="category">カテゴリー</label>
            <select class="form-control" name="category">
              @foreach($categories as $key => $c)
              <option value="{{ $key }}" @if($c == $data->category) selected @endif>{{ $c }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="message">メッセージ</label>
            <textarea rows="3" class="form-control" name="message">{{ $data->message }}</textarea>
          </div>


          <div class="form-group">
            <label for="deadline">公開期限</label>
            {{ $data->deadline }}
            <div class='input-group date' id='deadline_{{ $data->id }}'>
              <input type='text' class="form-control" name="deadline" value="{{ $data->deadline }}">
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>
          </div>
          <script type="text/javascript">
            $(function () {
              $('#deadline_{{ $data->id }}').datetimepicker({
                locale: 'ja',
                format: 'YYYY-MM-DD',
              });
            });
          </script>



        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
          <button type="submit" class="btn btn-success">更新</button>
        </div>
      </div>
    </div>
  </form>
</div>