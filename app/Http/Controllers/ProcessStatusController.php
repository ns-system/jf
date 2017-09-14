<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Suisin\MonthlyImportForm;
use App\Http\Requests\Suisin\UsbStorage;
use App\Http\Controllers\Controller;
use App\Services\ProcessStatusService;

class ProcessStatusController extends Controller
{

    protected $service;

    public function __construct() {
        $this->service = new ProcessStatusService();
    }

    public function index() {
        $rows = \App\Month::orderBy('monthly_id', 'desc')->paginate(25);

        $max    = \App\Month::max('monthly_id');
        $months = [];
        for ($i = -3; $i < 3; $i++) {
            $tmp    = date('Y-m-d', strtotime($max . '01'));
            $serial = strtotime("{$tmp} -{$i} month");
            if (!\App\Month::where('monthly_id', '=', date('Ym', $serial))->exists())
            {
                $months[] = [
                    'ym'      => date('Ym', $serial),
                    'display' => date('Y年n月', $serial),
                ];
            }
        }
        $cnts = [];
        foreach ($rows as $row) {
            $all_cnt        = \App\ZenonMonthlyStatus::where('monthly_id', '=', $row->monthly_id)->count();
            $exist_cnt      = \App\ZenonMonthlyStatus::where('monthly_id', '=', $row->monthly_id)->where('is_exist', '=', true)->count();
            $import_cnt     = \App\ZenonMonthlyStatus::where('monthly_id', '=', $row->monthly_id)->where('is_import', '=', true)->count();
            $cnts[$row->id] = [
                'all'    => $all_cnt,
                'exist'  => $exist_cnt,
                'import' => $import_cnt,
            ];
        }
//        var_dump($cnts);
//        exit();
//        foreach ($rows as $row) {
//            var_dump($row);
//        }
        return view('admin.month.index', ['rows' => $rows, 'counts' => $cnts, 'months' => $months]);
    }

    public function create() {
        $in = \Input::get();
        if (\App\Month::where('monthly_id', '=', $in['monthly_id'])->exists())
        {
            return false;
        }
        \App\Month::firstOrCreate(['monthly_id' => $in['monthly_id']]);
        \Session::flash('flash_message', "月別ID［{$in['monthly_id']}］を生成しました。");
        return redirect(route('admin::super::month::show'));
    }

    public function publish($id) {
//        echo $id;
        $month = \App\Month::find($id);
        if ($month == null)
        {
            \Session::flash('flash_message', '不正な月別IDが指定されました。');
            return back();
        }

        $current = \App\Month::where('is_current', '=', true)->get();
        if ($current != null)
        {
            foreach ($current as $c) {
                $c->is_current = (int) false;
                $c->save();
            }
        }

        $month->is_current = (int) true;
        $month->save();
        \Session::flash('flash_message', "月別ID［{$month->monthly_id}］のデータを公開しました。");
        return redirect(route('admin::super::month::show'));
    }

    public function show($id) {
        $count = $this->service->setRows($id)->getCount();
        $rows  = $this->service->getRows(25);
        return view('admin.month.status', ['rows' => $rows, 'id' => $id, 'count' => $count]);
    }

    public function search($id) {
        $input  = \Input::all();
//        var_dump($input);
//        exit();
        $params = $this->service->setRows($id)->where($input)->getParameters();
        $count  = $this->service->getCount();
        $rows   = $this->service->getRows(25);
        if ($rows->isEmpty())
        {
            $params['warn_message'] = "指定した条件ではデータが見つかりませんでした。";
        }

        return view('admin.month.status', ['rows' => $rows, 'id' => $id, 'parameters' => $params, 'count' => $count])->with($params);
    }

    public function copyConfirm($id) {
        $dir       = '/mnt/server/csv_files/temp';
        $tmp_lists = scandir($dir);
        $lists     = [];
        foreach ($tmp_lists as $t) {
            $f = pathinfo($t);
            if (!empty($f['extension']) && $f['extension'] == 'csv')
            {
                $file_path = $dir.'/'.$t;
                $lists[] = [
                    'name' => $t,
                    'size' =>filesize($file_path),
                    'time'=>date('', filemtime($file_path)),
                ];
            }
        }
        var_dump($lists);

        return view('admin.month.copy_confirm', ['id' => $id, 'lists' => $lists]);
    }

    public function copy($id) {
//        var_dump($request->input());
        // TODO: copy queue
        return view('admin.month.copy_processing', ['id' => $id]);
    }

