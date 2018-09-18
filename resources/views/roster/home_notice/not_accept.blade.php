@if(!empty($not_accepts))
<div class="well">
  <h2 class="border-bottom pointer" data-toggle="collapse" data-target="#collapseExample">
    @include('partial.bell')
    未入力・未承認分
    <b class="badge">{{ $not_accepts->count() }}</b>
  </h2>

  <div class="collapse" id="collapseExample">
    @include('partial.not_accept', ['not_accepts'=>$not_accepts, 'is_chief'=>$is_chief, 'is_admin'=>false])
  </div>
</div>
@endif