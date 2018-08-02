@extends('layout')

@section('title', 'CSVリスト')

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
    <div class="border-bottom"><h2>勤怠管理システム CSV出力 <small> - リスト</small></h2></div>
    @include('roster.admin.csv.partial.search')

    @if(!empty($not_accepts))
    <div class="alert alert-danger" role="alert">
        <p>
            @include('partial.bell')未入力・未承認が 
            <a href="#not-accepts" data-toggle="modal">
                <b class="text-danger">{{ number_format($not_accepts->count()) }}件</b>
            </a>
            あります。
        </p>
    </div>
    <div class="modal fade" id="not-accepts" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary-important">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">タイトル</h4>
                </div>
                <div class="modal-body">
                    @include('partial.not_accept', ['not_accepts'=>$not_accepts,'is_chief'=>false, 'is_admin'=>true])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if(!empty($rosters) && !$rosters->isEmpty())
    @include('roster.admin.csv.partial.list')
    @else
    <div class="alert alert-warning" role="alert">データが見つかりませんでした。</div>
    @endif
</div>
@endsection

@section('footer')
@parent
@endsection