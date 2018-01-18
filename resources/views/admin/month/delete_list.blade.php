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
        <h2 class="border-bottom">削除処理 - 区分選択<small> - {{$monthly_id}}</small>
        </h2>
        <div class="row">
            <div class="col-md-4">
                <a href="{{route('admin::super::term::delete_confirm', ['term_status'=>'daily',  'id'=>$monthly_id])}}" class="btn btn-primary btn-block">日次</a>
                <a href="{{route('admin::super::term::delete_confirm', ['term_status'=>'weekly', 'id'=>$monthly_id])}}" class="btn btn-primary btn-block">週次</a>
                <a href="{{route('admin::super::term::delete_confirm', ['term_status'=>'monthly','id'=>$monthly_id])}}" class="btn btn-primary btn-block">月次</a>
            </div>

            <div class="col-md-8">
                <div class="well">
                    <p>削除したい区分を選択してください。</p>
                    <p>次の画面で削除する対象を選択するため、この段階では削除されません。</p>
                    <p>一度削除されたデータは復元することはできない点に十分ご留意ください。</p>
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