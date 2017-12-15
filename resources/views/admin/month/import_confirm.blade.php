@extends('layout')
@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('title', '月別マスタ')

@section('header')
@parent
@section('brand', '月別マスタ')
@endsection


@section('brand')
@endsection

@section('sidebar')
<div class="col-md-2">
    {{--     <div data-spy="affix" style="min-width: 200px; max-width: 300px; width: 100%;"> --}}
        <div class="nav nav-pills nav-stacked list-group">
            <p role="presentation" class="list-group-item collapse bg-primary-important" style="color: #fff;">メニュー</p>

            <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#filter"><span class="caret"></span> 絞<small>り込み</small></a>
            <span></span>
            <div class="collapse" id="filter">
                <a role="presentation"
                class="list-group-item collapse list-second"
                href="#"
                onclick="return false;"
                id="process"
                >処理対象のみ</a>

                <a role="presentation"
                class="list-group-item collapse list-second"
                href="#"
                onclick="return false;"
                id="reset"
                >絞り込み解除</a>

                <a role="presentation"
                class="list-group-item collapse list-second"
                href="#"
                id="more"
                onclick="return false;"
                >もっと見る</a>
                <span></span>
            </div><span class="list-group-item collapse list-divider"></span>

            <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#change"><span class="caret"></span> チ<small>ェック状態</small></a>
            <span></span>
            <div class="collapse" id="change">
                <a role="presentation"
                class="list-group-item collapse list-second"
                href="#"
                onclick="return false;"
                id="check"
                >全てにチェック</a>

                <a role="presentation"
                class="list-group-item collapse list-second"
                href="#"
                onclick="return false;"
                id="uncheck"
                >チェックを外す</a>
                <span></span>
            </div>
        </div>
    {{--     </div> --}}
</div>
@endsection

<div style="margin-top: 100px;"></div>

