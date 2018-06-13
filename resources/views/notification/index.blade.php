@extends('layout')

@section('title', 'ログイン')

@section('header')
@parent
@section('brand', 'ＪＦマリンバンク')
@endsection

@section('sidebar')
<div class="col-md-2">
  @include('partial.check_sidebar')
</div>
@endsection

@section('content')
<div class="col-md-10">
  @include('partial.alert')

  @include('notification.partials.create')

  <table class="table table-hover">
    <thead>
      <th class="bg-primary">作成日</th>
      <th class="bg-primary">公開期限</th>
      <th class="bg-primary">作成者</th>
      <th class="bg-primary">カテゴリー</th>
      <th class="bg-primary">内容</th>
      <th class="bg-primary"></th>
      <th class="bg-primary"></th>

    </thead>
    <tbody>
      @foreach($rows as $r)
      <tr>
        <td>{{ date('Y-m-d', strtotime($r->created_at)) }}</td>
        <td>{{ $r->deadline }}</td>
        <td>{{ $r->user->last_name }} {{ $r->user->first_name }}</td>
        <td>{{ $r->category }}</td>
        <td>{{ $r->message }}</td>
        <td>
          @include('notification.partials.update', ['data'=>$r, 'categories'=>$categories])
        </td>
        <td>
          <form action="/notifications/{{$r->id}}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-danger btn-sm">削除</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection

@section('footer')
@parent
@endsection