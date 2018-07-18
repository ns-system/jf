@extends('layout')

@section('title', 'ユーザー権限')

@section('header')
@parent
@section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
<div class="col-md-2">
  @include('partial.check_sidebar')

</div>
@endsection



@section('content')
<div class="col-md-10">

  @include('partial.alert')

  <div class="border-bottom" id="top">
    <h2>
      勤務データ承認 <small> - {{date('Y年n月', strtotime($ym.'01'))}}</small>
    </h2>
  </div>

  <!-- タブメニュー -->
  <div style="margin-bottom: 20px;">
    <ul class="nav nav-tabs">
      <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown"><span id="show-name">　</span> <span class="caret"></span></a>

        <ul class="dropdown-menu" role="menu">
          <li><a href="{{route_with_query('app::roster::accept::calendar', ['ym'=>$ym, 'div'=>$div])}}">未承認のみ</a></li>
          @foreach($users as $i => $u)
          <li data-name="{{ $u->last_name }} {{ $u->first_name }}" @if(!empty($user_id) && ($u->user_id == $user_id)) class="active" @endif>
            <a href="{{route_with_query('app::roster::accept::calendar', ['ym'=>$ym, 'div'=>$div, 'user'=>$u->user_id], ['status' => $status])}}">
              @include('partial.avatar', ['avatar' => $u->user_icon, 'size' => '40px',])
              {{$u->last_name}} {{ $u->first_name }} <small>さん</small>
            </a>
          </li>
          @endforeach
        </ul>
      </li>
    </ul>
  </div>




  <!-- タブ内容 -->
  <div class="tab-content">
    @if(empty($user_id))
    @include('roster.app.accept.partial.all_list')
    @else
    @include('roster.app.accept.partial.calendar_list')
    @endif
  </div>
</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
  $(function(){

    $('.activate').click(function(){
      var trg = $(this).attr('data-target');
      $(trg).show();
    });
    $('.inactivate').click(function(){
      var trg = $(this).attr('data-target');
      $(trg).find('input[type="checkbox"]').each(function(){
        $(this).prop('checked', false);
        var color = $(this).parent('.btn').attr('data-color');
        $(this).parent('.btn').removeClass(color+' btn-default').addClass('btn-default');
      });
      $(trg).find('input[type="text"]').each(function(){
        $(this).val('');
      });
      $(trg).hide();
    });

    var select = $('.dropdown .active').attr('data-name')
    console.log(select)
    var name   = (!select) ? '未承認のみ' : select + 'さん'
    $('#show-name').html(name)
  });
</script>
@endsection