<html>
    <head>
        <meta http-equiv="content-language" content="ja">
        <meta charset="UTF-8">

        <title>@yield('title'){{$configs['title'] or ''}}</title>

 {{--        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet"> --}}
        <link rel="stylesheet" href="https://nkmr6194.github.io/Umi/css/bootstrap.css"></link>


        <style type="text/css">
            body{word-wrap: break-word; overflow-y: scroll;}
            th,
            td
            {
                text-align: center;
            }
            table{
                width: 100%;
            }

            .table-small th,
            .table-small th *,
            .table-small td,
            .table-small td *
            {
                font-size: 12px;
            }
            small{font-size: 80% !important;}
            .margin-0{margin: 0 !important;}
            .va-middle,
            .va-middle *{
                vertical-align: middle !important;
            }
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

            .bg-primary-important .close,
            .bg-primary-important .close:hover{ color: #fff; }

            .close{opacity: 0.6; outline: none;}
            .tooltip-inner{min-width: 200px; max-width: 300px;}

            .small{font-size: 75% !important; font-weight: bolder;}
/*            .label-default{ background: #668; }*/


            .text-primary-light{color: #597ea2;}
            .text-danger-light{color: #f08b80;}
            .text-warning-light{color: #f7bb5b;}
            .text-info-light{color: #75b9e7;}
            .text-warning-light{color: #3ae6c4;}

            .bolder{font-weight: bolder;}
            .border-bottom{border-bottom: 2px solid #aaa; margin-bottom: 15px;}

            .list-divider{border-bottom: none; padding: 1px; /*background: #fcfcfc;*/}
            .list-second{padding-left: 30px; font-size: 0.8em;}

            .user-name{border-bottom: none; color: #444; cursor: default;}
            .user-name:hover{text-decoration: none;}

            .label{display: inline-block; padding: 5px 10px;}

            .modal-dialog{ width: 650px; }

            .pagination{ margin: 0; }
        </style>
    </head>
    <body class="no-thank-yu">
        @section('header')
        <div class="bs-component" style="margin-bottom: 120px;">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="http://192.168.10.10">@yield('brand'){{$configs['brand'] or ''}}</a>
                    </div>

                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li><a href="http://www.jf-nssinren.or.jp/" target="_blank">Official</a></li>
                            <li><a href="http://192.1.10.136/myweb10po" target="_blank">Groupware</a></li>
                            <li><a href="http://cvs.phpmyadmin" target="_blank">pma</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            @if(Auth::check())


                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                                    <div class="media" style="height: 40px; width: 40px; border-radius: 20%; background: #eee;">
                                        <img style="width: 100%; height: 100%;" @if(\Auth::user()->user_icon != '') src="{{asset('/user_icon/' . \Auth::user()->user_icon)}}" @else src="{{asset('/user_icon/unset.png')}}" @endif>
                                    </div>

{{-- 
<div class="media" style="width: 250px;">
    <div class="col-xs-4" style="height: 50px; width: 50px; padding: 0;">
        <img style="width: 100%; height: 100%;" src="http://free-designer.net/design_img/0216053006.jpg">
    </div>
    <div class="col-xs-6">{{Auth::user()->name}}<small>さん</small> <span class="caret"></div>
</div>
 --}}
{{--                                 <span class="caret"></span></a> --}}
{{--                                 <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">{{Auth::user()->name}}<small>さん</small> <span class="caret"></span></a> --}}
                                <div class="dropdown-menu list-group" role="menu" style="font-size: 80%; padding: 0; border-radius: 5px;">
                                    <span class="list-group-item user-name">{{Auth::user()->name}}<small>さん</small></span>
                                    <a href="{{route('app::user::show', ['id'=>\Auth::user()->id])}}" class="list-group-item"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> ユーザー情報確認</a>
                                    <a href="/auth/logout" class="list-group-item"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> ログアウト</a>
                        </ul>
                        </li>
                        @endif
                        </ul>
                    </div>
                </div>
            </nav>
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="https://nkmr6194.github.io/Umi/js/bootstrap.min.js"></script>
{{--         <script src="{{asset('/toggle-button/js/bootstrap-toggle.min.js')}}"></script> --}}
        <script type="text/javascript">
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
                $('.min-width').each(function(){
                    var width = $(this).attr('data-size');
                    $(this).css('min-width', width + 'px');
                });
            });

            /**
             * class='btn-group' && data-toggle='buttons'に対して
             * チェックされたボタンをハイライト表示する関数
             * class='btn'内のdata-color属性に変えたい色のボタンクラスを与えることで
             * 好きな色に変更できる(デフォルト値はbtn-primary)
             */
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
//                console.log(color);
                if(color == null) color = 'btn-primary';

                $(this).removeClass('btn-default').addClass(color);
            });

        </script>
        @show

    </body>
</html>