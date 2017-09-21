<div class="text-right">
<span class="btn-group" data-toggle="buttons" style="margin-bottom: 5px;">
    <input type="hidden" name="id[{{$r->key_id}}]" value="{{$r->key_id}}">
    <label class="btn btn-xs btn-default" data-color="btn-success"><input type="checkbox" name="actual[{{$r->key_id}}]" value="1">承認</label>
    <label class="btn btn-xs btn-default" data-color="btn-danger" ><input type="checkbox" name="actual[{{$r->key_id}}]" value="0">却下</label>
    <input class="form-control input-sm" type="text" name="actual_reject[{{$r->key_id}}]" placeholder="却下理由（任意）" style="display: inline-block; width: 200px; padding: 5px; height: 22px; border-left: none;">
</span>
</div>