
<div class="col-md-10 col-md-offset-1">
<form class="form-horizontal" role="form" method="POST" action="{{route('app::roster::user::edit', ['$id'=>$id])}}">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">


    <p>
    <label>部署</label>
    <select class="form-control input-sm" name="division_id">
    	@foreach($divs as $div)
    		<option value="{{$div->division_id or ''}}" @if($div_id == $div->division_id) selected @endif>{{$div->division_name or ''}}</option>
    	@endforeach
    </select>
    </p>

    <p>
    <label>標準勤務形態</label>
    <select class="form-control input-sm" name="work_type_id">
		<option value="">勤務形態を選択してください（責任者の場合、選択不要です）</option>
    	@foreach($types as $type)
    		<option value="{{$type->work_type_id or ''}}" @if($type_id == $type->work_type_id) selected @endif>
    			{{$type->work_type_name or ''}}
                @if(!empty($type->work_start_time) && !empty($type->work_end_time))
                （{{date('G:i', strtotime($type->work_start_time))}} ～ {{date('G:i', strtotime($type->work_end_time))}}）
                @endif
    		</option>
    	@endforeach
    </select>
   	</p>
    
	<p class="text-right"><button type="submit" class="btn btn-primary">更新する</button></p>
    
</form>
</div>