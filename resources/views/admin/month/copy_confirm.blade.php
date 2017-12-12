@extends('layout')
@section('title', '処理確認')

@section('header')
@parent
@section('brand', '月次処理')
@endsection

{{-- @section('sidebar')
<div class="col-md-2">
    @include('partial.check_sidebar')
</div>
@endsection --}}

<div style="margin-top: 100px;"></div>


@section('content')
<div class="col-md-10 col-md-offset-1">
    <div class="container-fluid">
        @include('partial.alert')
        @include('admin.month.partial.breadcrumbs')
        <div class="border-bottom"><h2>USBストレージ内 ファイルリスト<small> - 処理対象：{{$id}}</small></h2></div>

        <div class="text-right" data-spy="affix" style="top: 110px; right: 135px; margin-bottom: 10px;" data-offset-top="120">
            <div class="btn-group">
                <a href="{{route('admin::super::month::copy_dispatch', ['id'=>$id])}}" class="btn btn-success btn-sm" onclick="return confirm('処理を開始してもよろしいですか？');">処理開始</a>
            </div>
        </div>



        <!-- タブ・メニュー -->
        <ul class="nav nav-tabs" style="margin-bottom: 10px;">
            <li class="active"><a href="#tmp_lists" data-toggle="tab">新規発生分</a></li>
            <li><a href="#monthly_lists" data-toggle="tab">累積分</a></li>
        </ul>

        <!-- タブ内容 -->
        <div class="tab-content">
            <div class="tab-pane active" id="tmp_lists">
                @if(!empty($tmp_lists))
                <table class="table table-hover table-striped table-small va-middle">
                    <thead>
                        <tr class="bg-primary">
                            <th>No</th>
                            <th>サイクル</th>
                            <th>CSVファイル名</th>
                            <th>ファイルサイズ</th>
                            <th>ダウンロード日時</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tmp_lists as $i => $l)
                        <tr>
                            <th class="bg-primary">{{$i + 1}}</th>
                            <td>
                                <span @if($l['cycle'] == 'M') class="label label-success" @else class="label label-default" @endif>{{$l['cycle']}}</span>
                            </td>
                            <td>{{$l['csv_file_name']}}</td>
                            <td class="text-right">{{number_format($l['kb_size'])}} kB</td>
                            <td>
                                <p>{{date('Y-m-d', $l['file_create_time'])}}</p>
                                <p>{{date('G:i:s', $l['file_create_time'])}}</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="alert alert-warning" role="alert">対象ファイルはありませんでした。</div>
                @endif
            </div>


            <div class="tab-pane" id="monthly_lists">
                @if(!empty($monthly_lists))
                <table class="table table-hover table-striped table-small va-middle">
                    <thead>
                        <tr class="bg-primary">
                            <th>No</th>
                            <th>サイクル</th>
                            <th>CSVファイル名</th>
                            <th>ファイルサイズ</th>
                            <th>ダウンロード日時</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthly_lists as $i => $l)
                        <tr>
                            <th class="bg-primary">{{$i + 1}}</th>
                            <td>
                                <span @if($l['cycle'] == 'M') class="label label-success" @else class="label label-default" @endif>{{$l['cycle']}}</span>
                            </td>
                            <td>{{$l['csv_file_name']}}</td>
                            <td class="text-right">{{number_format($l['kb_size'])}} kB</td>
                            <td>
                                <p>{{date('Y-m-d', $l['file_create_time'])}}</p>
                                <p>{{date('G:i:s', $l['file_create_time'])}}</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="alert alert-warning" role="alert">対象ファイルはありませんでした。</div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection

@section('footer')
@parent
@endsection