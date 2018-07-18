<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Http\Requests\Roster\ForceEdit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Roster\CsvSearch;
use App\Services\Roster\CsvExport;
use Illuminate\Redis\Database;

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
    const COLORS                = [
        '未入力' => '200, 200, 200',
        '未承認' => '218, 131,0',
        '却下'  => '206, 51, 35',
        '承認'  => '0, 163, 131',
    ];
    const INT_MONTH_COUNT       = 12;
    const INT_RECORD_PER_PAGE   = 50;

    protected $service;

    public function __construct()
    {
        $this->service = new CsvExport();
    }

    private function getRest()
    {
        $tmp_rests = \App\Rest::orderBy('rest_reason_id')->get();
        $rests     = [];
        foreach ($tmp_rests as $r) {
            $rests[$r->rest_reason_id] = $r->rest_reason_name;
        }
        return $rests;
    }

    private function getType()
    {
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

    public function index()
    {
        $months  = \App\Roster::groupBy('month_id')
            ->select(\DB::raw('COUNT(*) as cnt, month_id'))
            ->orderBy('month_id', 'desc')
            ->where('month_id', '<>', 0)
            ->take(self::INT_MONTH_COUNT)
            ->get();
        $current = date('Ym');
        $chart   = $this->getChartData($current);
        $colors  = self::COLORS;

        return view('roster.admin.csv.index', ['months' => $months, 'current' => $current, 'chart' => $chart, 'colors' => $colors]);
    }

    private function getChartData()
    {
        $d       = new \DateTime();
        $current = $d->format('Ym');
        $past    = $d->modify('-1 month')->format('Ym');

        $summary = \App\Roster::groupBy('month_id')
            ->whereBetween('month_id', [$past, $current])
            ->orderBy('month_id', 'desc')
            ->select(['month_id'])
            // plan
            ->addSelect(\DB::raw('count((is_plan_entry = false) or null)                                                            as 予定未入力'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_accept = false and is_plan_reject = false) or null)       as 予定未承認'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_reject = true) or null)                                   as 予定却下'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_accept = true) or null)                                   as 予定承認済'))
            // actual
            ->addSelect(\DB::raw('count((is_actual_entry = false) or null) as 実績未入力'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_accept = false and is_actual_reject = false) or null) as 実績未承認'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_reject = true) or null)                               as 実績却下'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_accept = true) or null)                               as 実績承認済'))
            ->get()
            ->toArray();

        $summary_with_division = \App\Roster::join('sinren_db.sinren_users', 'sinren_users.user_id', '=', 'rosters.user_id')
            ->join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
            ->whereBetween('month_id', [$past, $current])
            ->orderBy('month_id', 'desc')
            ->orderBy('sinren_users.division_id')
            ->groupBy('month_id')
            ->groupBy('sinren_users.division_id')
            // plan
            ->select(['month_id', 'sinren_users.division_id', 'sinren_divisions.division_name'])
            ->addSelect(\DB::raw('count((is_plan_entry = false) or null)                                                            as 予定未入力'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_accept = false and is_plan_reject = false) or null)       as 予定未承認'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_reject = true) or null)                                   as 予定却下'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_accept = true) or null)                                   as 予定承認済'))
            // actual
            ->addSelect(\DB::raw('count((is_actual_entry = false) or null) as 実績未入力'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_accept = false and is_actual_reject = false) or null) as 実績未承認'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_reject = true) or null)                               as 実績却下'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_accept = true) or null)                               as 実績承認済'))
            ->get()
            ->toArray();

        $summary_with_date = \App\Roster::groupBy('entered_on')
            ->whereBetween('month_id', [$past, $current])
            // plan
            ->select(['month_id', 'entered_on'])
            ->addSelect(\DB::raw('count((is_plan_entry = false) or null)                                                            as 予定未入力'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_accept = false and is_plan_reject = false) or null)       as 予定未承認'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_reject = true) or null)                                   as 予定却下'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_accept = true) or null)                                   as 予定承認済'))
            // actual
            ->addSelect(\DB::raw('count((is_actual_entry = false) or null) as 実績未入力'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_accept = false and is_actual_reject = false) or null) as 実績未承認'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_reject = true) or null)                               as 実績却下'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_accept = true) or null)                               as 実績承認済'))
            ->get()
            ->toArray();

        $res = [];
        foreach ($summary as $s) {
            $res[$s['month_id']]['summary'][] = $s;
        }
        foreach ($summary_with_division as $s) {
            $res[$s['month_id']]['summary_with_division'][] = $s;
        }
        foreach ($summary_with_date as $s) {
            $res[$s['month_id']]['summary_with_date'][] = $s;
        }

