<?php

namespace App\Http\Controllers;

use App\Http\Requests\Suisin\MonthlyImportForm;
use App\Http\Controllers\Controller;
use App\Services\ProcessStatusService;
//use App\Services\ImportZenonDataService;
use App\Services\CopyCsvFileService;

class ProcessStatusController extends Controller
{

    use \App\Services\Traits\JsonUsable;

    protected $service;
    protected $json_service;
    protected $path;

    public function __construct() {
        $this->service = new ProcessStatusService();
//        $obj                = new ImportZenonDataService();
//        $this->json_service = $obj;
        $json          = $this->getJsonFile(config_path(), 'import_config.json');
        $this->path    = $json['csv_folder_path'];
    }

    public function index() {
        $rows     = \App\Month::orderBy('monthly_id', 'desc')->paginate(25);
        $max_date = \App\Month::max('monthly_id');
        if (empty($max_date))
        {
            $max_date = date('Ym');
        }
        $months = [];
        for ($i = -3; $i < 3; $i++) {
            $tmp    = date('Y-m-d', strtotime($max_date . '01'));
            $serial = strtotime("{$tmp} -{$i} month");
            if (!\App\Month::where('monthly_id', '=', date('Ym', $serial))->exists())
            {
                $months[] = ['ym' => date('Ym', $serial), 'display' => date('Y年n月', $serial),];
            }
        }
        $cnts = [];
        foreach ($rows as $row) {
            $all_cnt        = \App\ZenonMonthlyStatus::where('monthly_id', '=', $row->monthly_id)->count();
            $exist_cnt      = \App\ZenonMonthlyStatus::where('monthly_id', '=', $row->monthly_id)->where('is_exist', '=', true)->count();
            $import_cnt     = \App\ZenonMonthlyStatus::where('monthly_id', '=', $row->monthly_id)->where('is_import', '=', true)->count();
            $cnts[$row->id] = ['all' => $all_cnt, 'exist' => $exist_cnt, 'import' => $import_cnt,];
        }
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
                $c->is_current = false;
                $c->save();
            }
        }

        $month->is_current = true;
        $month->save();
        \Session::flash('flash_message', "月別ID［{$month->monthly_id}］のデータを公開しました。");
        return redirect(route('admin::super::month::show'));
    }

    public function show($id) {
        $count = $this->service->setRows($id)->getCount();
        $rows  = $this->service->getRowPages(25);
        return view('admin.month.status', ['rows' => $rows, 'id' => $id, 'count' => $count]);
    }

    public function search($id) {
        $input  = \Input::all();
        $params = $this->service->setRows($id)->where($input)->getParameters();
        $count  = $this->service->getCount();
        $rows   = $this->service->getRowPages(25);
        if ($rows->isEmpty())
        {
            $params['warn_message'] = "指定した条件ではデータが見つかりませんでした。";
        }
        return view('admin.month.status', ['rows' => $rows, 'id' => $id, 'parameters' => $params, 'count' => $count])->with($params);
    }

    public function copyConfirm($id) {
        $dir         = $this->path . '/temp';
        $csv_service = new CopyCsvFileService();
        $lists       = $csv_service->getCsvFileList($dir);
        // 月次サイクルを先頭に持ってくるよう配列ソート
        // 2次元配列であるため、array_columnでカラム内の単一の値を取得し、それをキーにソートする
        // array_multisort(
        //     /* カラム1 */, /* SORT_ASC or SORT_DESC */, /* (並び替える値の型) */,
        //     /* カラム2 */, /* SORT_ASC or SORT_DESC */, /* (並び替える値の型) */,
        //     ...
        //     /* 最後に入れ替えを行いたいリスト変数 */
        // );
        array_multisort(array_column($lists, 'cycle'), SORT_DESC, array_column($lists, 'identifier'), SORT_ASC, $lists);
        return view('admin.month.copy_confirm', ['id' => $id, 'lists' => $lists]);
    }

    public function copy($id, $job_id) {
        return view('admin.month.copy', ['id' => $id, 'job_id' => $job_id]);
    }

    public function importConfirm($id, $job_id) {
        $files  = $this->service->setRows($id)
                ->getRows()
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                ->where('cycle', '=', 'M')
                ->orderBy('is_process', 'desc')
                ->orderBy('is_exist', 'desc')
                ->orderBy('zenon_format_id', 'asc')
                ->get()
        ;
        $counts = [];
        foreach ($files as $f) {
//            var_dump($f);
            $cnt = 0;
            if (!empty($f->table_name))
            {
                $cnt = \DB::connection('mysql_zenon')
                        ->table($f->table_name)
                        ->where("{$f->table_name}.monthly_id", '=', $f->monthly_id)
                        ->count()
                ;
            }
            $counts[$f->key_id] = $cnt;
        }
        return view('admin.month.import_confirm', ['files' => $files, 'id' => $id, 'job_id' => $job_id, 'counts' => $counts]);
    }

    public function import($id, $job_id) {
        $rows = $this->service->getProcessRows($id)
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                ->where('is_execute', '=', true)
                ->orderBy('zenon_data_csv_file_id', 'asc')
                ->get()
        ;
        $job  = \App\JobStatus::find($job_id);
        if ($job->is_import_start)
        {
            \Session::flash('flash_message', '処理は終了しています。');
        }
        else if ($job->is_import_start)
        {
            \Session::flash('warn_message', 'すでに処理は開始されています。');
        }
        return view('admin.month.import', ['id' => $id, 'rows' => $rows, 'job_id' => $job_id]);
    }

    public function dispatchImportJob($id, $job_id, MonthlyImportForm $request) {
        $in        = $request->input();
        $processes = array_keys($in['process']);
        $rows      = $this->service->getProcessRows($id, $processes)->get();
        if (!isset($in['process']) && count($in['process']) <= 0)
        {
            throw new \Exception('処理対象が選択されていません。');
        }
        // すでにDispatchされていたらリダイレクトさせる
        $job = \App\JobStatus::find($job_id);
        if ($job->is_import_start)
        {
            \Session::flash('warn_message', 'すでに処理は開始されています。');
            return redirect(route('admin::super::month::import', ['id' => $id, 'job_id' => $job_id]));
        }

        $this->service->resetProcessStatus($rows, $id);
        try {
            $this->dispatch(new \App\Jobs\Suisin\CsvUpload($id, $processes, $job_id));
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return redirect(route('admin::super::month::import', ['id' => $id, 'job_id' => $job_id]));
    }

    public function dispatchCopyJob($id) {
        $job = \App\JobStatus::create(['is_copy_start' => true]);
        try {
            $this->dispatch(new \App\Jobs\Suisin\CsvFileCopy($id, $job->id));
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return redirect(route('admin::super::month::copy', ['id' => $id, 'job_id' => $job->id]));
    }

    private function editJob($job_id) {
        $job   = \App\JobStatus::find($job_id);
        $array = [
            'is_copy_start'   => $job->is_copy_start,
            'is_copy_error'   => $job->is_copy_error,
            'is_copy_end'     => $job->is_copy_end,
            'is_import_start' => $job->is_import_start,
            'is_import_error' => $job->is_import_error,
            'is_import_end'   => $job->is_import_end,
        ];
        return $array;
    }

    public function copyAjax($id, $job_id) {
        $status = $this->editJob($job_id);
        return response()->json(['status' => $status, 'id' => $id], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function importAjax($id, $job_id) {
        $in      = \Input::only('input')['input'];
        $rows    = $this->service->getProcessRows($id, $in)->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))->get();
        $max_cnt = $rows->count();
        $now_cnt = $this->service->getProcessRows($id, $in)->where('is_post_process_end', '=', true)->count();

        $status = $this->editJob($job_id);
        $arr    = [];
        foreach ($rows as $r) {
            $s               = (($r->process_started_at == '0000-00-00 00:00:00') ? '-' : date('G:i:s', strtotime($r->process_started_at)));
            $e               = (($r->process_ended_at == '0000-00-00 00:00:00') ? '-' : date('G:i:s', strtotime($r->process_ended_at)));
            $arr[$r->key_id] = [
                'key_id'                => $r->key_id,
                'is_pre_process_start'  => $r->is_pre_process_start,
                'is_pre_process_end'    => $r->is_pre_process_end,
                'is_post_process_start' => $r->is_post_process_start,
                'is_post_process_end'   => $r->is_post_process_end,
                'is_execute'            => $r->is_execute,
                'is_import'             => $r->is_import,
                'process_started_at'    => $s,
                'process_ended_at'      => $e,
                'row_count'             => $r->row_count,
                'executed_row_count'    => $r->executed_row_count,
            ];
        }
        $param = ['rows' => $arr, 'max_cnt' => $max_cnt, 'now_cnt' => $now_cnt, 'status' => $status,];
        return response()->json($param, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function exportProcessList($id) {
        $rows  = $this->service->setRows($id)
                ->getRows()
                ->get()
        ;
        $lists = [];
        foreach ($rows as $r) {
            $l       = [
                '月別ID'        => $r->monthly_id,
                '全オンフォーマットNo' => $r->zenon_format_id,
                '全オンデータ区分'    => $r->data_type_name,
                '全オンデータ名'     => $r->zenon_data_name,
                '識別子'         => $r->identifier,
                'CSVファイル状態'   => ($r->is_exist) ? 'あり' : 'なし',
                '処理状態'        => ($r->is_import) ? '処理済み' : '未処理',
                '処理対象'        => ($r->is_process) ? '対象' : '対象外',
                '累積'          => ($r->is_cumulative) ? 'する' : 'しない',
                '口座変換'        => ($r->is_account_convert) ? 'する' : 'しない',
                '分割'          => ($r->is_split) ? 'する' : 'しない',
                'CSVファイル名'    => $r->csv_file_name,
                'ファイルサイズ(kB)' => number_format($r->file_kb_size),
                'CSVファイル基準日'  => (empty($r->csv_file_set_on) || $r->csv_file_set_on == '0000-00-00') ? '' : $r->csv_file_set_on,
                'データ件数'       => number_format($r->row_count),
                '処理開始時間'      => (empty($r->process_started_at) || $r->process_started_at == '0000-00-00 00:00:00') ? '' : $r->process_started_at,
                '処理終了時間'      => (empty($r->process_ended_at) || $r->process_ended_at == '0000-00-00 00:00:00') ? '' : $r->process_ended_at,
                'テーブル名'       => $r->table_name,
                'データ開始位置'     => $r->first_column_position,
                'データ終了位置'     => $r->last_column_position,
                'データ長'        => $r->column_length,
                '目安還元日'       => $r->reference_return_date,
            ];
            $lists[] = $l;
        }
        $headers   = array_keys($l);
//        var_dump($headers);
//        var_dump($lists);
//        $obj       = new \App\Services\CsvService();
        $file_name = "{$id}_月次データ処理リスト_" . date('Ymd_His') . '.csv';
        return $this->exportCsv($lists, $file_name, $headers);
    }

    public function exportNothingList($id) {
        try {
            $ignore = $this->getJsonFile($this->path . '/log/', "{$id}_ignore_file_list.json");
//            $ignore = $this->json_service->getJsonFile($this->path . "/log/{$id}_ignore_file_list.json");
        } catch (\Exception $exc) {
            $ignore = [];
        }
        try {
            $not_exist = $this->getJsonFile($this->path . '/log/', "{$id}_not_exist_file_list.json");
//            $not_exist = $this->json_service->getJsonFile($this->path . "/log/{$id}_not_exist_file_list.json");
        } catch (\Exception $exc) {
            $not_exist = [];
        }

        $lists = [
            [
                'CSVファイルパス'   => '',
                'CSVファイル名'    => '',
                'CSVファイル基準日'  => '',
                '識別子'         => '',
                'ファイルサイズ(kB)' => '',
                'ダウンロード日時'    => '',
                '区分'          => '',
                '除外された理由'     => '',
            ]
        ];

        foreach ($ignore as $l) {
            $lists[] = [
                'CSVファイルパス'   => $l['destination'],
                'CSVファイル名'    => $l['csv_file_name'],
                'CSVファイル基準日'  => $l['csv_file_set_on'],
                '識別子'         => $l['identifier'],
                'ファイルサイズ(kB)' => number_format($l['kb_size']),
                'ダウンロード日時'    => date('Y-m-d H:i:s', (int) $l['file_create_time']),
                '区分'          => '処理対象外',
                '除外された理由'     => "処理サイクルが月次以外であるか、指定した月のデータではないようです。（指定月：{$id}）",
            ];
        }

        foreach ($not_exist as $l) {
            $lists[] = [
                'CSVファイルパス'   => $l['destination'],
                'CSVファイル名'    => $l['csv_file_name'],
                'CSVファイル基準日'  => $l['csv_file_set_on'],
                '識別子'         => $l['identifier'],
                'ファイルサイズ(kB)' => number_format($l['kb_size']),
                'ダウンロード日時'    => date('Y-m-d H:i:s', (int) $l['file_create_time']),
                '区分'          => '設定ファイルなし',
                '除外された理由'     => "データベースに登録されていない種類の還元データです。",
            ];
        }
        $headers   = array_keys($lists[0]);
//        $obj       = new \App\Services\CsvService();
        $file_name = "{$id}_月次データ処理対象外リスト_" . date('Ymd_His') . '.csv';
        return $this->exportCsv($lists, $file_name, $headers);
    }

}
