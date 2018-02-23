<html>
<head>
    <meta http-equiv="content-language" content="ja">
    <meta charset="UTF-8">
    @yield('meta')

    <title>
        @if(env('APP_ENV') !== "product") [{{env('APP_ENV')}}] @endif
        @yield('title'){{$configs['title'] or ''}}
    </title>

    <link rel="stylesheet" href="https://nkmr6194.github.io/Umi/css/bootstrap.css"></link>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://nkmr6194.github.io/Umi/js/bootstrap.min.js"></script>


    {{-- マテリアルデザインがクソかっこいい --}}
{{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons">
<link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.0.0-beta.3/dist/css/bootstrap-material-design.min.css" integrity="sha384-k5bjxeyx3S5yJJNRD1eKUMdgxuvfisWKku5dwHQq9Q/Lz6H8CyL89KF52ICpX4cL" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://unpkg.com/popper.js@1.12.5/dist/umd/popper.js" integrity="sha384-KlVcf2tswD0JOTQnzU4uwqXcbAy57PvV48YUiLjqpk/MJ2wExQhg9tuozn5A1iVw" crossorigin="anonymous"></script>
<script src="https://unpkg.com/bootstrap-material-design@4.0.0-beta.3/dist/js/bootstrap-material-design.js" integrity="sha384-hC7RwS0Uz+TOt6rNG8GX0xYCJ2EydZt1HeElNwQqW+3udRol4XwyBfISrNDgQcGA" crossorigin="anonymous"></script> --}}

<style type="text/css">
body {word-wrap: break-word; overflow-y: scroll;}
th,td{text-align: center;}
table{width: 100%;}

