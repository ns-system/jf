@extends('layout')
@section('title', '件数確認')

@section('header')
@parent
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
        <h2 class="border-bottom">件数確認画面</h2>
        <div class="row">
            <div class="col-md-12">



                <table class="table table-striped table-small va-middle">
                    <thead>
                        <tr class="bg-primary">
                            <th width="200px">テーブル名</th>
                            @foreach($months as $m)
                            <th>{{ $m->monthly_id }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($counts as $table_id => $cnt)
                        <tr>
                            <th class="text-left">
                                <p>{{ $cnt[$m->monthly_id]['table_name_jp'] }}</p>
                                <p><small class="text-muted">{{ $cnt[$m->monthly_id]['table_name'] }}</small></p>
                            </th>
                            @foreach($months as $m)
                            <td class="text-right">{{ number_format(intval($cnt[$m->monthly_id]['count'])) }}件</td>
                            @endforeach
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
@parent
</script>
@endsection