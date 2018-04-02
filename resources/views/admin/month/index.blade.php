<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<!DOCTYPE html>

@extends('layout')
@section('title', '月別マスタ')

@section('header')
@parent
@section('brand', '月別マスタ')
@endsection


@section('brand')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('partial.check_sidebar')
</div>
@endsection

<div style="margin-top: 100px;"></div>

@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        @include('partial.alert')

        <div class="row" style="margin-bottom: 20px;">
            <div class="container-fluid">
                <div class="alert fade in container-fluid" role="alert" style="margin-bottom: 20px; border-radius: 5px; border: 1px solid #ccc;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <div class="col-md-5">
                        <canvas id="usage"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h3 class="border-bottom">データベース専有領域</h3>
                        <table class="table table-small">
                            <tr>
                                <th width="20%" class="bg-primary">DB</th>
                                <td width="80%" class="text-right">{{$usage['db_dir']}}</td>
                            </tr>
                            <tr>
                                <th width="20%" class="bg-primary">使用済み</th>
                                <td width="80%" class="text-right">{{number_format($usage['db_usage'])}}{{$usage['size_unit']}}B / {{$usage['db_usage_percent']}}%</td>
                            </tr>
                            <tr>
                                <th width="20%" class="bg-primary">利用可能</th>
                                <td width="80%" class="text-right">{{number_format($usage['db_available'])}}{{$usage['size_unit']}}B / {{$usage['db_avail_percent']}}%</td>
                            </tr>
                            <tr>
                                <th width="20%" class="bg-primary">合計</th>
                                <td width="80%" class="text-right">{{number_format($usage['db_full_size'])}}{{$usage['size_unit']}}B</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-bottom"><h2>月別マスタ選択</h2></div>
        <div class="text-right" data-spy="affix" data-offset-top="130" style="top: 115px; right: 30px; z-index: 1;">
            <div style="margin-bottom: 10px;">
                {!! $rows->render() !!}
            </div>
            <div class="btn-group" style="margin-bottom: 10px;">
                <a 
                href="{{route('admin::super::month::copy_confirm', ['id'=>date('Ym')])}}"
                class="btn btn-primary btn-sm"
                data-toggle="tooltip"
                title="CSVファイルをサーバーにアップロードします。この時点ではDBに登録されません。"
                style="width: 120px;">アップロード</a>
                <span></span>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#job-status">処理状況確認</button>
                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#add-month">新規ID生成</button>
            </div>
        </div>

        <div class="modal fade" id="add-month" tabindex="-1">
            <form method="POST" action="{{route('admin::super::month::create')}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary-important">
                            <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                            <h4 class="modal-title">新たに月を追加する</h4>
                        </div>
                        <div class="modal-body">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="form-group">
                                    <label>月別ID選択</label>
                                    <select class="form-control" name="monthly_id">
                                        @foreach($months as $m)
                                        <option value="{{$m['ym']}}" @if(date('Ym') == $m['ym']) selected @endif>{{$m['display']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-10 col-md-offset-1">
                                <button type="submit" class="btn btn-warning btn-sm">新規IDを生成する</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal fade" id="job-status" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary-important">
                        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                        <h4 class="modal-title">処理状況を見る</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-small va-middle table-striped table-hover table-condensed">
                            <tr>
                                <th>ID</th>
                                <th>コピー</th>
                                <th>反映</th>
                                <th>エラー</th>
                                <th>処理時間</th>
                            </tr>
                            @foreach($job_status as $job)
                            <tr>
                                <th>{{$job->id}}</th>
                                <td>
                                    @if($job->is_copy_error)   <span class="label label-danger" >異常</span>
                                    @elseif($job->is_copy_end) <span class="label label-success">終了</span>
                                    @else                      <span class="label label-warning">未了</span> @endif
                                </td>
                                <td>
                                    @if($job->is_import_error)      <span class="label label-danger" >異常</span>
                                    @elseif(!$job->is_import_start) <span class="label label-default">未割当</span>
                                    @elseif($job->is_import_end)    <span class="label label-success">終了</span>
                                    @else                           <span class="label label-warning">未了</span> @endif
                                </td>
                                <td>{{$job->error_message}}</td>
                                <td>
                                    <p>@if(!empty($job->created_at) && $job->created_at != '0000-00-00 00:00:00') {{$job->created_at}} @else - @endif</p>
                                    <p>@if(!empty($job->updated_at) && $job->updated_at != '0000-00-00 00:00:00') {{$job->updated_at}} @else - @endif</p>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include('admin.month.partial.month_form')
</div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
<script>
    var mycolor = '#00a383';
    if({{$usage['db_usage_percent']}} > 75) mycolor = '#f39c12';
    if({{$usage['db_usage_percent']}} > 90) mycolor = '#e74c3c';
    var doughnutData = [
    {
        value: {{$usage['db_usage']}},
        color: mycolor,
        label: "使用済み({{$usage['size_unit']}}B)",
    },
    {
        value : {{$usage['db_available']}},
        color : "#cfcfcf",
        label: "未使用({{$usage['size_unit']}}B)",
    },
    ];
    window.onload = function(){
        var ctx = document.getElementById("usage").getContext("2d");
        window.myDoughnut = new Chart(ctx).Doughnut(doughnutData, {
            responsive : true
        });
    }
    // var myDoughnut = new Chart(document.getElementById("sample").getContext("2d")).Doughnut(doughnutData);
</script>

@endsection