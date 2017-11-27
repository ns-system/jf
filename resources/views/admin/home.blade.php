@extends('layout')

@section('title', '管理者ホーム')

@section('header')
@parent
@section('brand', '管理者メニュー')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')
</div>
@endsection


@section('content')
<div class="col-md-6">
    <div class="border-bottom"><h2>管理者用メニュー</h2></div>

    <div class="well">
       <p>ここから管理用コンソールに接続できます。</p>
       <p>管理用コンソールからは各種マスタの変更や修正が行えます。</p>
       <p>なお、権限のないマスタの修正を行うことはできません。</p>
   </div>
</div>

<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading"><h4>お知らせ</h4></div>
        @if(!empty($new_users) && !$new_users->isEmpty())
        <table class="table">
            <tbody>
                @foreach($new_users as $new)
                <tr>
                    <td class="text-left" style="font-size: 85%;">
                        @if($new->is_new)
                        <p><span class="label label-success">ユーザー追加</span> <small>{{date('Y年n月j日 H時i分', strtotime($new->updated_at))}}</small></p>
                        <p>{{$new->last_name}} {{$new->first_name}}<small>さん</small>が追加されました。</p>
                        @else
                        <p><span class="label label-info">ユーザー更新</span> <small>{{date('Y年n月j日 H時i分', strtotime($new->updated_at))}}</small></p>
                        <p>{{$new->last_name}} {{$new->first_name}}<small>さん</small>の情報が更新されました。</p>
                        @endif
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
        @else
        <p>新着情報はありません。</p>
        @endif
    </div>
</div>

@endsection

@section('footer')
@parent
@endsection