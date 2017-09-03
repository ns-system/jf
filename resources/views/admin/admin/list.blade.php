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

        <table class="table table-hover table-small va-middle table-striped margin-0">
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
                    @foreach($configs['table_columns'] as $config)
                    @foreach($config as $columns)
                    <td>
                        @if(array_key_exists('model', $columns))
                        <?php
                        $model     = new $columns['model'];
                        $lk        = $columns['key']['local_key'];
                        $fk        = $columns['key']['foreign_key'];
                        // var_dump($lk);
                        // var_dump($fk);
                        // exit();
                        $model_row = $model->where($lk, $row[$fk])->first();
                        ?>
                        @if($model_row)
                        @foreach($columns['row'] as $column)
                        <p class="@if(array_key_exists('class', $column)) {{$column['class']}} @endif">
                            @if(array_key_exists('format', $column))
                                {{sprintf($column['format'], $model_row[$column[0]])}}}
                            @else
                                {{$model_row[$column[0]]}}
                            @endif
                        </p>
                        @endforeach
                        @endif
                        @else
                        @foreach($columns as $column)
                        <p class="@if(array_key_exists('class', $column)) {{$column['class']}} @endif">
                            @if(array_key_exists('format', $column))
                                {{sprintf($column['format'], $row[$column[0]])}}
                            @else
                                {{$row[$column[0]]}}
                            @endif
                        </p>
                        @endforeach
                        @endif
                    </td>
                    @endforeach
                    @endforeach
                </tr>
                @endforeach

            </tbody>
        </table>

        {!! $rows->render() !!}
    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
@parent
@endsection