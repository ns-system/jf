<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TableEditService;

class SuisinAdminController extends Controller
{

    protected $service;

    const INT_PAGINATE = 50;

    public function __construct() {
        $this->service = new TableEditService();
    }

    public function index($system) {
        $rows = \App\DatabaseLog::orderBy('updated_at', 'desc')->take(10)->get();
        $view = strtolower($system) . '.admin.home';
        return view($view, ['rows' => $rows]);
    }

    /**
     *   関数名 ： show
     *   内容   ：データベース内のマスタを表示する関数
     *   役割   ： 推進支援システム管理者
     *   備考   ： Provider側でパラメータ生成処理を行っている
     */
    public function show($system, $category) {
        $input                 = \Input::get();
        $service               = $this->service;
        $service->setHtmlPageGenerateConfigs("App\Services\\{$system}CsvConfigService", $category);
        $conf                  = $service->getHtmlPageGenerateParameter();
        $conf['table_columns'] = $service->getTopPageTableSettings();
        $search_columns        = $service->getSerachColumns();
        $rows                  = $service->searchModel($input)->getModel()->paginate(self::INT_PAGINATE);
        $view                  = strtolower($system) . '.admin.list';
        return view($view, ['rows' => $rows, 'configs' => $conf, 'serach_columns' => $search_columns, 'search_values' => $input, 'system' => $system, 'category' => $category]);
    }

    /**
     *   関数名     ： export
     *   内容       ：データベース内のマスタをCSVに出力する関数
     *   アクション ： GET
     *   役割       ： 推進支援システム管理者
     *   備考       ： Provider側でパラメータ生成処理を行っている
     */
    public function export($system, $category) {

//        try {
        $service    = $this->service;
        $jp_columns = $service->setHtmlPageGenerateConfigs("App\Services\\{$system}CsvConfigService", $category)->getJpColumns();
        $conf       = $service->getHtmlPageGenerateParameter();
        $file_name  = $conf['title'] . date('_Ymd_His') . '.csv';
        try {
            $export_rows = $service->getExportRows();
            return $service->exportCsv($export_rows, $file_name, $jp_columns);
        } catch (\Exception $e) {
            \Session::flash('danger_message', $e->getMessage());
            return back();
        }
    }

    /**
     *   関数名     ： import
     *   内容       ： CSVファイルを一度取り込んでプレビューする関数
     *   アクション ： POST
     *   インプット ： CSVファイル
     *   役割       ： 推進支援システム管理者
     *   備考       ： checkCsvFile関数で簡単なバリデーションを行っている
     */
    public function import($system, $category/* , CsvFile $request */) {
        $service = $this->service;
        try {
            $csv_file_object  = $service->setHtmlPageGenerateConfigs("App\Services\\{$system}CsvConfigService", $category)
                    ->setCsvFileObjectFromRequest(\Request::file('csv_file'))
                    ->getCsvFileObject()
            ;
            $page_settings    = $service->getHtmlPageGenerateParameter();
            $rows             = $service->convertCsvFileToArray(/* language = */'en', /* is_header_exist = */ true, /* csv_file_object = */ $csv_file_object);
            $import_setttings = $service->getImportSettings();
            $rules            = $service->makeValidationRules($rows);
        } catch (\Exception $e) {
            \Session::flash('danger_message', $e->getMessage());
            return back();
        }

        // バリデーションチェック処理
        $validator = \Validator::make($rows, $rules);
        if ($validator->fails())
        {
            \Session::flash('danger_message', "CSVファイルの内容に不備がありました。");
            return back()->withErrors($validator);
        }

        $page_settings['title']         = '確認 - ' . $page_settings['title'];
        $page_settings['h2']            = "CSVファイル確認 <small> - {$service->getFileName()}</small>";
        $page_settings['key']           = $import_setttings['keys'];
        $page_settings['table_columns'] = $import_setttings['table_columns'];
        $view                           = strtolower($system) . '.admin.import';
//        \Session::flash('success_message', 'CSVデータの取り込みが完了しました。');
//        \Session::flash('warn_message', '現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。');

        $email = \Auth::user()->email;
        try {
            $this->dispatch(new \App\Jobs\Suisin\MasterUpload($system, $category, $rows, $page_settings, $email, $service->getFileName(), true));
        } catch (\Exception $e) {
            \Session::flash('danger_message', $e->getMessage());
            return back();
        }

        \Session::flash('success_message', 'CSVインポート処理を開始しました。処理結果はメールにて通知いたします。');
        return view($view, ['configs' => $page_settings, 'rows' => $rows]);
    }

    public function delete($system, $category, \App\Http\Requests\ConfigDelete $request) {
        $service = $this->service;
        $service->setHtmlPageGenerateConfigs("App\Services\\{$system}CsvConfigService", $category);
        $conf    = $service->getHtmlPageGenerateParameter();
        $model   = $service->getPlaneModel();
        $email   = \Auth::user()->email;
        try {
            $this->dispatch(new \App\Jobs\Suisin\TruncateMaster(
                    $model->getConnectionName(), $model->getTable(), $email, $conf['h2'])
            );
        } catch (\Exception $e) {
            \Session::flash('danger_message', $e->getMessage());
            return back();
        }
        \Session::flash('warn_message', '削除処理を開始しました。処理結果はメールにて通知いたします。反映までにしばらく時間がかかる場合があります。');
        return back();
    }

}
