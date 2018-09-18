<h2 class="border-bottom">お知らせ作成</h2>
<p style="margin-bottom: 20px;">
<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#sampleModal">新規作成</button>
</p>
<!-- モーダル・ダイアログ -->
<div class="modal fade" id="sampleModal" tabindex="-1">
  <form method="POST">
    {{ csrf_field() }}

    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          <h4 class="modal-title">お知らせ作成</h4>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <label for="category">カテゴリー</label>
            <select class="form-control" name="category" id="category">
              @foreach($categories as $key => $c)
              <option value="{{ $key }}">{{ $c }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="message">メッセージ</label>
            <textarea rows="3" class="form-control" id="message" name="message"></textarea>
          </div>


          <div class="form-group">
            <label for="deadline">公開期限</label>
            <div class='input-group date' id='deadline_create'>
              <input type='text' class="form-control" name="deadline">
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>
          </div>
          <script type="text/javascript">
            $(function () {
              $('#deadline_create').datetimepicker({
                locale: 'ja',
                format: 'YYYY-MM-DD',
              });
            });
          </script>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
          <button type="submit" class="btn btn-primary">作成</button>
        </div>
      </div>
    </div>
  </form>
</div>
