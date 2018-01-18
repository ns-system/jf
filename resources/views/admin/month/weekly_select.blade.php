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

@section('sidebar')
<div class="col-md-2">
    @include('partial.check_sidebar')
</div>
@endsection


@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        @include('partial.alert')
        <h2 class="border-bottom">日次処理 - 日付選択 <small> - {{$id}}</small></h2>
        @if(!empty($file_list))
        <div class="row">
            <div class="col-md-6">
                <form method="POST" action="{{route('admin::super::term::weekly_select', ['id'=>$id, 'job_id'=>$job_id])}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <h3 class="border-bottom">ファイル選択</h3>
                    <select size="12" class="form-control" id="files" name="files[]" multiple>
                        @foreach($file_list as $file)
                        <option value="{{$file}}">{{$file}}</option>
                        @endforeach
                    </select>
                    <p><button type="submit" class="btn btn-primary btn-block">選択する</button></p>
                </form>
            </div>
            <div class="col-md-6">
                <h3>　</h3>
                <div class="well">
                    <p>Ctrl+クリックで複数ファイルを選択することができます。</p>
                    <p>識別子が同じものを複数選択した場合、最新のファイルのみ処理されます。</p>
                </div>
            </div>

        </div>
        @else
        <div class="alert alert-warning" role="alert">ファイルが見つかりませんでした。</div>
        @endif
    </div>
</div>
@endsection

@section('footer')
@parent
@endsection