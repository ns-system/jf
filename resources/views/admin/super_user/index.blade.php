@extends('layout')

@section('title', '管理ユーザー設定')

@section('header')
@parent
@section('brand', '管理ユーザー設定')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')
</div>
@endsection

@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        @include('partial.alert')
        <div class="border-bottom"><h2>管理ユーザー設定</h2></div>

        <div class="text-right">
            @include('admin.admin.partial.admin_search')
        </div>

        @if($users->count() != 0)
        <table class="table">
            <thead>
                <tr>
                    <th class="bg-primary">名前</th>
                    <th class="bg-primary">部署</th>
                    <th class="bg-primary">メールアドレス</th>
                    <th class="bg-primary">管理ユーザー</th>
                    {{--                     <th class="bg-primary">推進支援</th> --}}
                    <th class="bg-primary">勤怠管理</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <?php /*$user = \App\User::find($tmp_user->key_id); */ ?>
                <tr>
                    <td>
                        @if($user->id == \Auth::user()->id){{$user->last_name}} {{$user->first_name}} <small>さん</small>
                        @else<a href="{{route('admin::super::user::detail', ['id'=>$user->id])}}">{{$user->last_name}} {{$user->first_name}} <small>さん</small></a>
                        @endif
                    </td>
                    <td>@if(!empty($user->division_name)) {{$user->division_name}} @else 登録なし @endif</td>
                    <td>{{$user->email}}</td>
                    <td>
                        @if(!$user->is_super_user) <span class="bolder">一般</span> @else <span class="bolder text-danger">スーパーユーザー</span> @endif
                    </td>
{{--                     <td>
                        @if($user->SuisinUser)
                        @if(!$user->SuisinUser->is_administrator) <span class="bolder">一般</span> @else <span class="bolder text-danger">管理</span> @endif
                        @else
                        <span class="bolder text-warning"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>未登録</span>
                        @endif
                    </td> --}}
                    <td>
                        @if($user->roster_user_id)
                        @if(!$user->is_roster_admin) <span class="bolder">一般</span> @else <span class="bolder text-danger">管理</span> @endif
                        @else
                        <span class="bolder text-warning"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>未登録</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-warning" role="alert">ユーザーが見つかりませんでした。</div>
        @endif

        {!! $users->appends(['name' => $name, 'div' => $div,])->render() !!}
    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection