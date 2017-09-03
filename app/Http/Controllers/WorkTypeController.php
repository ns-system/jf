<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class WorkTypeController extends Controller
{

    protected $route = 'roster/work_type';
    protected $title = '勤務形態';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showWorkType() {
        $gists   = \App\WorkType::paginate(50);
        $configs = [
            'title'         => $this->title,
            'brand'         => $this->title,
            'import_route'  => '/admin/' . $this->route . '/import',
            'export_route'  => '/admin/' . $this->route . '/export',
            'h2'            => $this->title,
            'table_columns' => [
                ['row' => [
                        ['work_type_id', '勤務コード', 'format' => '%02d']
                    ]
                ],
                ['row' => [
                        ['work_type_name', '勤務形態', 'class' => 'text-left']
                    ]
                ],
                ['row' => [
                        ['work_start_time', '勤務開始時間', 'class' => 'text-left'],
                        ['work_end_time', '勤務終了時間', 'class' => 'text-left'],
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

    public function exportWorkType() {
        $columns = [
            'work_type_id',
            'work_type_name',
            'work_start_time',
            'work_end_time',
        ];

        $titles     = [
            'No',
            '勤務形態名',
            '勤務開始時間',
            '勤務終了時間',
        ];
        $consignors = \App\WorkType::get($columns)->toArray();
        $obj        = new \App\Providers\CsvServiceProvider();
        return $obj->exportCsv($consignors, '通証区分_' . date('Ymd_His') . '.csv', $titles);
    }

    public function importWorkType() {
        $titles = [
            0 => 'work_type_id',
            1 => 'work_type_name',
            2 => 'work_start_time',
            3 => 'work_end_time',
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
            'key'           => ['work_type_id',],
            'table_columns' => [
                [1, 'work_type_id', '勤務形態コード', 'format' => '%02d'],
                [1, 'work_type_name', '勤務形態名', 'class' => 'text-left'],
                [1, 'work_start_time', '勤務開始時間', 'class' => 'text-left'],
                [1, 'work_end_time', '勤務終了時間', 'class' => 'text-left'],
            ],
        ];
        \Session::flash('flash_message', 'CSVデータの取り込みが完了しました。');
        return view('roster.admin.import', ['rows' => $datas, 'configs' => $configs]);
    }

    public function uploadWorkType() {
        $input = \Input::all();
//        echo count($input['bankbook_deed_code']);
//        var_dump($input);
//        exit();
        $types = [
            'work_type_id'    => 'integer',
            'work_start_time' => 'time',
            'work_end_time'   => 'time',
        ];
        $flags = [
            'work_type_id'    => 1,
            'work_type_name'  => 1,
            'work_start_time' => 1,
            'work_end_time'   => 1,
        ];
        $obj   = new \App\Providers\CsvServiceProvider;
        $cnt   = $obj
                ->swapColumnRow($input)
                ->convertTypes($types)
                ->setUpdateFlags($flags)
                ->setPrimaryKey(['work_type_id',])
                ->reflectToDb('\App\WorkType')
                ->getCount()
        ;
//        var_dump($cnt);
//        exit();
        \Session::flash('flash_message', ($cnt['insert_count'] + $cnt['update_count']) . "件の処理が終了しました。（新規：{$cnt['insert_count']}件，更新：{$cnt['update_count']}件）");
        return \Redirect::to(url('/admin/' . $this->route));
    }

}
