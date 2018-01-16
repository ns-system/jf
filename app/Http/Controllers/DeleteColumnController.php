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
        $cnt   = \App\ZenonTable::where('zenon_format_id', '=', $request->zenon_format_id)->count();
        $table = \App\ZenonTable::where('zenon_format_id', '=', $request->zenon_format_id)->delete();
        \Session::flash('success_message', number_format($cnt) . "件を削除しました。");
        return back();
    }

}
