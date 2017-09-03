@extends('layout')

@section('title', '勤怠管理')

@section('header')
@parent
@section('brand', '勤怠管理システム')

<style type="text/css">
    .calendar th,
    .calendar td{
        border: none;
    }
    .small{ font-weight: bolder; }
</style>
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('app.sidebar.sidebar')
</div>
<div class="col-md-10">
    <h2 style="margin: 0;">
        <nav style="display: inline-block;">
            <ul class="pager" style="margin: 0; text-align: left;">
                <li style=" font-size: 16px;">
                    <a href="{{(route('app::roster::calendar::show', ['ym' => $prev]))}}">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> {{date('Y年n月', strtotime($prev.'01'))}}
                    </a>
                </li>
		        <span><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> {{date('Y年n月', strtotime($ym.'01'))}} 勤怠管理カレンダー</span>
                <li style=" font-size: 16px;">
                    <a href="{{(route('app::roster::calendar::show', ['ym' => $next]))}}">
                        {{date('Y年n月', strtotime($next.'01'))}} <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    </a>
                </li>
            </ul>
        </nav>
    </h2>
</div>
@endsection

@section('content')
<div class="col-md-12">
    <div class="container-fluid">
        @include('partial.alert')

        <table class="calendar">
            <thead>
                <tr>
                    <th width="14.286%" class="text-danger">日</th>
                    <th width="14.286%">月</th>
                    <th width="14.286%">火</th>
                    <th width="14.286%">水</th>
                    <th width="14.286%">木</th>
                    <th width="14.286%">金</th>
                    <th width="14.286%" class="text-info">土</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calendars as $i => $day)
                @if($day['week'] == 0)
                <tr>
                    @endif
                    <td>
                        @if($day['day'] != 0)
                        @include('roster.app.calendar.partial.day')
                        @endif
                    </td>
                    @if($day['week'] == 6)
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection

@section('footer')
@parent
<script type="text/javascript">
    $(function () {
        $('#fix').click(function () {
            $('.fix-or-wide').each(function () {
                $(this).css('min-width', 'initial');
            });
        });
        $('#wide').click(function () {
            $('.fix-or-wide').each(function () {
                $(this).css('min-width', '250px');
            });
        });
    })
</script>
@endsection