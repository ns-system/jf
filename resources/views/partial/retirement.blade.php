<?php $hidden = (!empty($hidden)) ? $hidden : false; ?>

@if($retirement)
<span class="glyphicon glyphicon-user text-danger" data-toggle="tooltip" title="退職済み" aria-hidden="true"></span>
@elseif($hidden)
<span class="glyphicon glyphicon-user text-muted" data-toggle="tooltip" title="勤怠除外" aria-hidden="true"></span>
@else
<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
@endif