<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Http\Requests\Roster\ForceEdit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Roster\CsvSearch;
use App\Services\Roster\CsvExport;

class RosterCsvExportController extends Controller
{

    const ARR_CSV_HEADER_PLAN   = [
        'EBAS001',
        'LSLS001',
        'LSLS002',
        'LSLS003',
        'LSLS004',
    ];
    const ARR_CSV_HEADER_ACTUAL = [
        'EBAS001',
        'LTLT001',
        'LTLT002',
        'LTLT003',
        'LTLT004',
        'LTDT001',
        'LTDT002',
        'LTLT009',
    ];
    const ARR_CSV_HEADER_RAW    = [
        '社員番号',
        'ユーザー名',
        '所属部署',
        '予定入力状態',
        '実績入力状態',
        '予定承認状態',
        '予定却下状態',
        '予定承認者',
        '予定却下者',
        '実績承認状態',
        '実績却下状態',
        '実績承認者',
        '実績却下者',
        '予定勤務形態',
        '予定休暇理由',
        '予定勤務開始時刻',
        '予定勤務終了時刻',
        '予定残業開始時刻',
        '予定残業終了時刻',
        '実績残業理由',
        '実績勤務形態',
        '実績休暇理由',
        '実績勤務開始時刻',
        '実績勤務終了時刻',
        '実績残業開始時刻',
        '実績残業終了時刻',
        '実績残業理由',
        '最終更新時刻',
    ];
    const INT_MONTH_COUNT       = 12;
    const INT_RECORD_PER_PAGE   = 50;

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
        $tmp_types = \App\WorkType::workTypeList()->get();
        $types     = [];
        foreach ($tmp_types as $t) {
            $types[$t->work_type_id] = [
                'id'   => $t->work_type_id,
                'name' => $t->work_type_name,
                'time' => $t->display_time,
            ];
        }
        return $types;
    }

    public function index() {
        $months  = \App\Roster::groupBy('month_id')
                ->select(\DB::raw('COUNT(*) as cnt, month_id'))
                ->orderBy('month_id', 'desc')
                ->where('month_id', '<>', 0)
                ->take(self::INT_MONTH_COUNT)
                ->get()
        ;
        $current = date('Ym');
        return view('roster.admin.csv.index', ['months' => $months, 'current' => $current,]);
    }

    public function show($ym) {
        $service  = $this->service;
        $r        = $this->service->setMonth($ym);
        $rosters  = $r->paginate(self::INT_RECORD_PER_PAGE);
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

    public function edit($ym, $id) {
        $rests  = \App\Rest::orderBy('rest_reason_id')->get();
        $types  = $this->getType();
        $roster = \App\Roster::find($id);
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
        \Session::flash('success_message', 'データの更新が完了しました。');
        return redirect(route('admin::roster::csv::show', ['ym' => $ym]));
    }

    public function search($ym, CsvSearch $request) {

        $in       = $request->input();
        $r        = $this->service->setMonth($ym)->getSearchRosters($in);
        $rosters  = $r->paginate(self::INT_RECORD_PER_PAGE);
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
            \Session::flash('success_message', null);
            \Session::flash('warn_message', '指定した条件ではデータが見つかりませんでした。');
        }
        else
        {
            \Session::flash('success_message', '検索が終了しました。');
            \Session::flash('warn_message', null);
        }
        return view('roster.admin.csv.list', $params);
    }

    public function export($ym, $type, CsvSearch $request) {
        $in = $request->input();
        try {
            $obj  = $this->service->setMonth($ym)->makeExportData($in);
            $rows = $obj->getRows($type);
        } catch (\Exception $exc) {
            \Session::flash('warn_message', '予期しないデータが入力されたため、処理が中断されました。');
            return back();
        }

        $month  = date('Y年n月', strtotime($ym . '01'));
        $header = [];
        if ($type == 'plan')
        {
            $file_name = '予定データ';
            $header    = self::ARR_CSV_HEADER_PLAN;
        }
        else
        {
            $file_name = '実績データ';
            $header    = self::ARR_CSV_HEADER_ACTUAL;
        }
        $file_name .= "_{$month}分_" . date('Ymd_His') . '.csv';
        return $obj->export($rows, $file_name, $header);
    }

    public function rawDataExport($ym, CsvSearch $request) {
        $obj       = $this->service->setMonth($ym);
        $month     = date('Y年n月', strtotime($ym . '01'));
        $rows      = $obj->getRawData($request->input());
        $file_name = $month . '分_勤怠管理データ_' . date('Ymd_His') . '.csv';
        return $obj->export($rows, $file_name, self::ARR_CSV_HEADER_RAW);
    }

}
