@extends('layout')

{{-- @section('title', 'CSV確認')
 --}}
@section('header')
@parent
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
        <div class="border-bottom"><h2>{!! $configs['h2'] !!}</h2></div>

        <form class="form-horizontal" role="form" method="POST" action="{!! $configs['form_route'] !!}">
            {{-- CSRF対策--}}
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="text-right" data-spy="affix" style="right: 30px; top: 100px;" data-offset-top="150">
    <div class="btn-group">
        <button type="button" class="btn btn-primary btn-sm margin-bottom" id="more">もっと見る</button>
        <button type="sumbit" class="btn btn-success btn-sm margin-bottom" onclick="return confirm('取り込んだデータをデータベースに反映させてよろしいですか？');">更新する</button>
    </div>
</div>
            <table class="table table-hover va-middle table-small">
                <thead>
                    <tr>
                        <th class="bg-primary">No</th>
                        @foreach($configs['table_columns'] as $column_config)
                        @if(array_key_exists('class', $column_config))
                        <th class="bg-primary $column_config['class']">{{$column_config['2']}}</th>
                        @else
                        <th class="bg-primary">{{$column_config['2']}}</th>
                        @endif
<!--                        <th class="bg-primary @if(array_key_exists('class', $column_config)) {{$column_config['class']}} @endif">{{$column_config['2']}}</th>-->
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $cnt => $row)
                    <tr class="rows" data-count="{{$cnt}}" @if($cnt > 24) style="display: none;" data-hidden="true" @endif>

                        {{-- True  --}}
                        <th class="bg-primary">
                            <span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span>
                            {{$cnt + 1}}
                        </th>
                        @foreach($configs['table_columns'] as $column_config)
                        @if(array_key_exists('class', $column_config))
                        <td class="$column_config['class']">
                            @else
                        <td>
                            @endif
    <!--                        <td class="@if(array_key_exists('class', $column_config)) {{$column_config['class']}} @endif">-->

                            @if(array_key_exists('format', $column_config))
                            <?php $display = sprintf($column_config['format'], $row[$column_config[1]]); ?>
                            @else
                            <?php $display = $row[$column_config[1]]; ?>
                            @endif

                            @if($column_config[0] === 1)
                            <span>{{$display}}</span>
                            <?php
                            $key     = '';
                            if (is_array($configs['key']))
                            {
                                // Multi key
                                foreach ($configs['key'] as $pk) {
                                    $key .= $row[$pk] . '-';
                                }
                                $key = mb_substr($key, 0, mb_strlen($key) - 1);
                            }
                            else
                            {
                                // Single key
                                $key = $row[$configs['key']];
                            }
                            ?>
                            <input type="hidden" name="<?php echo $column_config[1] . '[' . $key . ']'; ?>" value="{{$row[$column_config[1]]}}">
                            @else
                            {{$display}}
                            @endif
                        </td>
                        @endforeach

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
<script type="text/javascript">
$(function(){
    $('#more').click(function(){
        var i = 0;
        $('.rows[data-hidden="true"]').each(function(){
            i++;
            $(this).show().removeAttr('data-hidden');
            console.log(i);
            if(i >= 25){ return false; }
        });
        if($('.rows[data-hidden="true"]').length == 0){
            $('#more').attr('disabled', 'disabled');
        }
    });
});
</script>
@endsection