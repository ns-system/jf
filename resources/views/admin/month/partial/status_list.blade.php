@if($rows->isEmpty())
<div class="alert alert-danger" role="alert">データが見つかりませんでした。</div>
@else
<table class="table va-middle table-small">
	<thead>
		<tr class="bg-primary">
            <th>No</th>
            <th>
                <p>区分</p>
                <p>データ名</p>
            </th>
            <th>
                <p>CSVファイル名</p>
                <p>DB内レコード件数</p>
                <p>件数</p>
            </th>
            <th class="text-right">当月分登録件数</th>
            <th>
                <p>サイクル</p>
                <p>目安還元日</p>
            </th>

            <th>
                <p>状態</p>
            </th>
            <th>
                <p>設定情報</p>
            </th>
            <th>処理時刻</th>

        </tr>
    </thead>
    <tbody>
     @foreach($rows as $i => $row)
     <tr>
        <th class="bg-primary">{{$i + 1}}</th>
        <td class="text-left">
            <p>{{$row->data_type_name or '登録されていません'}}</p>
            <p>{{$row-> zenon_data_csv_file_id}} ： {{$row->zenon_data_name}}</p>
        </td>
        <td class="text-left">
            <p>@if(!empty($row->csv_file_name)) {{$row->csv_file_name}} @else <small>CSVファイルが存在しません</small> @endif</p>
            <p>@if($row->table_name) {{$row->table_name}} @else <small>テーブルが登録されていません</small> @endif</p>
        </td>
        <td class="text-right">
            <?php
            if(isset($row->database_name) && isset($row->table_name))
            {
                try {
                    $sql = "SELECT COUNT(*) AS row_count FROM `{$row->database_name}`.`{$row->table_name}` WHERE `monthly_id`=?;";
                    $res = \DB::select($sql, [$id]);
                    if($res != null){
                        foreach ($res as $r) {
                            echo number_format($r->row_count) . "件";
                        }
                    }else{
                        echo "0件";
                    }
                } catch (\Exception $e) {
                    echo "0件";
                }
            }
            else
            {
                echo "0件";
            }
            ?>
        </td>
        <td class="text-left">
            <p><span class="label label-info">{{$row->cycle}}</span> {{$row->reference_return_date or '登録されていません'}}</p>
        </td>

        <td>
            <p>
             @if($row->is_exist) <span class="label label-success" style="min-width: 100px;">ファイル</span>
             @else               <span class="label label-default" style="min-width: 100px;">ファイル</span> @endif
         </p>
         <p>
            @if($row->is_process) <span class="label label-success" style="min-width: 100px;">処理対象</span>
            @else                 <span class="label label-default" style="min-width: 100px;">処理対象</span> @endif
        </p>
        <p>
         @if($row->is_import) <span class="label label-success" style="min-width: 100px;">処理</span>
         @else                <span class="label label-default" style="min-width: 100px;">処理</span> @endif
     </p>
 </td>
 <td>
    <p>
     @if($row->is_cumulative) <span class="label label-info">累積</span>
     @else                    <span class="label label-default">累積</span> @endif
 </p>
 <p>
     @if($row->is_split) <span class="label label-info">分割</span>
     @else               <span class="label label-default">分割</span> @endif
 </p>
 <p>
    @if($row->is_account_convert) <span class="label label-info">変換</span>
    @else                         <span class="label label-default">変換</span> @endif
</p>
</td>
<td>@if($row->is_import) {{date('Y年n月j日 H:i:s', strtotime($row->process_updated_at))}} @else - @endif</td>
</tr>
@endforeach
</tbody>
</table>
@endif