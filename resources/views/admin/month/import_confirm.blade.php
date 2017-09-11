@extends('layout')
@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('title', '月別マスタ')

@section('header')
@parent
@section('brand', '月別マスタ')
@endsection


@section('brand')
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
        <div class="border-bottom">
            <h2>CSVファイルコピー処理</h2>
        </div>

<table class="table table-hover table-striped table-small va-middle">
    <thead>
        <tr class="bg-primary">
            <th>区分</th>
            <th>処理対象</th>
            <th>還元データ名　CSVファイル名</th>
            <th>還元日</th>
            <th>CSVファイル　処理状態</th>
            <th>目安還元日</th>
            <th>累積　分割　口座変換</th>
        </tr>
    </thead>

    <tbody>
    @foreach($files as $f)
        <tr>
            <td>
                <input
                    type="checkbox"
                    style="width: 24px;height: 24px;vertical-align: middle; margin:0; margin-bottom: 5px;" disabled
                    @if($f->is_process) checked @endif
                >
            </td>
            <td class="va-middle">
                    @if($f->is_process) <label class="label label-success" style="min-width: 100px;">処理対象</label>
                    @else               <label class="label label-danger"  style="min-width: 100px;">処理対象外</label> @endif</p>
            </td>
            <td>
                <p>{{$f->zenon_data_name}}</p>
                <P>{{$f->csv_file_name}}</P>
            </td>
            <td class="va-middle">
                <p>{{$f->csv_file_set_on}}</p>
            </td>
            <td class="va-middle">
                <p>
                    @if($f->is_exist) <label class="label label-success" style="min-width: 100px;">ファイルあり</label>
                    @else             <label class="label label-warning" style="min-width: 100px;">ファイルなし</label> @endif
                </p>
                <p>
                    @if($f->is_import) <label class="label label-warning" style="min-width: 100px;">処理済み</label>
                    @else              <label class="label label-default" style="min-width: 100px;">未処理</label> @endif
                </p>
            </td>

            <td class="va-middle">{{$f->reference_return_date}}</td>

            <td>
                <p>
                    @if($f->is_cumulative)      <label class="label label-info"    style="min-width: 100px;">累積する</label>
                    @else                       <label class="label label-warning" style="min-width: 100px;">累積しない</label> @endif
                </p>
                <p>
                    @if($f->is_split)           <label class="label label-info"    style="min-width: 100px;">分割する</label>
                    @else                       <label class="label label-warning" style="min-width: 100px;">分割しない</label> @endif
                </p>
                <p>
                    @if($f->is_account_convert) <label class="label label-info"    style="min-width: 100px;">変換する</label>
                    @else                       <label class="label label-warning" style="min-width: 100px;">変換しない</label> @endif
                </p>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{!! $files->render() !!}


    </div>
</div>

@endsection

@section('footer')
@parent
@endsection