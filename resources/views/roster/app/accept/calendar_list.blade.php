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
            <li @if(empty($user_id)) class="active" @endif>
                <a href="{{route_with_query('app::roster::accept::calendar', ['ym'=>$ym, 'div'=>$div])}}">選択してください</a>
            </li>
            @foreach($users as $i => $u)
            <li @if(!empty($user_id) && ($u->user_id == $user_id)) class="active" @endif>
                <a href="{{route_with_query('app::roster::accept::calendar', ['ym'=>$ym, 'div'=>$div, 'user'=>$u->user_id], ['status' => $status])}}"><b>{{$u->last_name}}</b><small>さん</small></a>
            </li>
            @endforeach
        </ul>
    </div>




    <!-- タブ内容 -->
    <div class="tab-content">
        @if(empty($user_id))
        <p>ユーザーを選択してください。</p>
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
        // $('#plan').click(function(){
        //     $('tr').show();
        //     $('tr[data-plan="false"][data-actual="false"]').hide();
        // });
        // $('#actual').click(function(){
        //     $('tr').show();
        //     $('tr[data-actual="false"]').hide();
        // });
        // $('#reset').click(function(){
        //     $('tr').show();
        // });

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
    });
</script>
@endsection