@extends('layout')

@section('title', 'ニコカレ！')
@section('meta')
@parent
<link rel="shortcut icon" type="image/x-icon" href="{{asset('/nikocale_icon/favicon.ico')}}" />
@endsection

@section('header')
@parent
@section('brand', 'ニコカレ！')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('app.sidebar.sidebar')
</div>
@endsection


@section('content')
<div class="col-md-10">
    @include('partial.alert')

    <h2>{{$division->division_name}} - ニコカレ！</h2>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h1 class="panel-title" style="font-size: 1.8em;">
                <?php $date = date('Y-m-d', strtotime($monthly_id . '01')); ?>
                <a href="{{route('app::nikocale::index', ['monthly_id'=>date('Ym', strtotime($date.'-1 month'))])}}"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>
                <span style="font-size: 1.8em;">{{date('Y年n月',strtotime($monthly_id.'01'))}}</span>
                <a href="{{route('app::nikocale::index', ['monthly_id'=>date('Ym', strtotime($date.'+1 month'))])}}"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a>
            </h1>
        </div>
        <div class="panel-body scroll-Y" style="width: 100%; overflow-x: scroll; padding: 10px 20px;">
            <table class="table va-middle table-hover table-bordered" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th width="150"><small>ユーザー名</small></th>
                        @foreach($calendar as $c)
                        <th width="80"><small>{{date('n/j', strtotime($c['date']))}}</small></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($user_ids as $id)
                    <tr>
                        <th>{{\App\User::find($id)->first_name}}<small>さん</small></th>
                        @foreach($calendar as $c)
                        <td width="80"
                            height="80" 
                            style="position: relative; padding: 0;" 
                            @if($c['week'] == 0 || $c['holiday']) class="bg-danger"
                            @elseif($c['week'] == 6)              class="bg-info" @endif
                        >
                            @if(!empty($emotions[$id][$c['date']]->comment))<span style="position: absolute; left: 5px; top: 5px; width: 7px; height: 7px; border-radius: 50%; z-index: 1; display: block; padding: 0;" class="label label-success"></span> @endif
                            @if($id == \Auth::user()->id)
                            <button
                                data-toggle="modal" data-target="#{{$c['date']}}_{{$id}}"
                                class="btn btn-default" style="padding: 10px; margin: 0; width: 100%; background: none; border: none; position: absolute; top: 0; left: 0; height: 100%;"
                            >
                                @if(isset($emotions[$id][$c['date']])) <img src="{{asset('/nikocale_icon/emotion_'.$emotions[$id][$c['date']]->emotion.'.png')}}" style="width: 100%;"> @endif
                            </button>
                            @else
                            <span
                                style="padding: 10px; margin: 0; width: 100%; background: none; border: none; display: block;"
                                @if(!empty($emotions[$id][$c['date']]->comment))
                                data-toggle="tooltip"
                                title="{{$emotions[$id][$c['date']]->comment}}"
                                data-placement="top"
                                @endif
                            >
                                @if(isset($emotions[$id][$c['date']])) <img src="{{asset('/nikocale_icon/emotion_'.$emotions[$id][$c['date']]->emotion.'.png')}}" style="width: 100%;"> @endif
                            </span>
                            @endif

                            @if($id == \Auth::user()->id)
                            <form method="POST" class="text-left" action="{{route('app::nikocale::store',  ['user_id'=>$id,'entered_on'=>$c['date']])}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <!-- モーダル・ダイアログ -->
                                <div class="modal fade" id="{{$c['date']}}_{{$id}}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary-important">
                                                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                                                <h4 class="modal-title text-left">{{date('n月j日', strtotime($c['date']))}}<small>（{{$c['week_name']}}）</small></h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="col-md-10 col-md-offset-1 text-left">
                                                    <small class="text-left"><label>今の気分を正直に選んでくださいね。</label></small>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="radio">
                                                                <label class="va-middle">
                                                                    <input type="radio" name="emotion" value="1" @if(isset($emotions[$id][$c['date']]->emotion) && $emotions[$id][$c['date']]->emotion == 1) checked @endif>
                                                                           <img src="{{asset('/nikocale_icon/emotion_1.png')}}" style="width: 40px;">いい感じ！
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="radio">
                                                                <label class="va-middle">
                                                                    <input type="radio" name="emotion" value="2" @if(isset($emotions[$id][$c['date']]->emotion) && $emotions[$id][$c['date']]->emotion == 2) checked @endif>
                                                                           <img src="{{asset('/nikocale_icon/emotion_2.png')}}" style="width: 40px;">まぁまぁ？
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="radio">
                                                                <label class="va-middle">
                                                                    <input type="radio" name="emotion" value="3"  @if(isset($emotions[$id][$c['date']]->emotion) && $emotions[$id][$c['date']]->emotion == 3) checked @endif>
                                                                           <img src="{{asset('/nikocale_icon/emotion_3.png')}}" style="width: 40px;">やな感じ
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <small><label>何か伝えたい事があれば自由に書いてくださいね。</label></small>
                                                    <div class="form-group">
                                                        <textarea class="form-control" rows="5" name="comment">@if(isset($emotions[$id][$c['date']]->comment)){{$emotions[$id][$c['date']]->comment}}@endif</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-10 col-md-offset-1">
                                                    <div class="btn-group">

                                                        @if(isset($emotions[$id][$c['date']]))
                                                        <a id="destroy_{{$emotions[$id][$c['date']]->key_id}}_{{$id}}" href="{{route('app::nikocale::destroy', ['id'=>$emotions[$id][$c['date']]->key_id])}}" class="btn btn-sm btn-danger" onclick="本当に削除してもよろしいですか？">削除する</a>
                                                        @endif
                                                        <button
                                                            name="submit_{{$c['date']}}_{{$id}}"
                                                            type="submit" class="btn btn-success btn-sm"
{{--                                                             @if(isset($emotions[$id][$c['date']])) formaction="{{route('app::nikocale::update', ['id'=>$emotions[$id][$c['date']]->key_id])}}"
                                                            @else                                  formaction="{{route('app::nikocale::store',  ['user_id'=>$id,'entered_on'=>$c['date']])}}" @endif --}}
                                                        >更新する</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- modal end -->
                            </form>
                            @endif

                        </td>
                        @endforeach
                        <td> 　<td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-md-offset-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h5 class="panel-title">トータル</h5>
                </div>
                <div class="panel-body">
                    <table class="table table-small table-hover va-middle">
                        <tr>
                            <th></th>
                            <th>あなた</th>
                            <th>部署</th>
                            <th>合計</th>
                        </tr>
                        <tr>
                            <th><img src="{{asset('/nikocale_icon/emotion_1.png')}}" style="width: 60px;"></th>
                            <th>{{$my_count[1]}}</th>
                            <th>{{$other_count[1]}}</th>
                            <th>{{$my_count[1] + $other_count[1]}}</th>
                        </tr>
                        <tr>
                            <th><img src="{{asset('/nikocale_icon/emotion_2.png')}}" style="width: 60px;"></th>
                            <th>{{$my_count[2]}}</th>
                            <th>{{$other_count[2]}}</th>
                            <th>{{$my_count[2] + $other_count[2]}}</th>
                        </tr>
                        <tr>
                            <th><img src="{{asset('/nikocale_icon/emotion_3.png')}}" style="width: 60px;"></th>
                            <th>{{$my_count[3]}}</th>
                            <th>{{$other_count[3]}}</th>
                            <th>{{$my_count[3] + $other_count[3]}}</th>
                        </tr>
                        <tr>
                            <th>合計</th>
                            <th>{{$my_count[1] + $my_count[2] + $my_count[3]}}</th>
                            <th>{{$other_count[1] + $other_count[2] + $other_count[3]}}</th>
                            <th>{{$my_count[1] + $my_count[2] + $my_count[3] + $other_count[1] + $other_count[2] + $other_count[3]}}</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
    $(function () {
        /**
         * [mousewheelevent description]
         * @
         */
        var mousewheelevent = 'onwheel' in document ? 'wheel' : 'onmousewheel' in document ? 'mousewheel' : 'DOMMouseScroll';
        $('.scroll-Y').on(mousewheelevent, function (e) {
            e.preventDefault();
            var dir = e.originalEvent.deltaY ? -(e.originalEvent.deltaY) : e.originalEvent.wheelDelta ? e.originalEvent.wheelDelta : -(e.originalEvent.detail);
            var deltaY = 1;
            if (dir < 0)
                deltaY = -1;
            var pos = $(this).scrollLeft();
            $(this).stop().animate({scrollLeft: (pos + deltaY * -700)}, 300, 'swing');

        });
    });
</script>
<script type="text/javascript" src="{{asset('/js/mouse_scroll.js')}}"></script>
@endsection