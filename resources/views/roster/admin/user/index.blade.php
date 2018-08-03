@extends('layout')

@section('title', 'ユーザー権限')

@section('header')
@parent
@section('brand', '勤怠管理システム')
@endsection

@section('sidebar')
<div class="col-md-2">
  @include('partial.check_sidebar')

</div>
@endsection



@section('content')
<div class="col-md-10">
  @include('partial.alert')
  <div class="border-bottom"><h2>勤怠管理システム 管理ユーザーリスト</h2></div>
  {{-- @include('roster.admin.user.partial.search') --}}
  <div style="margin-bottom: 15px;"></div>
  <div>
    @if($users->isEmpty())
    <div class="alert alert-warning" role="alert">ユーザーが見つかりませんでした。</div>
    @else
    <table class="table table-small table-hover table-sortable">
      <thead>
        <tr>
          <th class="bg-primary">#</th>
          <th class="bg-primary">名前</th>
          <th class="bg-primary">名前</th>
          <th class="bg-primary">部署</th>
          <th class="bg-primary">ユーザー区分</th>
          <th class="bg-primary">管轄部署</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $key => $user)
        <tr class="va-middle">
          <th class="bg-primary">{{ $key + 1 }}</th>
          <td>
            @include('partial.retirement', ['retirement'=>$user->retirement,'hidden'=>$user->roster_hidden]){{$user->last_name}} {{$user->first_name}} <small>さん</small>
          </td>
          <td>{{ $user->last_name_kana }} {{ $user->first_name_kana }}</td>
          <td>
            <p>{{$user->division_name}}</p>
            <p><a href="{{route('app::roster::user::show', ['id'=>$user->user_id])}}">変更する</a></p>
          </td>
          <td>
            @if(is_null($user->is_administrator)) <p><span class="label label-default">未登録</span></p>
            @elseif($user->is_administrator)    <p><span class="label label-danger" >管理者</span></p>
            @elseif($user->is_chief)            <p><span class="label label-warning">責任者</span></p>
            @elseif($user->is_proxy)
            @if($user->is_proxy_active)
            <p>
              <span class="label label-success" data-toggle="tooltip" title="責任者代理機能は有効です。">
                <span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>  責任者代理
              </span>
            </p>
            @else
            <p>
              <span class="label label-default" data-toggle="tooltip" title="責任者代理機能は無効です。">
                <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> 責任者代理
              </span>
            </p>
            @endif
            @else <p><span class="label label-primary">一般ユーザー</span></p>
            @endif
            <p><a href="{{route('admin::roster::user::show', ['id'=>$user->user_id])}}">変更する</a></p>
          </td>
          <td class="text-left">
            @if(!empty($controls))
            <ul>
              @foreach($controls as $c)
              @if($c->user_id == $user->user_id) <li>{{$c->division_name}}</li> @endif
              @endforeach
            </ul>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>

</div>
@endsection

@section('footer')
@parent
@endsection