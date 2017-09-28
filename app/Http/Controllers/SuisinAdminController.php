<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TableEditService;

class SuisinAdminController extends Controller
{

    protected $service;

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
        $service               = $this->service;
        $model                 = $service->setHtmlPageGenerateConfigs("App\Services\\{$system}CsvConfigService", $category)->getModel();
        $conf                  = $service->getHtmlPageGenerateParameter();
        $conf['table_columns'] = $service->getTopPageTableSettings();
        $rows                  = $model->paginate(25);
//        $rows                  = $model->toSql();
//        dd($rows);
//        $conf['import_route']  = $service->getImportRoute();
//        $conf['export_route']  = $service->getExportRoute();
        $view                  = strtolower($system) . '.admin.list';
        return view($view, ['rows' => $rows, 'configs' => $conf]);
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

        $file_name   = $conf['title'] . date('_Ymd_His') . '.csv';
        $export_rows = $service->getExportRows();
        return $service->exportCsv($export_rows, $file_name, $jp_columns);
//        return $service->setConfigs("App\Services\\{$system}CsvConfigService", $category)->exportRow();
//        } catch (\Exception $e) {
//            echo $e->getTraceAsString();
//        }
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
        $service          = $this->service;
//        $rows             = $service->setHtmlPageGenerateConfigs("App\Services\\{$system}CsvConfigService", $category)->setCsvFile(\Request::file('csv_file'))->convertCsvFileToArray();
        $csv_file_object  = $service->setHtmlPageGenerateConfigs("App\Services\\{$system}CsvConfigService", $category)
                ->setCsvFileObjectFromRequest(\Request::file('csv_file'))
                ->getCsvFileObject()
        ;
        $rows             = $service->convertCsvFileToArray(/* language = */'en', /* is_header_exist = */ true, /* csv_file_object = */ $csv_file_object);
        $page_settings    = $service->getHtmlPageGenerateParameter();
        $import_setttings = $service->getImportSettings();
        $rules            = $service->makeValidationRules($rows);
        $validator        = \Validator::make($rows, $rules);

//        dd($rows);
        $page_settings['title']         = '確認 - ' . $page_settings['title'];
        $page_settings['h2']            = "CSVファイル確認 <small> - {$service->getFileName()}</small>";
        $page_settings['key']           = $import_setttings['keys'];
        $page_settings['table_columns'] = $import_setttings['table_columns'];
        $view = strtolower($system) . '.admin.import';
        if ($validator->fails())
        {
//            dd($validator);
            \Session::flash('danger_message', "CSVファイルの内容に不備がありました。");
//            return view($view, ['configs' => $page_settings, 'rows' => $rows]);
            return back()->withErrors($validator);
        }
        \Session::flash('warn_message', 'CSVデータの取り込みが完了しました。引き続き更新処理を行ってください。');
        return view($view, ['configs' => $page_settings, 'rows' => $rows]);
    }

    /**
     *   関数名     ： upload
     *   内容       ： 取り込んだCSVファイルをデータベースに反映させる関数
     *   アクション ： POST
     *   インプット ： フォーム
     *   役割       ： 推進支援システム管理者
     *   備考       ： 成功時・失敗時共にshow画面へ
     */
    public function upload($system, $category) {
        $service       = $this->service;
        $input         = \Input::except(['_token']);
        $service->setHtmlPageGenerateConfigs("App\Services\\{$system}CsvConfigService", $category);
        $page_settings = $service->getHtmlPageGenerateParameter();


        try {
            \DB::connection('mysql_master')->beginTransaction();
            \DB::connection('mysql_suisin')->beginTransaction();
//            dd($input);
            $cnt = $service->uploadToDatabase($input, 'mysql_zenon');
            \DB::connection('mysql_master')->commit();
            \DB::connection('mysql_suisin')->commit();
        } catch (\Exception $exc) {
            \DB::connection('mysql_master')->rollback();
            \DB::connection('mysql_suisin')->rollback();
            \Session::flash('danger_message', $exc->getMessage());
            return back();
        }
        \Session::flash('flash_message', ($cnt['insert_count'] + $cnt['update_count']) . "件の処理が終了しました。（新規：{$cnt['insert_count']}件，更新：{$cnt['update_count']}件）");
        return redirect($page_settings['index_route']);

//        $after_rows = $service->swapPostColumnAndRow($input);
//        dd($after_rows);
//        dd($input);
//        $cnt     = $param   = $service->setConfigs("App\Services\\{$system}CsvConfigService", $category)->updatePost($input);
//        \Session::flash('flash_message', ($cnt['insert_count'] + $cnt['update_count']) . "件の処理が終了しました。（新規：{$cnt['insert_count']}件，更新：{$cnt['update_count']}件）");
//        return redirect($service->getIndexRoute());
    }

}
