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
            予定データ承認 <small> - {{date('Y年n月', strtotime($ym.'01'))}}</small>
        </h2>
    </div>


    <form method="POST" action="{{route('app::roster::accept::calendar_accept')}}">

        {{-- 検索用ボタン --}}
        <div class="text-right" data-spy="affix" data-offset-top="85" style="z-index: 1;  top: 120px; right: 15px;">
            <div class="btn-group">
                <a href="{{route('app::roster::accept::index')}}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-backward" aria-hidden="true"></span> 戻る</a>
                <a href="#top" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span> トップへ</a>
                <a href="{{route('app::roster::accept::calendar', ['ym'=>$ym, 'div'=>$div, 'all'=>'part'])}}" class="btn btn-warning btn-sm" id="plan">未承認</a>
                <a href="{{route('app::roster::accept::calendar', ['ym'=>$ym, 'div'=>$div])}}" class="btn btn-success btn-sm" id="reset">全て</a>
                <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('チェックしたデータが一括で更新されますがよろしいですか？');"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> 一括で更新する</button>
            </div>
        </div>

        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <!-- タブメニュー -->
        <div style="margin-bottom: 20px;">
            <ul class="nav nav-tabs">
                @foreach($users as $i => $u)
                <li @if($i == 0) class="active" @endif><a href="#id-{{$u->user_id}}" data-toggle="tab"><b>{{$u->last_name}}</b><small>さん</small></a></li>
                @endforeach
            </ul>
        </div>

        <!-- タブ内容 -->
        <div class="tab-content">
            @foreach($users as $i => $u)
            <div id="id-{{$u->user_id}}" @if($i == 0) class="tab-pane active" @else class="tab-pane" @endif>
                @include('roster.app.accept.partial.calendar_list')
            </div>
            @endforeach
        </div>
    </form>
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