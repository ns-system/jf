<style>
.notification-wrap { color: #fff; }
.white { color: #fff; }
</style>

<div>
  @if(empty($notifications) || $notifications->isEmpty())
  <div class="row">
    <div class="col-md-8 col-md-offset-2 @if(!empty($dark)) white @endif">
      <p>新しいお知らせはありません。</p>
    </div>
  </div>
  @else
  <div id="sampleCarousel" class="carousel slide" data-ride="carousel" style="padding: 20px 0;">
    <div class="carousel-inner" role="listbox">
      @foreach($notifications as $i => $n)
      <div @if($i == 0) class="item active" @else class="item" @endif>
        <div class="row">
          <div class="col-md-8 col-md-offset-2 @if(!empty($dark)) white @endif">

            <table @if(!empty($dark)) class="white" @endif>
              <tr>
                <td width="75px" style="padding-left: 5px;"><span class="label label-info">{{ date('n月j日', strtotime($n->created_at)) }}</span></td>
                <td width="75px" style="padding-left: 5px;"><span class="label label-warning">{{ $n->category }}</span></td>
                <td width="90%" class="text-left" style="padding-left: 5px;">
                  <span>{{ $n->message }}</span>
                  <i> - {{ $n->user->last_name }}</i>
                </td>
              </tr>
            </table>


          </div>
        </div>
      </div>
      @endforeach
    </div>
    <a class="left carousel-control" href="#sampleCarousel" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">前へ</span>
    </a>
    <a class="right carousel-control" href="#sampleCarousel" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">次へ</span>
    </a>
  </div>
  @endif
</div>