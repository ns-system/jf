@if($rows->isEmpty())
<div class="alert alert-danger" role="alert">データが見つかりませんでした。</div>
@else
<table class="table va-middle table-small">
	<thead>
		<tr>
			<th class="bg-primary">
				<p>区分</p>
				<p>データ名</p>
			</th>
			<th class="bg-primary">
				<p>CSVファイル名</p>
				<p>MySQL内レコード件数</p>
{{-- 				<p>データ名</p> --}}
			</th>
			<th class="bg-primary">
				<p>DB名</p>
				<p>テーブル名</p>
			</th>

			<th class="bg-primary">
				<p>サイクル</p>
				<p>目安還元日</p>
			</th>

			<th class="bg-primary">
				<p>処理状態</p>
				<p>処理結果</p>
			</th>
			<th class="bg-primary">
				<p>累積</p>
				<p>分割</p>
			</th>
			<th class="bg-primary">処理時刻</th>

		</tr>
	</thead>
	<tbody>
	@foreach($rows as $row)
		<tr>
			<td class="text-left">
				<p>{{$row->data_type_name or '登録されていません'}}</p>
				<p>{{$row->zenon_data_name}}</p>
			</td>
			<td class="text-left">
				<p>{{$row->csv_file_name}}</p>
				<p>
					@if($row->is_exist) <span class="label label-primary">あり</span>
					@else <span class="label label-danger">なし</span> @endif
				@if(isset($row->database_name) && isset($row->table_name))
					<?php
					try {
						$sql = "SELECT COUNT(*) AS row_count FROM `{$row->database_name}`.`$row->table_name` WHERE `monthly_id`=?;";
						$res = \DB::select($sql, [$id]);
						if($res != null){
							foreach ($res as $r) {
								echo number_format($r->row_count) . "件";
							}
						}else{
							echo "0件";
						}
					} catch (Exception $e) {
							echo "0件";
					}
						// var_dump($res);
						// foreach ($res as $key => $value) {
						// 	var_dump($value);
						// }
					?>
				@else 0件
				@endif</p>
			</td>
			<td class="text-left">
				<p>{{$row->database_name or 'DBが登録されていません'}}</p>
				<p>{{$row->table_name or 'テーブルが登録されていません'}}</p>
			</td>
			<td class="text-left">
				<p>
					@if($row->is_monthly) <span class="label label-info">月次</span>
					@else <span class="label label-success">日次</span> @endif
				</p>
				<p>{{$row->reference_return_date or '登録されていません'}}</p>
			</td>

			<td class="text-left">
				<p>
					@if($row->is_process) <strong class="text-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 処理対象</strong>
					@else                 <strong class="text-danger" ><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> 処理対象外</strong> @endif
				</p>
				<p>
					@if($row->is_import) <strong class="text-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 完了</strong>
					@else                <strong class="text-danger" ><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 未処理</strong> @endif
				</p>
			</td>
			<td class="text-left">
				<p>
					@if($row->is_cumulative) <strong class="text-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 累積する</strong>
					@else                    <strong class="text-warning"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> 累積しない</strong> @endif
				</p>
				<p>
					@if($row->is_split) <strong class="text-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 分割する</strong>
					@else               <strong class="text-warning"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> 分割しない</strong> @endif
				</p>
			</td>
			<td>{{date('n月j日 H:i:s', strtotime($row->updated_at))}}</td>
		</tr>
	@endforeach
	</tbody>
</table>
@endif