.table-small th,
.table-small th *,
.table-small td,
.table-small td * {font-size: 12px;}
small, .text-sm {font-size: 80% !important;}
.margin-0{margin: 0 !important;}
.va-middle,.va-middle *{vertical-align: middle !important;}
.margin-bottom{margin-bottom: 10px;}
p{margin: 3px 0;}
input[type=file]{display: none;}
.max-width-100{max-width: 100px; width: 100%;}
.max-width-150{max-width: 150px; width: 100%;}
.max-width-200{max-width: 200px; width: 100%;}
.max-width-250{max-width: 250px; width: 100%;}
.max-width-300{max-width: 300px; width: 100%;}
.max-width-400{max-width: 300px; width: 100%;}
.max-width-500{max-width: 300px; width: 100%;}
.max-width-600{max-width: 300px; width: 100%;}
.margin-left-auto{margin-left: auto;}
.bg-primary-important{border: #2c3e50 !important; background: #2c3e50 !important; color: #fff;};
.bg-primary-important *{color: #fff;}

.bg-success-important{border: #18bc9c !important; background: #18bc9c !important; color: #fff;};
.bg-success-important *{color: #fff;}

.bg-primary-important .close,
.bg-primary-important .close:hover{ color: #fff; }

.close{opacity: 0.6; outline: none;}
.tooltip-inner{min-width: 200px; max-width: 300px;}

.text-primary-light {color: #597ea2;}
.text-danger-light  {color: #f08b80;}
.text-warning-light {color: #f7bb5b;}
.text-info-light    {color: #75b9e7;}
.text-warning-light {color: #fbb23d;}

.bolder       {font-weight: bolder;}
.border-bottom{border-bottom: 2px solid #aaa; margin-bottom: 15px;}

.list-divider{border-bottom: none; padding: 1px; /*background: #fcfcfc;*/}
.list-second {padding-left: 30px; font-size: 0.8em;}
.list-group-item:last-child{ margin: -1px; }
.user-name   {border-bottom: none; color: #444; cursor: default;}
.user-name:hover{text-decoration: none;}

.label{display: inline-block; padding: 5px 10px;}

.modal-dialog{ width: 650px; }

.pagination{ margin: 0; }

.progress{ background: #ddd; margin-bottom: 0;}

.btn-success{ border-color: #00a383; }
.btn-primary{ border-color: #132537; }
.btn-danger { border-color: #ce3323; }
.btn-warning{ border-color: #da8300; }
.btn-info   { border-color: #1b7fc2; }
.tooltip.top{ background: rgba(255,255,255,0); }

/*.btn-group .btn{ margin-left: 2px; }*/
.brand-logo, .official-logo, .gw-logo { 
    background-size: 100% 100%;
    background-repeat: no-repeat;
    cursor: pointer;
    transition: 0.4s;
}
.brand-logo{    background-image: url({{asset('/logos/logo_02.png')}}); width: 200px; height: 33px; margin-bottom: 0px; }
.official-logo{ background-image: url({{asset('/logos/official.png')}}); width: 90px; height: 30px; }
.gw-logo{       background-image: url({{asset('/logos/groupware.png')}}); width: 120px; height: 30px; }
.brand-logo:hover{    background-image: url({{asset('/logos/logo_02_hover.png')}}); }
.official-logo:hover{ background-image: url({{asset('/logos/official_hover.png')}}); }
.gw-logo:hover{       background-image: url({{asset('/logos/groupware_hover.png')}}); }

.alert-fixed { width: 300px; padding: 10px; padding-right: 30px; font-size: 80%; z-index: 2; left: 20px; position: fixed; bottom: 40px; margin-bottom: 0; max-height: 400px; overflow-y: scroll; };
.margin-bottom{margin-bottom: 10px;}
.tooltip-inner{ text-align: left; }
.list-group-item:last-child{ margin: 0; }
.navbar-nav > li > .dropdown-menu{ margin-top: -10px; }
.dropdown-menu{ min-width: 200px; }
input[type="checkbox"], input[type="radio"] { width: 16px; height: 16px; }

</style>
</head>
<body class="no-thank-yu">
    @section('header')
    <div class="bs-component" style="margin-bottom: 120px;">
        @include('partial.nav')
    </div>
    @show

    <div class="container-fluid">
        <div class="row">
            @section('sidebar')
            @show

            @yield('content')
        </div>
    </div>

    @section('footer')
    <script src="{{asset('/js/js.cookie.js')}}"></script>

    <script type="text/javascript">
        $(function () {
            $('.modal-content').draggable();
            $('[data-toggle="tooltip"]').tooltip();
            $('.min-width').each(function(){
                var width = $(this).attr('data-size');
                $(this).css('min-width', width + 'px');
            });

            $(document).bind("ajaxSend", function(c, xhr) {
                $(window).bind( 'beforeunload', function() {
                    alert('abort');
                    xhr.abort();
                })
            });

                // +==========================================================
                // | class='btn-group' && data-toggle='buttons'に対して
                // | チェックされたボタンをハイライト表示する関数
                // | class='btn'内のdata-color属性に変えたい色のボタンクラスを与えることで
                // | 好きな色に変更できる(デフォルト値はbtn-primary)
                $('.btn-group[data-toggle="buttons"] .btn input:checked').each(function(){
                    var btn = $(this).parent('.btn');
                    var color = $(this).parent('.btn').attr('data-color');
                    console.log(color);
                    if(color == null) color = 'btn-primary';
                    btn.removeClass('btn-default').addClass(color);
                });
                $('.btn-group[data-toggle="buttons"] .btn').click(function(){
                    var obj = $(this).siblings();
                    obj.each(function(){
                        var color = $(this).attr('data-color');
                        if(color == null) color = 'btn-primary';
                        $(this).removeClass(color).addClass('btn-default');
                    });
                    var color = $(this).attr('data-color');
                    if(color == null) color = 'btn-primary';

                    $(this).removeClass('btn-default').addClass(color);
                });

                // +==========================================================
                // | ページ遷移してもアコーディオンパネルを開いたままにしておく関数
                // | Cookieに開いたタブ情報があればinクラスを与えて開いた状態にする
                // +==========================================================
                // Cookie情報の読み込み
                var cookie_array = [];
                var raw_tabs = Cookies.get('activeAccordionGroup'); /* console.log(raw_tabs); */
                // 配列情報が文字列で取得されるため、不要な文字を削除して配列に変換する
                if(raw_tabs != null) {
                    raw_tabs     = raw_tabs.replace(/\[/g, '').replace(/]/g, '').replace(/"/g, '');
                    var tabs     = raw_tabs.split(',');
                    $.each(tabs, function(i, tab){
                        // console.log(i + ' : ' + tab);
                        $(tab).addClass('in');
                    });
                    var cookie_array = tabs; console.log('cookie_array : ' + cookie_array);
                }
                // cookie情報を扱う配列にセット
                // 注意：この時点ではまだcookieを操作していない
                // #### アコーディオンパネルをクリックしたタイミングで発火する ####
                $('.collapse[data-toggle="collapse"]').click(function() {
                    // 共通処理：idを取得してcookie配列に追加
                    var id = $(this).attr('href');
                    if(id != null && cookie_array.indexOf(id) == -1) cookie_array.push(id);
                    // タブが閉じられたときの処理：アクティブなタブとその子タブを配列から削除する
                    if($(this).next().hasClass('in')){  console.log('close -> ' + id);
                    var remove_id = getGroupId(id);
                        // 削除する要素を検出して子要素を全て消す
                        if(cookie_array.length == 0) { /*console.log('undefined');*/ return false; }
                        $.each(cookie_array, function(i, buf) {
                            //console.log(buf + ' <=> ' + remove_id);
                            if(buf == null) return false;
                            if(buf.indexOf(remove_id) != -1) cookie_array.splice(i /* i番目の要素から */, 1 /* つ削除 */);
                        });
                    }
                    // console.log(cookie_array);
                    // 改めてcookie情報としてセットする
                    Cookies.set('activeAccordionGroup', cookie_array, { expires: 7 });
                });
            });
function getGroupId(id) {
    var tmp_id = id.replace(/#/g, '');
    var arr_id = tmp_id.split('_');
    if(!(arr_id instanceof Array) || arr_id.length < 2) return false;
    return arr_id[0];
}
</script>
@show
</body>
</html>