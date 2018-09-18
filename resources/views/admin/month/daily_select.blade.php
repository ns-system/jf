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
        <h2 class="border-bottom">日次処理 - 日付選択 <small> - {{$id}}</small></h2>
        @if(!empty($date_list))
        <div class="row">
            <div class="col-md-4">
                <form method="POST" action="{{route('admin::super::term::daily_select', ['id'=>$id, 'job_id'=>$job_id])}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <h3 class="border-bottom">日付選択</h3>
                    <select size="18" class="form-control" id="date" name="date">
                        @foreach($date_list as $date)
                        <option value="{{$date}}">{{$date}}</option>
                        @endforeach
                    </select>
                    <p><button type="submit" class="btn btn-primary btn-block">選択する</button></p>
                </form>
            </div>

            <div class="col-md-8">
                <h3 class="border-bottom">ファイルリスト</h3>
                @foreach($date_list as $date)
                <div id="list_{{$date}}" style="display: none;" class="file_list">
                    <div class="well" style="padding: 10px 30px;">
                        @foreach($file_list[$date] as $file)
                        <p><small>{{$file}}</small></p>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="alert alert-warning" role="alert">ファイルが見つかりませんでした。</div>
        @endif
    </div>
</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
    $(function(){
        $('#date').on('click', function(){
            console.log($(this).val());
            $('.file_list').each(function(){ $(this).hide(); });
            $('#list_' + $(this).val()).show();
        });
    });

</script>
@endsection