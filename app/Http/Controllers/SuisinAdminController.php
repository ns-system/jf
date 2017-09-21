<?php

namespace App\Http\Controllers;

use App\Http\Requests\CsvFile;
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
        $service = $this->service;
        $rows    = $service->setConfigs("App\Services\\{$system}CsvConfigService", $category)->getModel();
        $rows    = $service->order($rows)->paginate(25);
        $conf    = $service->getListConfig();
        $view    = strtolower($system) . '.admin.list';
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
            $service = $this->service;
            return $service->setConfigs("App\Services\\{$system}CsvConfigService", $category)->exportRow();
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

//        try {
        $service   = $this->service;
        $service->setConfigs("App\Services\\{$system}CsvConfigService", $category)->makeRowAndValidateRules(\Request::file('csv_file'));
        $validator = $service->getValidate();
        if ($validator !== true)
        {
            return back()->withErrors($validator);
        }
        $param = $service->getImportParameter();
        \Session::flash('flash_message', 'CSVデータの取り込みが完了しました。引き続き更新処理を行ってください。');
        $view  = strtolower($system) . '.admin.import';
//        var_dump($param);
//        exit();
        return view($view, $param);
//        } catch (\Exception $e) {
//            
//        }
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
        $service = $this->service;
        $input   = \Input::all();
        $cnt     = $param   = $service->setConfigs("App\Services\\{$system}CsvConfigService", $category)->updatePost($input);
        \Session::flash('flash_message', ($cnt['insert_count'] + $cnt['update_count']) . "件の処理が終了しました。（新規：{$cnt['insert_count']}件，更新：{$cnt['update_count']}件）");
        return redirect($service->getIndexRoute());
    }

}