@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        @include('partial.alert')
        @include('admin.month.partial.breadcrumbs')
        <div class="border-bottom">
            <h2>CSVファイルコピー処理<small> - {{date('Y年n月', strtotime($id.'01'))}} 月次処理</small></h2>
        </div>

        <form method="POST" action="{{route('admin::super::month::import_dispatch', ['id'=>$id, 'job_id'=>$job_id])}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="text-right" data-spy="affix" data-offset-top="130" style="margin-bottom: 10px; top: 110px; right: 25px;">
                <button type="submit"              class="btn btn-sm btn-success" style="min-width: 115px;" onclick="return validateCheck();">処理を開始する</button>
            </div>


            <table class="table table-hover table-striped table-small va-middle">
                <thead>
                    <tr class="bg-primary">
                        <th>No</th>
                        <th></th>
                        <th>
                            <p data-toggle="tooltip" title="処理を行う場合チェックを入れてください。データが取り込まれていない場合、処理は行えません。">
                                処理
                            </p>
                        </th>
                        <th>
                            <p data-toggle="tooltip" title="還元データファイル名とCSVファイル名です。">
                                還元データ名／CSVファイル名
                            </p>
                        </th>
                        <th>
                            <p data-toggle="tooltip" title="還元日＝実際にデータが還元された日です。月次データは前月末時点のデータとなります。目安還元日＝還元が想定される目安日です。">
                                還元日／目安還元日
                            </p>
                        </th>
                        <th>
                            <p data-toggle="tooltip" title="当月のデータベースに登録されている件数を表示しています。口座共通部は全てのレコードの合計件数となっています。">
                                当月登録件数
                            </p>
                        </th>
                        <th>
                            <p data-toggle="tooltip" title="対象＝処理対象となっているデータ，ファイル＝CSVファイルが存在するデータ，処理＝現在の処理状態">
                                状態
                            </p>
                        </th>
                        <th>
                            <p data-toggle="tooltip" title="設定カラム数＝登録されているカラムの数（MySQL全オンテーブル設定参照），データカラム件数＝想定されるカラム数（全オン還元CSVファイル設定参照）">
                                設定カラム数／データカラム件数
                            </p>
                        </th>
                        <th>
                            <p data-toggle="tooltip" title="累積＝累積し続けるデータ，分割＝CSVファイルを分割するかどうか（例：口座共通部など），変換＝口座番号の変換を行うか（当座性・定期性他）">
                                設定情報
                            </p>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($files as $i => $f)
                    <tr
                    class="display"
                    @if($i < 25) data-display="true"
                    @else        data-display="false" @endif

                    data-process="{{$f->is_process}}"
                    >
                    <th class="bg-primary">{{$i + 1}}</th>
                    <td>
                        @if($f->is_process && ($column_counts[$f->key_id] === $f->column_length) && $column_counts[$f->key_id] > 0)
                        @else <p class="text-danger" data-toggle="tooltip" data-title="処理は行われません。">
                            <span class="glyphicon glyphicon-ban-circle" aria-hidden="true" style="font-size: 24px;"></span>
                        </p>
                        @endif

                        @if(!$f->is_import || $record_counts[$f->key_id] <= 0)
                        @else <p class="text-info" data-toggle="tooltip" data-title="処理は行えますが、すでにデータベースに登録されているようです。">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" style="font-size: 24px;"></span>
                        </p>
                        @endif

                    </td>
                    <td>
                        
                        <input
                        type="checkbox"
                        style="width: 18px;height: 18px;vertical-align: middle; margin:0; margin-bottom: 5px;"
                        name="process[{{$f->key_id}}]"
                        @if($f->is_process && ($column_counts[$f->key_id] === $f->column_length) && $column_counts[$f->key_id] > 0)
                        data-toggle="tooltip"
                        data-placement="right"
                        title="すでに当月データが累積されていた場合、データが二重で累積される恐れがあります。"
                        value="{{$f->key_id}}"
                        @if(!$f->is_exist) disabled 
                        @elseif(!$f->is_process) disabled
                        @endif

                        @if(!$f->is_import && $f->is_exist && $f->is_process && $record_counts[$f->key_id] <= 0) checked @endif
                        @else disabled
                        @endif
                        >
                        {{--                 <input type="hidden" name="id[{{$f->key_id}}]" value="{{$f->key_id}}"> --}}
                    </td>
                    <td class="text-left">
                        <p>{{$f->zenon_format_id}}：{{$f->zenon_data_name}}</p>
                        <P>{{$f->csv_file_name}}</P>
                    </td>
                    <td class="va-middle">
                        <p>@if(!empty($f->csv_file_set_on) && $f->csv_file_set_on != '0000-00-00'){{$f->csv_file_set_on}} @endif</p>
                        <p>{{$f->reference_return_date}}</p>
                    </td>
                    <td class="text-right">{{number_format((int) $record_counts[$f->key_id])}}件</td>
                    <td class="va-middle">
                        <p>
                            @if($f->is_process) <label class="label label-success" style="min-width: 75px;">対象</label>
                            @else               <label class="label label-default" style="min-width: 75px;">対象</label> @endif
                        </p>
                        <p>
                            @if($f->is_exist) <label class="label label-success" style="min-width: 75px;">ファイル</label>
                            @else             <label class="label label-default" style="min-width: 75px;">ファイル</label> @endif
                        </p>
                        <p>
                            @if($f->is_import)       <label class="label label-success" style="min-width: 75px;">処理済</label>
                            @else                    <label class="label label-warning" style="min-width: 75px;">未処理</label> @endif
                        </p>
                    </td>

                    <td class="va-middle text-right">
                        <p @if($column_counts[$f->key_id] <= 0) class="text-warning" data-toggle="tooltip" data-title="テーブルカラムが取り込まれていないようです。" data-placement="right" @endif>
                            {{number_format((int) $column_counts[$f->key_id])}}件
                        </p>
                        <p @if($f->column_length !== $column_counts[$f->key_id]) class="text-warning" data-toggle="tooltip" data-title="全オン還元CSVファイル設定情報とテーブルカラム数が一致していないようです。" data-placement="right" @endif>{{number_format((int) $f->column_length)}}件</p>
                    </td>

                    <td>
                        <p>
                            @if($f->is_cumulative)      <label class="label label-info"   >累積</label>
                            @else                       <label class="label label-default">累積</label> @endif
                        </p>
                        <p>
                            @if($f->is_split)           <label class="label label-info"   >分割</label>
                            @else                       <label class="label label-default">分割</label> @endif
                        </p>
                        <p>
                            @if($f->is_account_convert) <label class="label label-info"   >変換</label>
                            @else                       <label class="label label-default">変換</label> @endif
                        </p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </form>


</div>
</div>
@include('admin.month.partial.error_box')

@endsection

@section('footer')
@parent
<script type="text/javascript">
    $(function(){
        $('.display').each(function(){
            // console.log($(this).attr('data-display'));
            if($(this).attr('data-display') == false){
                $(this).hide();
            }
        });
        $('#more').click(function(){
            var i = 0;
            $('.display[data-display="false"]').each(function(){
                $(this).show();
                $(this).attr('data-display', true);
                i++;
                if(i > 24){
                    return false;
                }
            });
            if($('.display[data-display="false"]').length == 0){
                $('#more').attr('disabled', true);
            }
        });

        $('#process').click(function(){
            $('.display').show();
            $('.display[data-process="0"]').each(function(){
                // console.log($(this));
                $(this).hide();
            });
        });

        $('#reset').click(function(){
            $('#more').attr('disabled', false);
            $('.display').hide().attr('data-display', false);
            var i = 0;
            $('.display').each(function(){
                $(this).show().attr('data-display', true);
                i++;
                if(i > 24){
                    return false;
                }
            });
        });

        $('#check').click(function(){
            $('input[type="checkbox"]').not(':disabled').each(function(){
                $(this).prop('checked', true);
            });
        });
        $('#uncheck').click(function(){
            $('input[type="checkbox"]').not(':disabled').each(function(){
                $(this).prop('checked', false);
            });
        });
    });

    function validateCheck(){
        var check_count = $('input[type="checkbox"]:checked').not(':disabled').length;
        if(check_count <= 0){
            alert('最低でも一つにチェックを入れてください。');
            return false;
        }else{
            return confirm('すでに当月データが累積されていた場合、データが二重で累積される恐れがあります。処理を継続してよろしいですか？');
        }
    }
</script>
@endsection