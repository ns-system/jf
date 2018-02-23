{{-- CSVファイルエクスポート・インポート --}}
<form class="form-horizontal" role="form" method="POST" action="{{$configs['import_route']}}" enctype="multipart/form-data">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="text-right">
        <div data-spy="affix" style="top: 100px; right: 30px;" data-offset-top="150">

            @if(!$rows->isEmpty())
            <div class="text-right" style="margin-bottom: 10px">
                {!! $rows->appends($search_values)->render() !!}
            </div>
            @endif


            <div class="btn-group">
                @if(!empty($serach_columns))
                <button
                type="button"
                class="btn btn-primary btn-xs"
                style="min-width: 75px;"
                data-toggle="modal"
                data-target="#searchForm"
                >検索</button><span></span>
                @endif
                <a
                class="btn btn-success btn-xs"
                href="{{$configs['export_route']}}"
                style="min-width: 100px; margin: 0;"
                data-toggle="tooltip"
                data-placement="bottom"
                title="表示されているデータをCSVファイルに出力します。"
                name ="ExportCSV"
                id ="ExportCSV"
                data-placement="top">ExportCSV</a><span></span>

                <label
                for="csv_file"
                class="btn btn-primary btn-xs"
                style="min-width: 250px; margin: 0;"
                data-toggle="tooltip"
                data-placement="bottom"
                title="取り込みを行うCSVファイルを選択してください。データ件数が多すぎると取り込めないため、最大1,000件を目安に処理を行ってください。"
                data-placement="top"
                >
                <span id="file_name">ファイルを選択してください</span>
                <input type="file"
                name="csv_file"
                id="csv_file"
                onchange="setFileName(document.getElementById('csv_file').value);"
                >
            </label><span></span>

            <button type="submit"
            class="btn btn-warning btn-xs"
            style="min-width: 100px; margin: 0;"
            data-toggle="tooltip"
            data-placement="bottom"
            name ="ImportCSV"
            id ="ImportCSV"
            title="選択したCSVファイルを取り込みます。先に取り込むファイルを指定してください。"
            data-placement="top"
            onclick="return checkFile(document.getElementById('csv_file').value);"
            >ImportCSV</button><span></span>
        </div>
    </div>
</div>
</form>

<!-- モーダル・ダイアログ -->
<form method="GET" action="{{$configs['index_route']}}">
    <div class="modal fade" id="searchForm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary-important">
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    <h4 class="modal-title">検索</h4>
                </div>
                <div class="modal-body">
                    @foreach($serach_columns as $key => $column)
                    <div class="row form-group">
                        <div class="col-md-4 col-md-offset-1 text-right"><label class="text-right"><small>{{$column['display']}}</small></label></div>
                        <div class="col-md-4">
                            <input type="text" name="{{$key}}" class="form-control input-sm" value="@if(isset($search_values[$key])){{$search_values[$key]}}@endif">
                        </div>
                        <div class="col-md-2 text-left">
                            <small><label>
                                @if($column['type'] === 'string') を含む
                                @else                             に等しい @endif
                            </label></small>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <a href="{{$configs['index_route']}}" class="btn btn-warning">クリア</a>
                        <button type="submit" class="btn btn-primary">検索する</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script type="text/javascript">
    function checkFile(file_name) {
        if (!file_name) {
            alert('ファイルが選択されていません。');
            return false;
        }
        var file_type = file_name.split(".");
        var len = file_type.length;
        if (len === 0) {
            alert('ファイルはCSVのみ対応しています。');
            return false;
        }
        console.log(file_type);
        if (file_type[1] == 'csv') {
            return true;
        }
        alert('ファイルはCSVのみ対応しています。');
        return false;
    }

    function setFileName(file_path) {
        var tmp = file_path.split('\\');
        var file_name = tmp[tmp.length - 1];

        $('#file_name').html(file_name);
        
        if(file_name.length==0){
         $('#file_name').html("ファイルを選択してください");
     }

 }
</script>