@extends('layout')

@section('title', 'ログイン')

@section('header')
@parent
@section('brand', 'ＪＦマリンバンク')
<style type="text/css">
    body{
        background: #44A08D;  /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #44A08D, #093637);  /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #44A08D, #093637); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

        background: #41295a;  /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #2F0743, #41295a);  /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #2F0743, #41295a); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

        background: #141E30;  /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #243B55, #141E30);  /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #243B55, #141E30); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

    }
</style>
@endsection

@section('sidebar')
@endsection

@section('content')
<div class="container-fluid">

    <div clas="row">
        <div class="col-md-8 col-md-offset-2">
            <!-- タブ・メニュー -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#login-form" data-toggle="tab"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> ログイン</a></li>
                <li><a href="#register-form" data-toggle="tab"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> ユーザー登録</a></li>
            </ul>

            <!-- タブ内容 -->
            <div class="tab-content" style="margin-top: 20px;">
                <div class="tab-pane active" id="login-form">
                    <div class="col-md-10 col-md-offset-1" style="background: rgba(255,255,255,0.1); padding: 30px 0px; border-radius: 2px; border: 1px solid #999;">
                        @include('auth.partial.login_form')
                    </div>
                </div>
                <div class="tab-pane" id="register-form">
                    <div class="col-md-10 col-md-offset-1" style="background: rgba(255,255,255,0.1); padding: 30px 0px; border-radius: 2px; border: 1px solid #999;">
                        @include('auth.partial.register_form')
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div clas="row">
        <div class="col-md-8 col-md-offset-2" style="margin-top: 20px;">
            @include('partial.alert')
        </div>
    </div>
</div><!-- .container-fluid -->
@endsection

@section('footer')
@parent
@endsection