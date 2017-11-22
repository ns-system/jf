@extends('layout')

@section('title', 'パスワード再設定')

@section('header')
@parent
@section('brand', 'パスワード再設定')
@endsection

@section('sidebar')
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @include('partial.alert')

            <div class="panel panel-default">
                <div class="panel-heading">パスワード再設定</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="/password/reset">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group">
                            <label class="col-md-4 control-label">メールアドレス</label>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">パスワード</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">パスワード（確認用）</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">パスワードをリセットする</button>
                            </div>
                        </div>
                    </form>
                </div><!-- .panel-body -->
            </div><!-- .panel -->
        </div><!-- .col -->
    </div><!-- .row -->
</div><!-- .container-fluid -->
@endsection

@section('footer')
@parent
@endsection