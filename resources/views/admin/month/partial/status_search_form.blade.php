<style type="text/css">
	.btn-group .btn{
		min-width: 100px;
	}
</style>

<form method="GET" action="{{route('admin::super::month::search', ['id'=>$id])}}">

<div class="modal fade" id="search" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary-important">
				<button type="button" class="close" data-dismiss="modal"><span>×</span></button>
				<h4 class="modal-title">検索条件を指定する</h4>
			</div>
			<div class="modal-body">


	<div class="col-md-3 col-md-offset-1 text-center"><label>サイクル</label></div>
	<div class="col-md-7" style="margin-bottom: 10px;">
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="cycle" value=""        checked> 指定なし</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="cycle" value="daily"   @if(isset($cycle) && $cycle === 'daily') checked @endif> 日次</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="cycle" value="monthly" @if(isset($cycle) && $cycle === 'monthly') checked @endif> 月次</label>
		</div>
	</div>

	<div class="col-md-3 col-md-offset-1 text-center"><label>CSVファイル</label></div>
	<div class="col-md-7" style="margin-bottom: 10px;">
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="exist" value="" checked> 指定なし</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="exist" value="1" @if(isset($exist) && $exist === '1') checked @endif> 存在する</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="exist" value="0" @if(isset($exist) && $exist === '0') checked @endif> 存在しない</label>
		</div>
	</div>

	<div class="col-md-3 col-md-offset-1 text-center"><label>処理結果</label></div>
	<div class="col-md-7" style="margin-bottom: 10px;">
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="import" value=""  checked> 指定なし</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="import" value="1" @if(isset($import) && $import === '1') checked @endif> 処理済み</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="import" value="0" @if(isset($import) && $import === '0') checked @endif> 未処理</label>
		</div>
	</div>

	<div class="col-md-3 col-md-offset-1 text-center"><label>累積</label></div>
	<div class="col-md-7" style="margin-bottom: 10px;">
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="cumulative" value=""  checked> 指定なし</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="cumulative" value="1" @if(isset($cumulative) && $cumulative === '1') checked @endif> 累積する</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="cumulative" value="0" @if(isset($cumulative) && $cumulative === '0') checked @endif> 累積しない</label>
		</div>
	</div>

	<div class="col-md-3 col-md-offset-1 text-center"><label>分割</label></div>
	<div class="col-md-7" style="margin-bottom: 10px;">
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="split" value=""  checked> 指定なし</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="split" value="1" @if(isset($split) && $split === '1') checked @endif> 分割する</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="split" value="0" @if(isset($split) && $split === '0') checked @endif> 分割しない</label>
		</div>
	</div>

	<div class="col-md-3 col-md-offset-1 text-center"><label>処理状態</label></div>
	<div class="col-md-7" style="margin-bottom: 10px;">
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="process" value=""  checked> 指定なし</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="process" value="1" @if(isset($process) && $process === '1') checked @endif> 処理対象</label>
			<label class="btn btn-default btn-sm"><input type="radio" autocomplete="off" name="process" value="0" @if(isset($process) && $process === '0') checked @endif> 処理対象外</label>
		</div>
	</div>

	<div class="col-md-3 col-md-offset-1 text-center"><label>テーブル名</label></div>
	<div class="col-md-7">
		<div class="form-group" style="margin-bottom: 10px;">
			<input type="text" name="table" @if(isset($table)) value="{{$table}}" @endif class="form-control input-sm">
		</div>
	</div>

	<div class="col-md-3 col-md-offset-1 text-center"><label>データ名</label></div>
	<div class="col-md-7" style="margin-bottom: 10px;">
		<div class="form-group">
			<input type="text" name="file" @if(isset($file)) value="{{$file}}" @endif class="form-control input-sm">
		</div>
	</div>
			</div>
			<div class="modal-footer">
				<div class="col-md-10 col-md-offset-1">
					<div class="btn-group">
						<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">閉じる</button>
						<a href="{{route('admin::super::month::status', ['id'=>$id])}}" class="btn btn-warning btn-sm">検索結果をリセットする</a>
						<button type="submit" class="btn btn-success btn-sm">検索する</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</form>
