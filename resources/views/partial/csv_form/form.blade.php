{{-- CSVファイルエクスポート・インポート --}}
<form class="form-horizontal" role="form" method="POST" action="{{$configs['import_route']}}" enctype="multipart/form-data">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

<div class="text-right">
<div data-spy="affix" style="top: 100px; right: 30px;" data-offset-top="110">

    @if(!$rows->isEmpty())
    <div class="text-right" style="margin-bottom: 10px">
        {!! $rows->render() !!}
    </div>
    @endif


    <div class="btn-group">
        <a
            class="btn btn-success btn-xs"
            href="{{$configs['export_route']}}"
            style="min-width: 100px;"
            data-toggle="tooltip"
            data-placement="bottom"
            title="表示されているデータをCSVファイルに出力します。"
            data-placement="top"
            >ExportCSV</a><span></span>

        <label
            for="csv_file"
            class="btn btn-primary btn-xs"
            style="min-width: 250px;"
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
                style="min-width: 100px;"
                data-toggle="tooltip"
                data-placement="bottom"
                title="選択したCSVファイルを取り込みます。先に取り込むファイルを指定してください。"
                data-placement="top"
                onclick="return checkFile(document.getElementById('csv_file').value);"
                >ImportCSV</button><span></span>
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
    }
</script>