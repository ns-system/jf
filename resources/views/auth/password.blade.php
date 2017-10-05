@extends('layout')

@section('title', 'パスワードリセット')

@section('header')
@parent
@section('brand', 'パスワードリセット')
@endsection

@section('sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
                @include('partial.alert')

            <div class="panel panel-warning">
                <div class="panel-heading">パスワードリセット</div>
                <div class="panel-body">
{{--                     @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif --}}

                    <form class="form-horizontal" role="form" method="POST" action="/password/email">
                        {{-- CSRF対策--}}
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group">
                            <label class="col-md-4 control-label">メールアドレス</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                                <span id="helpBlock" class="help-block">登録されているメールアドレスを入力してください。</span>

                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4 text-left">
                                <button type="submit" class="btn btn-warning" onclick="return confirm('パスワードをリセットしてよろしいですか？');">
                                    パスワードをリセットする
                                </button>
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