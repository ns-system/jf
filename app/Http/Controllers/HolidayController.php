<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class HolidayController extends Controller
{

    protected $route = 'roster/holiday';
    protected $title = '祝日リスト';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show() {
        $gists   = \App\Holiday::paginate(50);
        $configs = [
            'title'         => $this->title,
            'brand'         => $this->title,
            'import_route'  => '/admin/' . $this->route . '/import',
            'export_route'  => '/admin/' . $this->route . '/export',
            'h2'            => $this->title,
            'table_columns' => [
//                ['row' => [
//                        ['id', 'No', 'format' => '%02d']
//                    ]
//                ],
                ['row' => [
                        ['holiday', '祝日', 'class' => 'text-left']
                    ]
                ], ['row' => [
                        ['holiday_name', '祝日名', 'class' => 'text-left']
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
            'id',
            'holiday',
            'holiday_name',
        ];

        $titles     = [
            'No',
            '祝日',
            '祝日名',
        ];
        $consignors = \App\Holiday::get($columns)->toArray();
        $obj        = new \App\Providers\CsvServiceProvider();
        return $obj->exportCsv($consignors, '祝日マスタ_' . date('Ymd_His') . '.csv', $titles);
    }

    public function import() {
        $titles = [
            'holiday',
            'holiday_name',
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
            'key'           => ['holiday',],
            'table_columns' => [
//                [1, 'id', 'No', 'format' => '%02d'],
                [1, 'holiday', '祝日', 'class' => 'text-left'],
                [1, 'holiday_name', '祝日名', 'class' => 'text-left'],
            ],
        ];
        \Session::flash('flash_message', 'CSVデータの取り込みが完了しました。');
//        exit();
        return view('roster.admin.import', ['rows' => $datas, 'configs' => $configs]);
    }

    public function upload() {
        $input = \Input::all();
//        echo count($input['bankbook_deed_code']);
//        var_dump($input);
//        exit();
        $types = [
//            'id'      => 'integer',
            'holiday' => 'date',
        ];
        $flags = [
            'holiday'      => 1,
            'holiday_name' => 1,
        ];
        $obj   = new \App\Providers\CsvServiceProvider;
        $cnt   = $obj
                ->swapColumnRow($input)
                ->convertTypes($types)
                ->setUpdateFlags($flags)
                ->setPrimaryKey(['holiday',])
                ->reflectToDb('\App\Holiday')
                ->getCount()
        ;
//        var_dump($cnt);
//        exit();
        \Session::flash('flash_message', ($cnt['insert_count'] + $cnt['update_count']) . "件の処理が終了しました。（新規：{$cnt['insert_count']}件，更新：{$cnt['update_count']}件）");
        return \Redirect::to(url('/admin/' . $this->route));
    }

}
