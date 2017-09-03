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
        $cnts = [];
        foreach ($rows as $row) {
            $cnt            = \App\ZenonStatus::where('monthly_id', '=', $row->monthly_id)->count();
            $cnts[$row->id] = $cnt;
        }
//        var_dump($cnts);
//        exit();
//        foreach ($rows as $row) {
//            var_dump($row);
//        }
        return view('admin.month.index', ['rows' => $rows, 'counts' => $cnts]);
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

}
