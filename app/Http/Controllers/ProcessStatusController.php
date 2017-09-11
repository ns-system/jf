<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
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
            $all_cnt        = \App\ZenonStatus::where('monthly_id', '=', $row->monthly_id)->count();
            $exist_cnt      = \App\ZenonStatus::where('monthly_id', '=', $row->monthly_id)->where('is_exist', '=', true)->count();
            $import_cnt     = \App\ZenonStatus::where('monthly_id', '=', $row->monthly_id)->where('is_import', '=', true)->count();
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

    public function copy($id) {
//        var_dump($id);
        // TODO: queue
        return view('admin.month.copy_processing', ['id' => $id]);
//        $job = (new \App\Jobs\CallJava())->delay(1);
//        $this->dispatch($job);
//        $e = shell_exec('java -jar ~/cvs/app/Console/Commands/connect/phpConnectTest.jar ~/cvs/app/Console/Commands/connect/yuusisien_config.properties test');
//        var_dump($e);
//
//        var_dump('At ' . date('Y-m-d H:i:s') . ', queue pushed.');
    }

    public function copyAjax($id) {
//        $processes = \App\ZenonStatus::where('monthly_id', '=', $id);

        $obj = new \App\Services\ImportZenonDataService();

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
        return response()->json(
                        [
                    'status' => $status,
                    'id'     => $id
                        ], 200, [], JSON_UNESCAPED_UNICODE
        );
    }

    public function confirm($id) {
        $files = \App\ZenonStatus::join('suisin_db.zenon_data_csv_files', 'zenon_data_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->where('monthly_id', '=', 201705)
                ->orderBy('zenon_format_id')
                ->paginate(50)
        ;
        return view('admin.month.import_confirm', ['files' => $files]);
    }
    
    public function import($id){
        
    }

}
