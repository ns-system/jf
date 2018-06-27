<!-- モーダル・ダイアログ -->
<style>
.min-width    { min-width: 75px; }
.pa tr td,
.pa tr th { padding: 2px 0; }
</style>
<div class="modal fade" id="{{ $key }}" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary-important">
        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
        <h4 class="modal-title">{{ date('n月j日', strtotime($key)) }}</h4>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-xs-10 col-xs-offset-1">
            <table class="table-small pa" style="margin-bottom: 0;">
              <thead>
                <tr class="bg-primary-important">
                  <th>ユーザー名</th>
                  <th>予定</th>
                  <th>実績</th>
                </tr>
              </thead>
              <tbody>
                @foreach($eusers as $e)
                <tr>
                  <td class="text-left">{{ $e->last_name }} {{ $e->first_name }} さん</td>
                  <td>
                    @if($e->is_plan_entry && $e->is_plan_accept)
                    <span class="label min-width label-success">承認済み</span>
                    @elseif($e->is_plan_entry && $e->is_plan_reject)
                    <span class="label min-width label-danger">却下</span>
                    @elseif($e->is_plan_entry)
                    <span class="label min-width label-warning">未承認</span>
                    @else
                    <span class="label min-width label-default">未入力</span>
                    @endif
                  </td>
                  <td>
                    @if($e->is_actual_entry && $e->is_actual_accept)
                    <span class="label min-width label-success">承認済み</span>
                    @elseif($e->is_actual_entry && $e->is_actual_reject)
                    <span class="label min-width label-danger">却下</span>
                    @elseif($e->is_actual_entry)
                    <span class="label min-width label-warning">未承認</span>
                    @else
                    <span class="label min-width label-default">未入力</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>




      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
      </div>
    </div>
  </div>
</div>