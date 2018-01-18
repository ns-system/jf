<?php

namespace App\Http\Controllers;

use App\Http\Requests\Suisin\MonthlyImportForm;
use App\Http\Controllers\Controller;
use App\Services\ProcessStatusService;
use App\Services\CopyCsvFileService;
use \App\Services\Traits\JsonUsable;

class ProcessStatusController extends Controller
{

    use JsonUsable;

    const INT_MAX_PREV_MONTH = -3;
    const INT_MAX_NEXT_MONTH = 30;

    protected $service;
//    protected $json_service;
    protected $path;

    public function __construct() {
        $this->service = new ProcessStatusService();
        $json          = $this->getJsonFile(config_path(), 'import_config.json');
        $this->path    = $json['csv_folder_path'];
    }

    public function index() {
        $rows     = \App\Month::orderBy('monthly_id', 'desc')->paginate(25);
        $max_date = date('Ym');
        $months   = [];
        for ($i = self::INT_MAX_PREV_MONTH; $i < self::INT_MAX_NEXT_MONTH; $i++) {
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
        $job_status = \App\JobStatus::orderBy('id', 'desc')->take(5)->get();
        $db         = new \App\Services\DatabaseUsageShowService();
        $db_usage   = $db->getMySqlConfig()->getNowDirectoryUsage();
        return view('admin.month.index', ['rows' => $rows, 'counts' => $cnts, 'months' => $months, 'job_status' => $job_status, 'usage' => $db_usage]);
    }

    public function create() {
        $in = \Input::get();
        if (\App\Month::where('monthly_id', '=', $in['monthly_id'])->exists())
        {
            return false;
        }
        $displayed_on = date('Y-m-d', strtotime($in['monthly_id'] . '01'));
        \App\Month::firstOrCreate(['monthly_id' => $in['monthly_id'], 'displayed_on' => $displayed_on]);
        \Session::flash('success_message', "月別ID［{$in['monthly_id']}］を生成しました。");
        return redirect(route('admin::super::month::show'));
    }

    public function publish($id) {
        $month = \App\Month::find($id);
        if ($month == null)
        {
            \Session::flash('success_message', '不正な月別IDが指定されました。');
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
        \Session::flash('success_message', "月別ID［{$month->monthly_id}］のデータを公開しました。");
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

        $mst_cnt = \App\ZenonCsv::count();
        $tbl_cnt = \App\ZenonTable::count();
        if ($mst_cnt <= 0 || $tbl_cnt <= 0)
        {
            \Session::flash('danger_message', '全オン還元CSVファイル設定もしくはMySQL全オンテーブルカラム設定が登録されていないようです。先に登録を行ってください。');
            return back();
        }

        $csv_service = new CopyCsvFileService();

        $tmp_dir = $this->path . '/temp';
        try {
            $tmp_lists = [];
            if (file_exists($tmp_dir))
            {
                $tmp_lists = $csv_service->getCsvFileList($tmp_dir);
            }
        } catch (\Exception $exc) {
            \Session::flash('danger_message', '一時ディレクトリもしくは月次ディレクトリが見つかりませんでした。管理者に問い合わせてください。');
            return back();
        }
        // 月次処理ステータスを月別IDで検索して、件数が0以上であればコピー処理をスキップする
        // 件数が0件かつリストが存在しない場合 -> エラーとして処理を行わない
        if (empty($tmp_lists)/* && empty($monthly_lists) */)
        {
            \Session::flash('warn_message', '所定のディレクトリに当月中のCSVファイルが見つかりませんでした。');
            return back();
        }
        // 月次サイクルを先頭に持ってくるよう配列ソート
        // 2次元配列であるため、array_columnでカラム内の単一の値を取得し、それをキーにソートする
        // array_multisort(
        //     /* カラム1 */, /* SORT_ASC or SORT_DESC */, /* (並び替える値の型) */,
        //     /* カラム2 */, /* SORT_ASC or SORT_DESC */, /* (並び替える値の型) */,
        //     ...
        //     /* 最後に入れ替えを行いたいリスト変数 */
        // );
        array_multisort(array_column($tmp_lists, 'cycle'), SORT_ASC, array_column($tmp_lists, 'identifier'), SORT_ASC, $tmp_lists);
        return view('admin.month.copy_confirm', ['id' => $id, 'tmp_lists' => $tmp_lists/* , 'monthly_lists' => $monthly_lists */]);
    }

    public function filesShow($term_status, $id) {
        $job = \App\JobStatus::where('is_copy_end', '=', true)
                ->where('is_import_start', '=', false)
                ->orderBy('id', 'desc')
                ->first()
        ;
        if (empty($job) || $job->id === 0)
        {
            $job = \App\JobStatus::create(['is_copy_start' => true, 'is_copy_end' => true, 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d H:i:s'),]);
        }
        $job_id  = $job->id;
        $mst_cnt = \App\ZenonCsv::count();
        $tbl_cnt = \App\ZenonTable::count();
        if ($mst_cnt === 0 || $tbl_cnt === 0)
        {
            \Session::flash('danger_message', '全オン還元CSVファイル設定もしくはMySQL全オンテーブルカラム設定が登録されていないようです。先に登録を行ってください。');
            return back();
        }
        $copy_csv_file_service = new \App\Services\CopyCsvFileService();
        try {
            // 週次・日次の場合はDBのスケルトンだけ生成する
            $copy_csv_file_service->setMonthlyId($id)
                    ->setDirectoryPath($this->path)
                    ->tableTemplateCreation(null, $id)
            ;
            // 月次の場合はDBにファイル名まで流し込む
            if ($term_status === 'monthly')
            {
                $copy_csv_file_service->registrationCsvFileToDatabase();
            }
        } catch (\Exception $exc) {
            \Session::flash('danger_message', $exc->getMessage());
            return back();
        }

        // 場合に応じてリダイレクト先を変える
        try {
            switch ($term_status) {
                case 'daily':
                    return $this->dailyFiles($term_status, $id, $job_id);
                case 'weekly':
                    return $this->weeklyFiles($term_status, $id, $job_id);
                case 'monthly':
                    return $this->monthlyFiles($term_status, $id, $job_id);
                default:
                    throw new \Exception("不正な区分が入力されたようです。");
            }
        } catch (\Exception $exc) {
            \Session::flash('danger_message', $exc->getMessage());
            return back();
        }
    }

    public function monthlyFiles($term_status, $id, $job_id) {
        $csv_service = new CopyCsvFileService();
        $monthly_dir = $this->path . "/{$term_status}/{$id}";
        try {
            $monthly_lists = [];
            if (file_exists($monthly_dir))
            {
                $monthly_lists = $csv_service->getCsvFileList($monthly_dir);
            }
        } catch (\Exception $exc) {
            \Session::flash('danger_message', '月次ディレクトリが見つかりませんでした。管理者に問い合わせてください。');
            return back();
        }
        // 月次処理ステータスを月別IDで検索して、件数が0以上であればコピー処理をスキップする
        // 件数が0件かつリストが存在しない場合 -> エラーとして処理を行わない
        if (empty($monthly_lists))
        {
            \Session::flash('warn_message', '所定のディレクトリに当月中のCSVファイルが見つかりませんでした。');
            return back();
        }
        return redirect()->route('admin::super::term::import_confirm', ['term_status' => $term_status, 'id' => $id, 'job_id' => $job_id]);
    }

    private function dailyFiles($term_status, $id, $job_id) {
        $service   = new \App\Services\DailyAndWeeklyFileService();
        $daily_dir = $this->path . "/{$term_status}/{$id}";
        $tmp       = $service->getDailyList($daily_dir);
        $date_list = $tmp['date_list'];
        $file_list = $tmp['file_list'];

        // 月次処理ステータスを月別IDで検索して、件数が0以上であればコピー処理をスキップする
        // 件数が0件かつリストが存在しない場合 -> エラーとして処理を行わない
        if (empty($date_list) || empty($file_list))
        {
            \Session::flash('warn_message', '所定のディレクトリに当月中のCSVファイルが見つかりませんでした。');
            return back();
        }
        return view('admin.month.daily_select', ['id' => $id, 'job_id' => $job_id, 'term_status' => $term_status, 'date_list' => $date_list, 'file_list' => $file_list]);
    }

    private function weeklyFiles($term_status, $id, $job_id) {
        $service    = new \App\Services\DailyAndWeeklyFileService();
        $weekly_dir = $this->path . "/{$term_status}/{$id}";
        $file_list  = $service->getFiles($weekly_dir);

        // 月次処理ステータスを月別IDで検索して、件数が0以上であればコピー処理をスキップする
        // 件数が0件かつリストが存在しない場合 -> エラーとして処理を行わない
        if (empty($file_list))
        {
            \Session::flash('warn_message', '所定のディレクトリに当月中のCSVファイルが見つかりませんでした。');
            return back();
        }
        return view('admin.month.weekly_select', ['id' => $id, 'job_id' => $job_id, 'term_status' => $term_status, 'file_list' => $file_list]);
    }

    private function updateMonthlyStatus($file, $path, $job_id) {
        $obj = \App\ZenonMonthlyStatus::join('suisin_db.zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->where('identifier', '=', $file['identifier'])
                ->firstOrFail()
        ;

        $obj->job_status_id         = $job_id;
        $obj->file_path             = $path;
        $obj->csv_file_name         = $file['csv_file_name'];
        $obj->file_kb_size          = $file['kb_size'];
        $obj->csv_file_set_on       = $file['csv_file_set_on'];
        $obj->is_exist              = true;
        $obj->is_execute            = false;
        $obj->is_pre_process_start  = false;
        $obj->is_pre_process_end    = false;
        $obj->is_pre_process_error  = false;
        $obj->is_post_process_start = false;
        $obj->is_post_process_end   = false;
        $obj->is_post_process_error = false;
        $obj->is_process_end        = false;
        $obj->is_import             = false;
        $obj->save();
    }

    public function weeklySelect($id, $job_id, \App\Http\Requests\Suisin\WeeklyFile $input) {
        $term_status = 'weekly';
        $service     = new \App\Services\CopyCsvFileService();
        $path        = "{$this->path}/{$term_status}/{$id}";
        $input_files = $input['files'];
        $lists       = $service->getCsvFileList($path);

        foreach ($input_files as $input_file) {
            foreach ($lists as $file) {
                if ($file['csv_file_name'] === $input_file)
                {
                    $this->updateMonthlyStatus($file, $path, $job_id);
                }
            }
        }
        return redirect()->route('admin::super::term::import_confirm', ['term_status' => 'weekly', 'id' => $id, 'job_id' => $job_id]);
    }

    public function dailySelect($id, $job_id, \App\Http\Requests\Suisin\DailyDate $input) {
        $term_status = 'daily';
        $service     = new \App\Services\CopyCsvFileService();
        $date        = $input['date'];
        $path        = "{$this->path}/{$term_status}/{$id}/{$date}";
        $lists       = $service->getCsvFileList($path);
        foreach ($lists as $file) {
            $this->updateMonthlyStatus($file, $path, $job_id);
        }
        return redirect()->route('admin::super::term::import_confirm', ['term_status' => 'daily', 'id' => $id, 'job_id' => $job_id]);
    }

    public function copy($id, $job_id) {
        return view('admin.month.copy', ['id' => $id, 'job_id' => $job_id]);
    }

    public function importConfirm($term_status, $monthly_id, $job_id) {
        $cycle = substr((strtoupper($term_status)), 0, 1);
        $files = $this->service->setRows($monthly_id)
                ->getRows()
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                ->where('cycle', '=', $cycle)
                ->orderBy('is_process', 'desc')
                ->orderBy('is_exist', 'desc')
                ->orderBy('zenon_format_id', 'asc')
                ->get()
        ;

        $record_counts = [];
        $column_counts = [];
        foreach ($files as $f) {
            $cnt = 0;
            if (!empty($f->table_name))
            {
                $cnt = \DB::connection('mysql_zenon')
                        ->table($f->table_name)
                        ->where("{$f->table_name}.monthly_id", '=', $f->monthly_id)
                        ->count()
                ;
            }
            $column_counts[$f->key_id] = \App\ZenonTable::where('zenon_format_id', '=', $f->zenon_format_id)->count();
            $record_counts[$f->key_id] = $cnt;
        }
        return view('admin.month.import_confirm', ['files' => $files, 'id' => $monthly_id, 'job_id' => $job_id, 'record_counts' => $record_counts, 'column_counts' => $column_counts, 'term_status' => $term_status]);
    }

    public function import($id, $job_id) {
        $rows = $this->service->getProcessRows($job_id)
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                ->where('is_execute', '=', true)
                ->orderBy('zenon_format_id', 'asc')
                ->orderBy('key_id', 'asc')
                ->get()
        ;
        $job  = \App\JobStatus::find($job_id);
        if ($job->is_import_end)
        {
            \Session::flash('success_message', '処理は終了しています。');
        }
        return view('admin.month.import', ['id' => $id, 'rows' => $rows, 'job_id' => $job_id]);
    }

    public function dispatchImportJob($term_status, $id, $job_id, MonthlyImportForm $request) {
        $in          = $request->only(['process']);
        $job_status  = $this->service->setJobStatus($job_id)->getJobStatus();
        $process_ids = array_keys($in['process']);
        // 日次・週次の場合、過去データを削除する
        if ($term_status === 'daily' || $term_status === 'weekly')
        {
            $this->dispatch(new \App\Jobs\Suisin\TableDelete($process_ids, \Auth::user()->email, /* 当月分のみ削除 = */ false, /* 処理後メール送信 = */ false));
        }
        $rows = $this->service->setJobStatusIdToMonthlyStatus($process_ids, $job_id)->getProcessRows($job_id)->get();
        if (!isset($rows))
        {
            throw new \Exception('処理対象が選択されていません。');
        }
        // すでにDispatchされていたらリダイレクトさせる
        if ($job_status->is_import_start)
        {
            \Session::flash('warn_message', 'すでに処理は開始されています。');
            return redirect(route('admin::super::month::import', ['id' => $id, 'job_id' => $job_id]));
        }
        $this->service->resetProcessStatus($process_ids);
        $this->service->setImportStartToJobStatus($job_status->id);
        try {
            $this->dispatch(new \App\Jobs\Suisin\CsvUpload($id, $process_ids, $job_id));
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return redirect()->route('admin::super::month::import', ['id' => $id, 'job_id' => $job_id]);
    }

    public function dispatchCopyJob($id) {
        $job = $this->service->createJobStatus();
        try {
            $this->dispatch(new \App\Jobs\Suisin\CsvFileCopy($id, $job->id));
        } catch (\Exception $e) {
            \Session::flash('danger_message', $e->getMessage());
            return back();
        }
        return redirect()->route('admin::super::month::copy', ['id' => $id, 'job_id' => $job->id]);
    }

    public function copyAjax($id, $job_id) {
        $status = $this->service->getNowJobStatusArray($job_id);
        return response()->json(['status' => $status, 'id' => $id], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function importAjax($id, $job_id) {
        $status                     = $this->service->getNowJobStatusArray($job_id);
        $csv_file_processing_status = $this->service->getNowMonthlyStatusArray($job_id);

        $param = ['rows' => $csv_file_processing_status, /* 'max_cnt' => $max_cnt, 'now_cnt' => $now_cnt, */ 'status' => $status,];
        return response()->json($param, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function exportProcessList($id) {
        $rows = $this->service->setRows($id)->getRows()->get();
        if ($rows->isEmpty())
        {
            \Session::flash('warn_message', "ファイルリストが存在しないようです。");
            return back();
        }
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
        $file_name = "{$id}_月次データ処理リスト_" . date('Ymd_His') . '.csv';
        return $this->service->exportCsv($lists, $file_name, $headers);
    }

    public function exportNothingList($id) {
        try {
            $ignore = $this->getJsonFile(storage_path() . '/jsonlogs/', "{$id}_ignore_file_list.json");
        } catch (\Exception $exc) {
            \Session::flash('warn_message', "ファイルリストが存在しないようです。");
            return back();
        }
        try {
            $not_exist = $this->getJsonFile(storage_path() . '/jsonlogs/', "{$id}_not_exist_file_list.json");
        } catch (\Exception $exc) {
            \Session::flash('warn_message', "ファイルリストが存在しないようです。");
            return back();
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
        $file_name = "{$id}_月次データ処理対象外リスト_" . date('Ymd_His') . '.csv';
        return $this->service->exportCsv($lists, $file_name, $headers);
    }

    public function showConsignors($monthly_id) {
        $sql        = "consignor_code, COUNT(*) as total_count, MAX(consignor_name) as consignor_name, MAX(scheduled_transfer_payment_on) as reference_last_traded_on, MAX(last_traded_on) as last_traded_on";
        $consignors = \App\Jifuri::where(['monthly_id' => $monthly_id])->select(\DB::raw($sql))->groupBy('consignor_code')->orderBy('consignor_code', 'asc')->paginate(50);
        return view('admin.month.consignor.list', ['consignors' => $consignors, 'monthly_id' => $monthly_id]);
    }

    public function createConsignors($monthly_id) {
        // 委託者マスタ創生
        try {
            \DB::connection('mysql_zenon')->transaction(function() use ($monthly_id) {
                $sql        = "consignor_code, COUNT(*) as total_count, MAX(consignor_name) as consignor_name, MAX(scheduled_transfer_payment_on) as reference_last_traded_on, MAX(last_traded_on) as last_traded_on";
                $consignors = \App\Jifuri::where(['monthly_id' => $monthly_id])->select(\DB::raw($sql))->groupBy('consignor_code')->orderBy('consignor_code', 'asc')->get();
                foreach ($consignors as $cns) {
                    $keys      = ['consignor_code' => $cns->consignor_code];
                    $table     = \App\Consignor::firstOrNew($keys);
                    $last_date = (empty($cns->reference_last_traded_on) || $cns->reference_last_traded_on === '0000-00-00' || $cns->reference_last_traded_on === '00000000') ?
                            $cns->last_traded_on :
                            $cns->reference_last_traded_on
                    ;

                    $table->consignor_code           = $cns->consignor_code;
                    $table->consignor_name           = $cns->consignor_name;
                    $table->total_count              = $cns->total_count;
                    $table->reference_last_traded_on = $last_date;
                    $table->save();
                }
            });
        } catch (\Exception $e) {
            // エラー発生時、フラグをリセット
            echo $e->getMessage();
            echo '[ ' . date('Y-m-d H:i:s') . ' ]' . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
            exit();
        }

        \Session::flash('success_message', "データの更新が正常に終了しました。");
        return back();
    }

    public function deleteList($monthly_id) {
        return view('admin.month.delete_list', ['monthly_id' => $monthly_id]);
    }

    public function deleteConfirm($term_status, $monthly_id) {
        $cycle = substr(strtoupper($term_status), 0, 1);

        $table_lists = \App\ZenonMonthlyStatus::join('suisin_db.zenon_data_csv_files', 'zenon_data_monthly_process_status.zenon_data_csv_file_id', '=', 'zenon_data_csv_files.id')
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                ->where('monthly_id', '=', $monthly_id)
                ->where('cycle', '=', $cycle)
                ->where('table_name', '<>', '')
                ->get()
        ;
        return view('admin.month.delete_confirm', ['term_status' => $term_status, 'monthly_id' => $monthly_id, 'table_lists' => $table_lists]);
    }

    public function delete(\App\Http\Requests\Suisin\TableDelete $input) {
        $table_ids = $input['tables'];
        // dispachしますよー
        $email     = \Auth::user()->email;
        $this->dispatch(new \App\Jobs\Suisin\TableDelete($table_ids, $email));
        \Session::flash('success_message', "データの削除が開始されました。処理結果はメールアドレス（{$email}）にお送りいたします。");
        return redirect()->route('admin::super::month::show');
    }

}
