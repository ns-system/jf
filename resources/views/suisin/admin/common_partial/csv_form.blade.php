{{-- CSVファイルエクスポート・インポート --}}
OLDDD
<form class="form-horizontal" role="form" method="POST" action="{{$configs['import_route']}}" enctype="multipart/form-data">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="row margin-bottom">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            <div class="btn-group" style="display: block;">
                <a
                    class="btn btn-success btn-sm"
                    href="{{$configs['export_route']}}"
                    style="width: 25%"
                    data-toggle="tooltip"
                    title="表示されているデータをCSVファイルに出力します。"
                    data-placement="top"
                    >ExportCSV</a>

                <label
                    for="csv_file"
                    class="btn btn-primary btn-sm"
                    style="width: 50%;"
                    data-toggle="tooltip"
                    title="取り込みを行うCSVファイルを選択してください。"
                    data-placement="top"
                    >
                    <span id="file_name">ファイルを選択してください</span>
                    <input type="file"
                           name="csv_file"
                           id="csv_file"
                           onchange="setFileName(document.getElementById('csv_file').value);"
                           >
                </label>

                <button type="submit"
                        class="btn btn-warning btn-sm"
                        style="width: 25%"
                        data-toggle="tooltip"
                        title="選択したCSVファイルを取り込みます。先に取り込むファイルを指定してください。"
                        data-placement="top"
                        onclick="return checkFile(document.getElementById('csv_file').value);"
                        >ImportCSV</button>
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
        var file_name = tmp[tmp.length - 1]
        $('#file_name').html(file_name);
    }
</script>