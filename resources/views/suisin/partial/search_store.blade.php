
<select name="control" id="control_stores"> 
	<option value="0" selected="selected">全地区</option>
	@foreach($controls as $control_store)
	<option
		value="{{$control_store->control_store_code}}"
		@if($control == $control_store->control_store_code)
			selected="selected"
	@endif>{{$control_store->control_store_name}}</option>
	@endforeach
</select>
<select name="small" id="small_stores"> 
	<option value="0" data-value="0" selected="selected">全地区</option>
	@foreach($smalls as $small_store)
		<option
			value="{{$small_store->small_store_number}}"
			data-value="{{$small_store->control_store_code}}"
			@if($small == $small_store->small_store_number)
				selected="selected"
			@endif
			@if($control != 0 && $small_store->control_store_code != $control)
				style="display: none;"
			@endif
			>{{$small_store->small_store_name}}</option>
	@endforeach
</select>