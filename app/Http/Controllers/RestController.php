<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class RestController extends Controller
{

    protected $route = 'roster/rest';
    protected $title = '休暇理由';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show() {
        $gists   = \App\Rest::paginate(50);
        $configs = [
            'title'         => $this->title,
            'brand'         => $this->title,
            'import_route'  => '/admin/' . $this->route . '/import',
            'export_route'  => '/admin/' . $this->route . '/export',
            'h2'            => $this->title,
            'table_columns' => [
                ['row' => [
                        ['rest_reason_id', '理由コード', 'format' => '%02d']
                    ]
                ],
                ['row' => [
                        ['rest_reason_name', '理由', 'class' => 'text-left']
                    ]
                ],
                ['row' => [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ]
        ];
        return view('roster.admin.list', ['rows' => $gists, 'configs' => $configs]);
    }

    public function export() {
        $columns = [
            'rest_reason_id',
            'rest_reason_name',
        ];

        $titles     = [
            '休暇理由コード',
            '休暇理由',
        ];
        $consignors = \App\Rest::get($columns)->toArray();
        $obj        = new \App\Providers\CsvServiceProvider();
        return $obj->exportCsv($consignors, '休暇理由_' . date('Ymd_His') . '.csv', $titles);
    }

    public function import() {
        $titles = [
            0 => 'rest_reason_id',
            1 => 'rest_reason_name',
        ];
        $errors = [];

        $obj       = new \App\Providers\CsvServiceProvider();
        $csv_datas = $obj->importCsv('csv_file', count($titles));
//        var_dump($csv_datas);
        $datas     = [];
        foreach ($csv_datas as $key => $line) {
            $data  = [];
            $error = '';
            foreach ($line as $i => $buf) {
//                if ($this->checkCsvFile($i, $buf) != '')
//                {
//                    $error = $this->checkCsvFile($i, $buf);
//                }
                $data[$titles[$i]] = $buf;
            }
            if ($error === '')
            {
                $data['is_execute'] = true;
            }
            else
            {
                $data['is_execute'] = false;
            }
            $data['error'] = $error;
            $datas[$key]   = $data;
        }
        $configs = [
            'title'         => $this->title,
            'brand'         => $this->title,
            'form_route'    => '/admin/' . $this->route . '/import/upload',
            'h2'            => "CSVファイル確認<small> － {$obj->getFileName()}</small>",
            'key'           => ['rest_reason_id',],
            'table_columns' => [
                [1, 'rest_reason_id', '休暇理由コード', 'format' => '%02d'],
                [1, 'rest_reason_name', '休暇理由', 'class' => 'text-left'],
            ]
        ];
        \Session::flash('flash_message', 'CSVデータの取り込みが完了しました。');
        return view('roster.admin.import', ['rows' => $datas, 'configs' => $configs]);
    }

    public function upload() {
        $input = \Input::all();
//        echo count($input['bankbook_deed_code']);
//        var_dump($input);
//        exit();
        $types = [
            'rest_reason_id' => 'integer',
        ];
        $flags = [
            'rest_reason_id'   => 1,
            'rest_reason_name' => 1,
        ];
        $obj   = new \App\Providers\CsvServiceProvider;
        $cnt   = $obj
                ->swapColumnRow($input)
                ->convertTypes($types)
                ->setUpdateFlags($flags)
                ->setPrimaryKey(['rest_reason_id',])
                ->reflectToDb('\App\Rest')
                ->getCount()
        ;
//        var_dump($cnt);
//        exit();
        \Session::flash('flash_message', ($cnt['insert_count'] + $cnt['update_count']) . "件の処理が終了しました。（新規：{$cnt['insert_count']}件，更新：{$cnt['update_count']}件）");
        return \Redirect::to(url('/admin/' . $this->route));
    }

}
