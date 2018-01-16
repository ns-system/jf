@extends('layout')
@section('title', 'テーブルカラム削除')

@section('header')
@parent
@section('brand', '管理ユ')

@section('sidebar')
<div class="col-md-2">
    @include('partial.check_sidebar')
</div>
@endsection

<div style="margin-top: 100px;"></div>



@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        @include('partial.alert')
        <div class="border-bottom"><h2>テーブルカラム削除</h2></div>

        @if(!$tables->isEmpty())

        <div class="alert alert-danger alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>警告</strong>
            <ul>
                <li>一度削除したテーブルカラムを復元することはできません。</li>
                <li>削除を行う前にエクスポートを行うことを強く推奨します。</li>
            </ul>
        </div>

        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-md-10 col-md-offset-1">
                        <form method="POST" action="{{route('admin::super::zenon_table::delete')}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            @foreach($tables as $table)
                            @endforeach
                            <select class="form-control" name="zenon_format_id">
                                @foreach($tables as $table)
                                <option value="{{$table->zenon_format_id}}">
                                    {{$table->zenon_data_name}}
                                    @if(!empty($table->total) && $table->total > 1) ［{{number_format($table->total)}}カラム］ @endif
                                </option>
                                @endforeach
                            </select>
                            <a href="{{route('admin::super::config::index', ['system'=>'Admin','category'=>'ZenonTable'])}}">エクスポートする</a>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="agree" value="true"> <b>削除に伴う責任を理解した上で処理を行います</b>
                                </label>
                            </div>
                            <p class="text-right">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('削除後はテーブルカラム情報を復元することはできません。削除してよろしいですか？');">削除する</button>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        @else
        <div class="alert alert-warning" role="alert">データが見つかりませんでした。</div>
        @endif

    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection