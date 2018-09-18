@extends('layout')

@section('header')
@parent
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('partial.check_sidebar')
</div>
@endsection

@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        @include('partial.alert')
        <div class="border-bottom"><h2>{!! $configs['h2'] !!}</h2></div>

        <div class="text-right" data-spy="affix" style="right: 30px; top: 100px;" data-offset-top="150">
            <div class="btn-group">
                <a href="{!! $configs['index_route'] !!}" class="btn btn-success btn-sm margin-bottom">確認する</a>
            </div>
        </div>
        <table class="table table-hover va-middle table-small">
            <thead>
                <tr>
                    <th class="bg-primary">No</th>
                    @foreach($configs['table_columns'] as $column_config)
                    <th @if(array_key_exists('class', $column_config)) class="bg-primary {{$column_config['class']}}" @else class="bg-primary" @endif>{{$column_config['2']}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $cnt => $row)
                <?php if($cnt >= 50) break; ?>
                <tr class="rows" data-count="{{$cnt}}">
                    {{-- True  --}}
                    <th class="bg-primary">
                        <span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span>
                        {{$cnt + 1}}
                    </th>
                    @foreach($configs['table_columns'] as $column_config)
                    <td @if(array_key_exists('class', $column_config)) class="{{$column_config['class']}}" @endif>


                        @if(array_key_exists('format', $column_config))
                        <?php $display = sprintf($column_config['format'], $row[$column_config[1]]); ?>
                        @else
                        <?php $display = $row[$column_config[1]]; ?>
                        @endif

                        <span
                        @if($column_config[0] === 0) class="text-muted" data-toggle="tooltip" title="このカラムはデータベースに登録されません。" @endif
                        >{{$display}}</span>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($rows) > 50)
        <p class="text-right">...他、{{number_format(count($rows) - 50 + 1)}}件</p>
        @endif
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