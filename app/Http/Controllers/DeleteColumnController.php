<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DeleteColumnController extends Controller
{

    public function index() {
        $tables = \App\ZenonCsv::leftJoin('zenon_table_column_configs', 'zenon_data_csv_files.zenon_format_id', '=', 'zenon_table_column_configs.zenon_format_id')
                ->select(\DB::raw('zenon_data_csv_files.zenon_format_id, zenon_data_name, table_name, count(*) as total'))
                ->having('total', '>', 1)
                ->groupBy('zenon_data_csv_files.zenon_format_id')
                ->orderBy('zenon_data_csv_files.zenon_format_id', 'asc')
                ->get()
        ;
        return view('admin.admin.column.delete', ['tables' => $tables]);
    }

    public function delete(Requests\SuperUser\ColumnDelete $request) {
        $ids     = $request->only('zenon_format_id');
        $cnt     = 0;
        $tbl_cnt = 0;
        foreach ($ids['zenon_format_id'] as $id) {
            $cnt += \App\ZenonTable::where('zenon_format_id', '=', $id)->count();
            $tbl_cnt++;
            \App\ZenonTable::where('zenon_format_id', '=', $id)->delete();
        }
        \Session::flash('success_message', "合計 {$tbl_cnt}テーブル / " . number_format($cnt) . "件を削除しました。");
        return back();
    }

}