//        \Log::debug([get_class($this) => $res]);
        return $res;
    }

    public function show($ym)
    {
        $service  = $this->service;
        $r        = $this->service->setMonth($ym)->getRosters();
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

    public function edit($ym, $id)
    {
        $rests  = \App\Rest::orderBy('rest_reason_id')->get();
        $types  = $this->getType();
        $roster = \App\Roster::find($id);
        $user   = \App\User::find($roster->user_id);

        $query_string = '';
        foreach (\Input::get() as $key => $para) {
            $query_string .= (mb_strlen($query_string) != 0) ? '&' : '';
            $query_string .= "{$key}={$para}";
        }
        $query_string = '?' . $query_string;
//        dd($query_string);
        $params = [
            'id'           => $id,
            'ym'           => $ym,
            'user'         => $user,
            'roster'       => $roster,
            'rests'        => $rests,
            'types'        => $types,
            'query_string' => $query_string,
        ];
        return view('roster.admin.csv.edit', $params);
    }

    public function update(ForceEdit $request, $ym)
    {
        $in           = $request->input();
        $query_string = (isset($in['query_string'])) ? $in['query_string'] : '';
        $this->service->update($in);
        \Session::flash('success_message', 'データの更新が完了しました。');

        $query_string = str_replace('?', '', $query_string);
        $arr          = explode('&', $query_string);
        $search       = [];
        foreach ($arr as $key => $str) {
            $search[$key] = $str;
        }
//        dd($params);
//        $params = $this->skipSearch($ym, $search);
//        dd($params);
//        return view('roster.admin.csv.list', $params);
        return redirect(route('admin::roster::csv::search', ['ym' => $ym]) . '?' . $query_string);
    }

    private function skipSearch($ym, $in)
    {
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
        if ($rosters->isEmpty()) {
            \Session::flash('success_message', null);
            \Session::flash('warn_message', '指定した条件ではデータが見つかりませんでした。');
        } else {
            \Session::flash('success_message', '検索が終了しました。');
            \Session::flash('warn_message', null);
        }
        return $params;
    }

    public function search($ym, CsvSearch $request)
    {

        $in     = $request->input();
        $params = $this->skipSearch($ym, $in);
//        $r        = $this->service->setMonth($ym)->getSearchRosters($in);
//        $rosters  = $r->paginate(self::INT_RECORD_PER_PAGE);
//        $calendar = $this->service->getCalendar();
//        $rests    = $this->getRest();
//        $types    = $this->getType();
//
//        $divs   = \App\Division::orderBy('division_id')->get();
//        $params = [
//            'rosters'  => $rosters,
//            'ym'       => $ym,
//            'types'    => $types,
//            'rests'    => $rests,
//            'calendar' => $calendar,
//            'divs'     => $divs,
//            'search'   => $in,
//        ];
//        if ($rosters->isEmpty())
//        {
//            \Session::flash('success_message', null);
//            \Session::flash('warn_message', '指定した条件ではデータが見つかりませんでした。');
//        }
//        else
//        {
//            \Session::flash('success_message', '検索が終了しました。');
//            \Session::flash('warn_message', null);
//        }
        return view('roster.admin.csv.list', $params);
    }

    public function export($ym, $type, CsvSearch $request)
    {
        $in = $request->input();
        try {
            $obj  = $this->service->setMonth($ym)->makeExportData($in);
            $rows = $obj->getRows($type);
        }
        catch (\Exception $exc) {
            \Session::flash('warn_message', '予期しないデータが入力されたため、処理が中断されました。');
            return back();
        }

        $month  = date('Y年n月', strtotime($ym . '01'));
        $header = [];
        if ($type == 'plan') {
            $file_name = '予定データ';
            $header    = self::ARR_CSV_HEADER_PLAN;
        } else {
            $file_name = '実績データ';
            $header    = self::ARR_CSV_HEADER_ACTUAL;
        }
        $file_name .= "_{$month}分_" . date('Ymd_His') . '.csv';
        return $obj->export($rows, $file_name, $header);
    }

    public function rawDataExport($ym, CsvSearch $request)
    {
        $obj       = $this->service->setMonth($ym);
        $month     = date('Y年n月', strtotime($ym . '01'));
        $rows      = $obj->getRawData($request->input());
        $file_name = $month . '分_勤怠管理データ_' . date('Ymd_His') . '.csv';
        return $obj->export($rows, $file_name, self::ARR_CSV_HEADER_RAW);
    }

    public function indexEnteredUsers($ym = '')
    {
        $dt            = (!empty($ym)) ? new \DateTime($ym . '01') : new \DateTime();
        $rows          = $this->getEnteredUsers($dt->format('Ym'));
        $current_month = $dt->format('Ym');
        $last_month    = $dt->modify('-1 month')->format('Ym');
        $next_month    = $dt->modify('+2 month')->format('Ym');
        return view('roster.admin.csv.entered_users', [
            'ym'         => $current_month,
            'rows'       => $rows,
            'colors'     => self::COLORS,
            'last_month' => $last_month,
            'next_month' => $next_month,
        ]);

    }

    public function getEnteredUsers($ym = '')
    {
        $dt   = (!empty($ym)) ? new \DateTime($ym . '01') : new \DateTime();
        $id   = \Auth::user()->id;
        $user = \App\User::find($id)->RosterUser($id);
        $date = [$dt->format('Y-m-01'), $dt->format('Y-m-t')];

        $query = \App\User::leftJoin('roster_db.rosters', 'users.id', '=', 'rosters.user_id')
            ->leftJoin('sinren_db.sinren_users', 'sinren_users.user_id', '=', 'users.id')
            ->leftJoin('sinren_db.sinren_divisions', 'sinren_divisions.division_id', '=', 'sinren_users.division_id')
            ->whereBetween('rosters.entered_on', $date)
            ->groupBy('users.id')
            ->select('users.id as id', 'first_name', 'last_name', 'sinren_divisions.division_name', 'sinren_divisions.division_id')
            ->addSelect(\DB::raw('count((is_plan_entry = false) or null)                                                            as 予定未入力'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_accept = false and is_plan_reject = false) or null)       as 予定未承認'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_reject = true) or null)                                   as 予定却下'))
            ->addSelect(\DB::raw('count((is_plan_entry = true and is_plan_accept = true) or null)                                   as 予定承認済'))
            ->addSelect(\DB::raw('count((is_actual_entry = false) or null)                                                          as 実績未入力'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_accept = false and is_actual_reject = false) or null) as 実績未承認'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_reject = true) or null)                               as 実績却下'))
            ->addSelect(\DB::raw('count((is_actual_entry = true and is_actual_accept = true) or null)                               as 実績承認済'))
            ->orderBy('sinren_divisions.division_id');

        if (!$user->is_super_user && !$user->is_administrator) {
            $div = ($user->is_chief) ? \App\ControlDivision::where('user_id', $id)->get() : \App\SinrenUser::where('user_id', $id)->get();
            $query->where(function ($query) use ($div) {
                foreach ($div as $d) {
                    $query->orWhere('sinren_users.division_id', $d->division_id);
                }
            });
        }

        return $query->get();
//        return view('roster.admin.csv.entered_users', ['ym' => $dt->format('Ym'), 'rows' => $query->get(), 'colors' => self::COLORS]);
    }

}
