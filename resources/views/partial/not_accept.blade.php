<?php
$week = ['日','月','火','水','木','金','土',];
?>

<table class="table small table-sortable">
  <thead>
    <tr>
      <th>#</th>
      <th>部署</th>
      <th>ユーザー名</th>
      <th>日付</th>
      <th>予定</th>
      <th>実績</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    @foreach($not_accepts as $key => $n)
    <tr>
      <th class="bg-{{ $n->week_color }}">{{ $key + 1 }}</th>
      <td class="bg-{{ $n->week_color }}">{{ $n->division_name }}</td>
      <td class="bg-{{ $n->week_color }}">{{ $n->last_name }} {{ $n->first_name }}さん</td>
      <td class="bg-{{ $n->week_color }} text-{{ $n->week_color }}">{{ $n->entered_on }}（{{ $week[$n->week] }}）</td>
      <td class="bg-{{ $n->week_color }}">
        <span class="label @if($n->plan === '却下') label-danger @elseif($n->plan === '承認済み') label-success @elseif($n->plan === '未承認') label-warning @else label-default @endif">{{ $n->plan }}</span>
      </td>
      <td class="bg-{{ $n->week_color }}">
        <span class="label @if($n->actual === '却下') label-danger @elseif($n->actual === '承認済み') label-success @elseif($n->actual === '未承認') label-warning @else label-default @endif">{{ $n->actual }}</span>
      </td>
      <td class="bg-{{ $n->week_color }}">
        @if($is_chief)
        <a href="{{ route('app::roster::accept::calendar', ['ym'=>$n->month_id, 'div'=>$n->division_id]) }}">承認する</a>
        @elseif($is_admin)
        <a href="#" onclick="sendMail({{ $n->id }})">入力を督促する</a>
        <p class="msg" id="msg-{{ $n->id }}"></p>
        @else
        <a href="{{ route('app::roster::calendar::show', ['ym'=>$n->month_id]) }}">入力する</a>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<style type="text/css">
.msg { display: none; }
</style>
<script type="text/javascript">
  function sendMail (roster_id) {
    $('#msg-' + roster_id).html('').hide()
    let params = {
      url  : '/send-roster-mail/' + roster_id,
      type : 'GET',
    }
    $.ajax(params)
    .done((res) => {
      console.log(res)
      $('#msg-' + roster_id).html(res.message).removeClass('text-danger').addClass('text-success').show()
    })
    .fail((e) => {
      let msg = (e && e.responseJSON && e.responseJSON[0]) ? e.responseJSON[0] : ''
      console.log(e, msg)
      $('#msg-' + roster_id).html(msg).removeClass('text-success').addClass('text-danger').show()
    })
    $(document).ready(function () {
      $('#not-accept-table').DataTable()
    })
  }
</script>