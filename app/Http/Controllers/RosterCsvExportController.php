<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Http\Requests\Roster\ForceEdit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Roster\CsvSearch;
use App\Services\Roster\CsvExport;

class RosterCsvExportController extends Controller
{

    protected $service;

    public function __construct() {
        $this->service = new CsvExport();
    }

    private function getRest() {
        $tmp_rests = \App\Rest::orderBy('rest_reason_id')->get();
        $rests     = [];
        foreach ($tmp_rests as $r) {
            $rests[$r->rest_reason_id] = $r->rest_reason_name;
        }
        return $rests;
    }

    private function getType() {
        $tmp_types = \App\WorkType::orderBy('work_type_id')->get();
        $types     = [];
        foreach ($tmp_types as $t) {
            $types[$t->work_type_id] = [
                'id'   => $t->work_type_id,
                'name' => $t->work_type_name,
                'time' => null,
            ];
            if ($t->work_start_time != $t->work_end_time)
            {
                $types[$t->work_type_id]['time'] = date('G:i', strtotime($t->work_start_time)) . ' ～ ' . date('G:i', strtotime($t->work_end_time));
            }
        }
        return $types;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $months  = \App\Roster::groupBy('month_id')
                ->select(\DB::raw('COUNT(*) as cnt, month_id'))
                ->orderBy('month_id', 'desc')
                ->where('month_id', '<>', 0)
                ->take(24)
                ->get()
        ;
        $current = date('Ym');
        return view('roster.admin.csv.index', ['months' => $months, 'current' => $current,]);
    }

    public function show($ym) {

        $service  = $this->service->setMonth($ym);
        $rosters  = $service->getRosters()->paginate(50);
        $calendar = $service->getCalendar();
        $rests    = $this->getRest();
        $types    = $this->getType();
        $divs     = \App\Division::orderBy('division_id')->get();
        $params   = [
            'rosters'  => $rosters,
            'ym'       => $ym,
            'types'    => $types,
            'rests'    => $rests,
            'calendar' => $calendar,
            'divs'     => $divs,
            'search'   => null,
        ];

        return view('roster.admin.csv.list', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($ym, $id) {
//        var_dump($id);
        $rests  = \App\Rest::orderBy('rest_reason_id')->get();
        $types  = $this->getType();
        $roster = \App\Roster::find($id);
//        var_dump($roster);
        $user   = \App\User::find($roster->user_id);
        $params = [
            'id'     => $id,
            'ym'     => $ym,
            'user'   => $user,
            'roster' => $roster,
            'rests'  => $rests,
            'types'  => $types,
        ];
        return view('roster.admin.csv.edit', $params);
    }

    public function update(ForceEdit $request, $ym) {
        $in = $request->input();
        $this->service->update($in);
        \Session::flash('flash_message', 'データの更新が完了しました。');
        return redirect(route('admin::roster::csv::show', ['ym' => $ym]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function search($ym, CsvSearch $request) {

        $in       = $request->input();
        $r        = $this->service->setMonth($ym)->getSearchRosters($in);
        $rosters  = $r->paginate(50);
        $calendar = $this->service->getCalendar();
        $rests    = $this->getRest();
        $types    = $this->getType();

        $divs   = \App\Division::orderBy('division_id')->get();
        $params = [
            'rosters'  => $rosters,
            'ym'       => $ym,
            'types'    => $types,
            'rests'    => $rests,
            'calendar' => $calendar,
            'divs'     => $divs,
            'search'   => $in,
        ];
        if ($rosters->isEmpty())
        {
            \Session::flash('warn_message', '指定した条件ではデータが見つかりませんでした。');
        }
        else
        {

            \Session::flash('warn_message', null);
        }
        return view('roster.admin.csv.list', $params);
    }

    public function export($ym, $type, CsvSearch $request) {
        $in  = $request->input();
        $obj = $this->service->setMonth($ym)->makeExportData($in);
//        $plan_rows   = $obj->getRows('plan');
//        $actual_rows = $obj->getRows('actual');

        $rows = $obj->getRows($type);

//        var_dump($plan_rows);
//        var_dump($actual_rows);

        $headers = [
            'plan'   => [
                'EBAS001',
                'LSLS001',
                'LSLS002',
                'LSLS003',
                'LSLS004',
            ],
            'actual' => [
                'EBAS001',
                'LTLT001',
                'LTLT002',
                'LTLT003',
                'LTLT004',
                'LTDT001',
                'LTDT002',
                'LTLT009',
            ],
        ];

        $month = date('Y年n月', strtotime($ym . '01'));
        if ($type == 'plan')
        {
            $file_name = '予定データ';
        }
        elseif ($type == 'actual')
        {
            $file_name='実績データ';
        }else{
            $file_name ='Unkown';
        }
        
        $file_name .= "_{$month}分_" .date('Ymd_His').'.csv';

        return $obj->export($rows, $file_name, $headers[$type]);

//        var_dump($plan_rows);
//        var_dump($actual_rows);
    }

    public function exportAll() {
        
    }

}
