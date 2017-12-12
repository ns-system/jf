@extends('layout')
@section('title', '委託者マスタ')

@section('header')
@parent
@section('brand', '委託者マスタ')
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
        <div class="border-bottom"><h2>委託者マスタ生成</h2></div>

        <div class="text-right" data-spy="affix" data-offset-top="100" style="top: 115px; right: 30px; z-index: 1;">
            <div style="margin-bottom: 10px;">
                {!! $consignors->render() !!}
            </div>
            <div class="btn-group" style="margin-bottom: 10px;">
                <a class="btn btn-success btn-sm" href="{{route('admin::super::month::consignor::create', ['monthly_id'=>$monthly_id])}}" onclick="return confirm('この内容でデータを更新してよろしいですか？');">委託者マスタ更新</a>
            </div>
        </div>

        @if(!empty($consignors) && !$consignors->isEmpty())
        <table class="table table-small">
            <thead>
                <tr class="bg-primary">
                    <th>番号</th>
                    <th>委託者コード</th>
                    <th class="text-left">委託者名</th>
                    <th class="text-right">当月契約件数</th>
                    <th>参考最終取引日</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consignors as $no => $consignor)
                <tr>
                    <th class="bg-primary">{{($no + 1)}}</th>
                    <td><b>{{$consignor->consignor_code}}</b></td>
                    <td class="text-left">{{$consignor->consignor_name}}</td>
                    <td class="text-right">{{number_format((int) $consignor->total_count)}}件</td>
                    <td>
                        @if($consignor->reference_last_traded_on >= $consignor->reference_last_traded_on)
                        {{$consignor->last_traded_on}}
                        @else {{$consignor->reference_last_traded_on}} @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-warning" role="alert">データが見つかりませんでした。先に月次処理を行ってください。</div>
        @endif
    </div>
</div><!-- .container-fluid -->
@endsection

@section('footer')
@parent
@endsection