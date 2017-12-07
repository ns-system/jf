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
        return view('admin.month.index', ['rows' => $rows, 'counts' => $cnts, 'months' => $months, 'job_status' => $job_status]);
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
        $csv_service = new CopyCsvFileService();

        $tmp_dir     = $this->path . '/temp';
        $monthly_dir = $this->path . "/monthly/{$id}";

        try {
            $tmp_lists     = $csv_service->getCsvFileList($tmp_dir);
            $monthly_lists = $csv_service->getCsvFileList($monthly_dir);
        } catch (\Exception $exc) {
            \Session::flash('danger_message', '一時ディレクトリもしくは月次ディレクトリが見つかりませんでした。管理者に問い合わせてください。');
            return back();
        }

        // 月次処理ステータスを月別IDで検索して、件数が0以上であればコピー処理をスキップする
        // 件数が0件かつリストが存在しない場合 -> エラーとして処理を行わない
        if (empty($tmp_lists) && empty($monthly_lists))
        {
            \Session::flash('danger_message', '所定のディレクトリに当月中のCSVファイルが見つかりませんでした。手順に沿って再度処理を行ってください。');
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
        array_multisort(array_column($monthly_lists, 'cycle'), SORT_ASC, array_column($monthly_lists, 'identifier'), SORT_ASC, $monthly_lists);
        return view('admin.month.copy_confirm', ['id' => $id, 'tmp_lists' => $tmp_lists, 'monthly_lists' => $monthly_lists]);
    }

    public function copy($id, $job_id) {
        return view('admin.month.copy', ['id' => $id, 'job_id' => $job_id]);
    }

    public function importConfirm($monthly_id, $job_id) {
        $files = $this->service->setRows($monthly_id)
                ->getRows()
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                ->where('cycle', '=', 'M')
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
//        dd($column_counts);
        return view('admin.month.import_confirm', ['files' => $files, 'id' => $monthly_id, 'job_id' => $job_id, 'record_counts' => $record_counts, 'column_counts' => $column_counts]);
    }

    public function import($id, $job_id) {
        $rows = $this->service->getProcessRows($job_id)
                ->select(\DB::raw('*, zenon_data_monthly_process_status.id as key_id'))
                ->where('is_execute', '=', true)
                ->orderBy('zenon_format_id', 'asc')
                ->orderBy('key_id', 'asc')
                ->get()
        ;
//        var_dump($job_id);
//        dd($rows);
        $job  = \App\JobStatus::find($job_id);
        if ($job->is_import_end)
        {
            \Session::flash('success_message', '処理は終了しています。');
        }
//        else if ($job->is_import_start)
//        {
//            dd($job);
//            \Session::flash('warn_message', 'すでに処理は開始されています。');
//        }
//        var_dump($job);
        return view('admin.month.import', ['id' => $id, 'rows' => $rows, 'job_id' => $job_id]);
    }

    public function dispatchImportJob($id, $job_id, MonthlyImportForm $request) {
        $in          = $request->only(['process']);
//        dd($in);
        $job_status  = $this->service->setJobStatus($job_id)->getJobStatus();
        $process_ids = array_keys($in['process']);
        $rows        = $this->service->setJobStatusIdToMonthlyStatus($process_ids, $job_id)->getProcessRows($job_id)->get();
//        dd($rows);
        if (!isset($rows))
        {
            throw new \Exception('処理対象が選択されていません。');
        }
        // すでにDispatchされていたらリダイレクトさせる
//        $job = \App\JobStatus::find($job_id);
        if ($job_status->is_import_start)
        {
            \Session::flash('warn_message', 'すでに処理は開始されています。');
            return redirect(route('admin::super::month::import', ['id' => $id, 'job_id' => $job_id]));
        }
        $this->service->resetProcessStatus($process_ids);
        $this->service->setImportStartToJobStatus($job_status->id);
//
//        $this->service->resetProcessStatus($rows, $id);
        try {
            $this->dispatch(new \App\Jobs\Suisin\CsvUpload($id, $process_ids, $job_id));
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return redirect()->route('admin::super::month::import', ['id' => $id, 'job_id' => $job_id]);
    }

    public function dispatchCopyJob($id) {
//        $job = \App\JobStatus::create(['is_copy_start' => true]);
        $job = $this->service->createJobStatus();
        try {
            $this->dispatch(new \App\Jobs\Suisin\CsvFileCopy($id, $job->id));
        } catch (\Exception $e) {
            \Session::flash('danger_message', $e->getMessage());
            return back();
//            echo $exc->getTraceAsString();
        }
        return redirect()->route('admin::super::month::copy', ['id' => $id, 'job_id' => $job->id]);
    }

    public function copyAjax($id, $job_id) {
        $status = $this->service->getNowJobStatusArray($job_id);
        return response()->json(['status' => $status, 'id' => $id], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function importAjax($id, $job_id) {
//        $in      = \Input::only('input')['input'];
        $status                     = $this->service->getNowJobStatusArray($job_id);
        $csv_file_processing_status = $this->service->getNowMonthlyStatusArray($job_id);

        $param = ['rows' => $csv_file_processing_status, /* 'max_cnt' => $max_cnt, 'now_cnt' => $now_cnt, */ 'status' => $status,];
        return response()->json($param, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function exportProcessList($id) {
        $rows  = $this->service->setRows($id)->getRows()->get();
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
            \Session::flash('danger_message', $exc->getMessage());
            return back();
//            dd($exc->getMessage());
//            $ignore = [];
        }
        try {
//            $not_exist = $this->getJsonFile($this->path . '/log/', "{$id}_not_exist_file_list.json");
            $not_exist = $this->getJsonFile(storage_path() . '/jsonlogs/', "{$id}_not_exist_file_list.json");
        } catch (\Exception $exc) {
            \Session::flash('danger_message', $exc->getMessage());
            return back();
//            $not_exist = [];
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

}
