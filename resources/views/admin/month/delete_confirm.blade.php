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
        <h2 class="border-bottom">削除処理 - テーブル選択 <small> - {{$monthly_id}}
            @if($term_status === 'daily')
            <label class="label label-success">日次</label>
            @elseif($term_status === 'weekly')
            <label class="label label-success">週次</label>
            @elseif($term_status === 'monthly')
            <label class="label label-success">月次</label>
            @endif
        </small></h2>
        <div class="row">
            <div class="col-md-6">
                <form method="POST" action="{{route('admin::super::term::delete')}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <select class="form-control input-sm" multiple size="18" name="tables[]">
                        @foreach($table_lists as $table)
                        <option value="{{$table->key_id}}">{{$table->zenon_data_name}} / {{$table->table_name}}</option>
                        @endforeach
                    </select>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="agree">
                            <b>私は削除に伴う責任を理解した上で削除を行います。</b>
                        </label>
                    </div>
                    <p>
                        <button type="submit" class="btn btn-block btn-danger" onclick="return confirm('選択したテーブルの中身を削除してもよろしいですか？');">削除する</button>
                    </p>
                </form>
            </div>

            <div class="col-md-6">
                <div class="well">
                    <p>削除したいテーブルを選択してください。</p>
                    <p>一度削除されたデータは復元することはできない点に十分ご留意ください。</p>
                    <p>Ctrl+Aで全選択、Ctrl+クリックで複数テーブルを選択することもできます。</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
@parent
</script>
@endsection