@extends('layout')

@section('header')
@parent
@endsection


@section('brand')
@endsection

@section('sidebar')
<div class="col-md-2">
    @include('admin.sidebar.sidebar')
</div>
@endsection

<div style="margin-top: 100px;"></div>



@section('content')
<div class="col-md-10">
    <div class="container-fluid">
        @include('partial.alert')
        <div class="border-bottom"><h2>{{$configs['h2']}}</h2></div>
@include('partial.csv_form.form')
@if(!$rows->isEmpty())
        <table class="table table-hover table-small va-middle table-striped">
            <thead>
                <tr>
                    <th class="bg-primary">No</th>
                    @foreach($configs['table_columns'] as $config)
                    <th class="bg-primary">
                        @foreach($config as $columns)
                        @if(array_key_exists('model', $columns))
                        @foreach($columns['row'] as $column)
                        <p class="@if(array_key_exists('class', $column)) {{$column['class']}} @endif">{{$column[1]}}</p>
                        @endforeach
                        @else
                        @foreach($columns as $column)
                        <p class="@if(array_key_exists('class', $column)) {{$column['class']}} @endif">{{$column[1]}}</p>
                        @endforeach
                        @endif
                        @endforeach
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>

                @foreach($rows as $cnt => $row)
                <tr>
                    <th class="bg-primary">{{$cnt + 1}}</th>
                    @foreach($configs['table_columns'] as $config/* 各列の表示情報［配列］ => 列内表示情報 */)
                    @foreach($config as $columns/* 各列の表示情報［配列］ => セル内表示情報 */)
                    <td>
                        @foreach($columns as $column/* 各セルの表示情報 => pタグ内表示情報 */)
                        <?php $key = $column[0]; ?>
                        @if(isset($row->$key))
                        <p
                            @if(array_key_exists('class', $column)) class="{{$column['class']}}" @endif
                        >
                            @if(array_key_exists('format', $column)) {{sprintf($column['format'], (int) $row->$key)}} @else {{$row->$key}} @endif
                        </p>
                        @endif
                        @endforeach
                    </td>
                    @endforeach
                    @endforeach
                </tr>
                @endforeach

            </tbody>
        </table>

@else
<div class="alert alert-warning" role="alert">データが見つかりませんでした。</div>
@endif

    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection