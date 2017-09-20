<div class="row process-breadcrumbs">
    
    <div class="col-md-3" style="padding: 0 5px;">
        <div
            @if(strpos(\Request::path(), '/copy_confirm/')) class="panel panel-warning"
            @else                                           class="panel panel-primary" @endif
            style="font-size: 70%; margin-bottom: 5px;"
        >
            <div class="panel-heading" style="padding: 10px 15px; border-bottom: none;">1.コピー前チェック</div>
        </div>
    </div>

    <div class="col-md-3" style="padding: 0 5px;">
        <div
            @if(strpos(\Request::path(), '/copy/'))             class="panel panel-warning active"
            @elseif(strpos(\Request::path(), '/copy_confirm/')) class="panel panel-default"
            @else                                               class="panel panel-primary" @endif
            style="font-size: 70%; margin-bottom: 5px;"
        >
            <div class="panel-heading" style="padding: 10px 15px; border-bottom: none;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> 2.コピー処理</div>
        </div>
    </div>

    <div class="col-md-3" style="padding: 0 5px;">
        <div
            @if(strpos(\Request::path(), '/import_confirm/'))   class="panel panel-warning active"
            @elseif(strpos(\Request::path(), '/copy_confirm/')) class="panel panel-default"
            @elseif(strpos(\Request::path(), '/copy/'))         class="panel panel-default"
            @else                                               class="panel panel-primary" @endif
            style="font-size: 70%; margin-bottom: 5px;"
        >
            <div class="panel-heading" style="padding: 10px 15px; border-bottom: none;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> 3.処理ファイル選択</div>
        </div>
    </div>

    <div class="col-md-3" style="padding: 0 5px;">
        <div
            @if(strpos(\Request::path(), '/import/')) class="panel panel-warning active"
            @else                                     class="panel panel-default" @endif
            style="font-size: 70%; margin-bottom: 5px;"
        >
            <div class="panel-heading" style="padding: 10px 15px; border-bottom: none;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> 4.アップロード処理</div>
        </div>
    </div>

</div>