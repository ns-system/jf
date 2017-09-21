<form class="form-horizontal" role="form" method="POST" action="/roster/add/user/add">
    {{-- CSRF対策--}}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="form-group">
        <label class="control-label">所属部署</label>
        <select name="division_id" class="form-control">
            @foreach($divisions as $div)
            <option value="{{$div->division_id}}">{{$div->division_name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="control-label">標準勤務形態</label>
        <select name="work_type_id" class="form-control">
            @foreach($work_types as $type)
            <option value="{{$type->work_type_id}}">{{$type->work_type_name}}（{{$type->work_start_time}} - {{$type->work_end_time}}）</option>
            @endforeach
        </select>
    </div>

    <div class="text-right">
        <button type="submit" class="btn btn-primary">登録する</button>
    </div>

    <p class="alert alert-info">所属長の場合、以下の項目を入力してください。</p>

    <div class="form-group">
        <label>決裁者</label>
        <div class="checkbox">
            <label class="control-label">
                <input type="hidden" name="is_chief" value="">
                <input type="checkbox" name="is_chief">私は所属長です。
            </label>
        </div>
    </div>

    @for($i = 1; $i <= 5; $i++)
    <div class="form-group">
        <label class="control-label">管轄部署{{$i}}</label>
        <select name="control_division_id[]" class="form-control">
            <option value=""></option>
            @foreach($divisions as $div)
            <option value="{{$div->division_id}}">{{$div->division_name}}</option>
            @endforeach
        </select>
    </div>
    @endfor

    <div class="text-right">
        <button type="submit" class="btn btn-primary">登録する</button>
    </div>
</form>