    public function copyAjax($id) {
//        $processes = \App\ZenonStatus::where('monthly_id', '=', $id);

        $obj = new \App\Services\ImportZenonDataService();

//        echo $cycle;
//        echo $id;
//        exit();
        try {
            $json = $obj->getJsonFile(storage_path('javalogs/201709_status.json'));
        } catch (\Exception $e) {
            $json = [
                'month'  => null,
                'status' => null
            ];
        }

        $status = false;
        if ($json['month'] == $id && $json['status'] == true)
        {
            $status = true;
        }
        return response()->json(['status' => $status, 'id' => $id], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function importConfirm($id) {
        $files = \App\ZenonMonthlyStatus::join('suisin_db.zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                ->month($id)
                ->where('cycle', '=', 'M')
                ->orderBy('is_process', 'desc')
                ->orderBy('is_exist', 'desc')
                ->orderBy('zenon_format_id', 'asc')
                ->get()
        ;
        return view('admin.month.import_confirm', ['files' => $files, 'id' => $id]);
    }

    public function import($id) {

        $rows = \App\ZenonMonthlyStatus::join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                ->month($id)
                ->where('is_execute', '=', true)
                ->orderBy('zenon_format_id', 'asc')
        ;

        //debug
//        foreach ($rows->get() as $r) {
//            $r->process_started_at = null;
//            $r->process_ended_at   = null;
//            $r->row_count          = 0;
//            $r->executed_row_count = 0;
////            $r->is_pre_process     = (int) false;
//            $r->is_post_process    = (int) false;
//            $r->is_process_end     = (int) false;
//            $r->is_import          = (int) false;
//            $r->save();
//        }
        return view('admin.month.import', ['id' => $id, 'rows' => $rows]);
    }

    public function dispatchJob($id, MonthlyImportForm $request) {
        $in        = $request->input();
        $processes = array_keys($in['process']);

        if (!isset($in['process']) && count($in['process']) <= 0)
        {
            throw new \Exception('処理対象が選択されていません。');
        }

        $rows = \App\ZenonMonthlyStatus::month($id)
                ->where(function($query) use ($in) {
                    foreach ($in['process'] as $key => $val) {
                        $query->orWhere('zenon_data_monthly_process_status.id', '=', $key);
                    }
                })
                ->get()
        ;
        foreach ($rows as $r) {
            $r->is_execute = (int) true;
            $r->save();
        }

        $this->dispatch(new \App\Jobs\Suisin\CsvUpload($id, $processes));
        return redirect(route('admin::super::month::import', ['id' => $id]));
    }

    public function importAjax($id) {

        $in   = \Input::only('input');
        $rows = \App\ZenonMonthlyStatus::join('zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                ->month($id)
                ->where(function($query) use ($in) {
                    foreach ($in['input'] as $val) {
                        $query->orWhere('zenon_data_monthly_process_status.id', '=', $val);
                    }
                })
                ->orderBy('zenon_format_id', 'asc')
                ->get()
        ;

        $max_cnt = $rows->count();

        $now_cnt = \App\ZenonMonthlyStatus::month($id)
                ->where(function($query) use ($in) {
                    foreach ($in['input'] as $val) {
                        $query->orWhere('zenon_data_monthly_process_status.id', '=', $val);
                    }
                })
                ->where('is_process_end', '=', true)
                ->count()
        ;

        $arr = [];
        foreach ($rows as $r) {
            $s               = (($r->process_started_at == '0000-00-00 00:00:00') ? '-' : date('G:i:s', strtotime($r->process_started_at)));
            $e               = (($r->process_ended_at == '0000-00-00 00:00:00') ? '-' : date('G:i:s', strtotime($r->process_ended_at)));
            $arr[$r->key_id] = [
                'key_id'             => $r->key_id,
                'is_pre_process'     => $r->is_pre_process,
                'is_post_process'    => $r->is_post_process,
                'is_execute'         => $r->is_execute,
                'is_process_end'     => $r->is_process_end,
                'is_import'          => $r->is_import,
                'process_started_at' => $s,
                'process_ended_at'   => $e,
                'row_count'          => $r->row_count,
                'executed_row_count' => $r->executed_row_count,
            ];
        }

//        $arr[62]['is_execute'] = 1;


        return response()->json(['rows' => $arr, 'max_cnt' => $max_cnt, 'now_cnt' => $now_cnt], 200, [], JSON_UNESCAPED_UNICODE);
    }

}
