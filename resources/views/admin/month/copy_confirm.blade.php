@extends('layout')
@section('title', '処理確認')

@section('header')
@parent
@section('brand', '月次処理')
@endsection

{{-- @section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')
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
    @foreach($lists as $i => $l)
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


{{-- <form method="POST" action="{{route('admin::super::month::copy',['id'=>$id])}}">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}"> --}}


{{--     <div class="form-group">
        <input type="text" name="usb_path" class="form-control" value="F:/">
        <span id="helpBlock" class="help-block"><small class="text-warning">USBストレージのドライブ名を入力してください。</small></span>
    </div>
        <button type="submit">sumbmit</button>
</form> --}}
    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection