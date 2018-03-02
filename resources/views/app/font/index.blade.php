@extends('layout')

@section('title', 'フォント')

@section('header')
@parent
@section('brand', 'ホーム')
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

        <div class="row">
            <div class="col-md-4">
                <form method="POST" action="{{ route('app::user::font::update', ['user_id'=>$user_id]) }}">
                    {{ csrf_field() }}
                    <div class="panel panel-primary">
                        <div class="panel-heading">フォント選択</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>フォント</label>
                                <select class="form-control" size="4" name="font" id="font">
                                    @foreach($fonts as $font)
                                    <option value="{{ $font['name'] }}" style="font-family: '{{ $font['name'] }}' !important;">{{ $font['name_jp'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>フォントサイズ</label>
                                <select class="form-control" size="4" name="font_size" id="font-size">
                                    @foreach($sizes as $size)
                                    <option value="{{ $size }}" style="font-size: {{ $size }}px;">{{ $size }}px</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>フォント太さ</label>
                                <select class="form-control" size="4" name="font_weight" id="font-weight">
                                    @foreach($bolds as $bold)
                                    <option value="{{ $bold }}" style="font-weight: {{ $bold }};">{{ $bold }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>フォント色</label>
                                <input type="color" class="form-control" id="font-color" placeholder="色" name="font_color">
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="submit" class="btn btn-primary">変更する</button>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="col-md-8">
                <h2 class="border-bottom">表示サンプル</h2>
            </div>

            <div class="col-md-8 well" id="preview">
                <h1 class="text-center">銀河鉄道の夜</h1>
                <h2 class="text-right">宮沢賢治 <small> - みやざわけんじ</small></h2>
                <h3 class="text-center">一　午後の授業</h3>
                <div class="col-md-10 col-md-offset-1">
                    <p>「ではみなさんは、そういうふうに川だと言われたり、乳の流れたあとだと言われたりしていた、このぼんやりと白いものがほんとうは何かご承知ですか」先生は、黒板につるした大きな黒い星座の図の、上から下へ白くけぶった銀河帯のようなところを指しながら、みんなに問いをかけました。</p>
                    <p>　カムパネルラが手をあげました。それから四、五人手をあげました。ジョバンニも手をあげようとして、急いでそのままやめました。たしかにあれがみんな星だと、いつか雑誌で読んだのでしたが、このごろはジョバンニはまるで毎日教室でもねむく、本を読むひまも読む本もないので、なんだかどんなこともよくわからないという気持ちがするのでした。</p>
                    <p>　ところが先生は早くもそれを見つけたのでした。</p>
                    <p>「ジョバンニさん。あなたはわかっているのでしょう」</p>
                    <p>　ジョバンニは勢いよく立ちあがりましたが、立ってみるともうはっきりとそれを答えることができないのでした。ザネリが前の席からふりかえって、ジョバンニを見てくすっとわらいました。ジョバンニはもうどぎまぎしてまっ赤になってしまいました。先生がまた言いました。</p>
                    <p>「大きな望遠鏡で銀河をよっく調べると銀河はだいたい何でしょう」</p>
                    <p>　やっぱり星だとジョバンニは思いましたが、こんどもすぐに答えることができませんでした。</p>
                    <p>　先生はしばらく困ったようすでしたが、眼をカムパネルラの方へ向けて、</p>
                    <p>「ではカムパネルラさん」と名指しました。</p>
                    <p>　するとあんなに元気に手をあげたカムパネルラが、やはりもじもじ立ち上がったままやはり答えができませんでした。</p>
                    <p>　先生は意外なようにしばらくじっとカムパネルラを見ていましたが、急いで、</p>
                    <p>「では、よし」と言いながら、自分で星図を指しました。</p>
                    <p>「このぼんやりと白い銀河を大きないい望遠鏡で見ますと、もうたくさんの小さな星に見えるのです。ジョバンニさんそうでしょう」</p>
                    <p>　ジョバンニはまっ赤になってうなずきました。けれどもいつかジョバンニの眼のなかには涙がいっぱいになりました。そうだ僕は知っていたのだ、もちろんカムパネルラも知っている、それはいつかカムパネルラのお父さんの博士のうちでカムパネルラといっしょに読んだ雑誌のなかにあったのだ。それどこでなくカムパネルラは、その雑誌を読むと、すぐお父さんの書斎から巨きな本をもってきて、ぎんがというところをひろげ、まっ黒な頁いっぱいに白に点々のある美しい写真を二人でいつまでも見たのでした。それをカムパネルラが忘れるはずもなかったのに、すぐに返事をしなかったのは、このごろぼくが、朝にも午後にも仕事がつらく、学校に出てももうみんなともはきはき遊ばず、カムパネルラともあんまり物を言わないようになったので、カムパネルラがそれを知ってきのどくがってわざと返事をしなかったのだ、そう考えるとたまらないほど、じぶんもカムパネルラもあわれなような気がするのでした。</p>
                    <p>　先生はまた言いました。</p>
                    <p>「ですからもしもこの天の川がほんとうに川だと考えるなら、その一つ一つの小さな星はみんなその川のそこの砂や砂利の粒にもあたるわけです。またこれを巨きな乳の流れと考えるなら、もっと天の川とよく似ています。つまりその星はみな、乳のなかにまるで細かにうかんでいる脂油の球にもあたるのです。そんなら何がその川の水にあたるかと言いますと、それは真空という光をある速さで伝えるもので、太陽や地球もやっぱりそのなかに浮かんでいるのです。つまりは私どもも天の川の水のなかに棲んでいるわけです。そしてその天の川の水のなかから四方を見ると、ちょうど水が深いほど青く見えるように、天の川の底の深く遠いところほど星がたくさん集まって見え、したがって白くぼんやり見えるのです。この模型をごらんなさい」</p>
                    <p>　先生は中にたくさん光る砂のつぶのはいった大きな両面の凸レンズを指しました。</p>
                    <p>「天の川の形はちょうどこんななのです。このいちいちの光るつぶがみんな私どもの太陽と同じようにじぶんで光っている星だと考えます。私どもの太陽がこのほぼ中ごろにあって地球がそのすぐ近くにあるとします。みなさんは夜にこのまん中に立ってこのレンズの中を見まわすとしてごらんなさい。こっちの方はレンズが薄いのでわずかの光る粒すなわち星しか見えないでしょう。こっちやこっちの方はガラスが厚いので、光る粒すなわち星がたくさん見えその遠いのはぼうっと白く見えるという、これがつまり今日の銀河の説なのです。そんならこのレンズの大きさがどれくらいあるか、またその中のさまざまの星についてはもう時間ですから、この次の理科の時間にお話します。では今日はその銀河のお祭りなのですから、みなさんは外へでてよくそらをごらんなさい。ではここまでです。本やノートをおしまいなさい」</p>
                    <p>　そして教室じゅうはしばらく机の蓋をあけたりしめたり本を重ねたりする音がいっぱいでしたが、まもなくみんなはきちんと立って礼をすると教室を出ました。</p>
                </div>

            </div>

        </div>
    </div><!-- .container-fluid -->
</div>
@endsection

@section('footer')
<script type="text/javascript">
    $(function(){
        $('#font-size').click(function(){
            var size = $(this).val();
            $('#preview').css({'font-size':size+'px'});
        });
        $('#font').click(function(){
            var font = $(this).val();
            $('#preview').css({'font-family': '"'+font});
            $('#preview h1').css({'font-family': '"'+font+'"'});
            $('#preview h2').css({'font-family': '"'+font+'"'});
            $('#preview h3').css({'font-family': '"'+font+'"'});
            $('#font-size').css({'font-family': '"'+font+'"'});
            $('#font-weight').css({'font-family': '"'+font+'"'});
            $('#font-color').css({'font-family': '"'+font+'"'});
        });
        $('#font-weight').click(function(){
            var weight = $(this).val();
            $('#preview').css({'font-weight':weight});
        });
        $('#font-color').change(function(){
            var color = $(this).val();
            $('#preview').css({'color':color});
        });
    })
</script>
@parent
@endsection