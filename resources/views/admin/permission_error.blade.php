@extends('layout')

@section('title', '権限エラー')

@section('header')
@parent
@section('brand', 'JFマリンバンク')
@endsection

@section('content')
<div class="col-md-10 col-md-offset-1">
<div class="alert alert-danger" role="alert"><strong>エラー</strong>：　許可されていないアクセスを行おうとしました。 <a href="/">ホームに戻る</a></div>
</div>
@endsection

@section('footer')
@parent
@endsection