<?php

namespace App\Services;

//use App\Http\Requests;

class RosterCsvConfigService
{

    protected $param;
    protected $route = '/admin/roster/config/Roster';

    public function getter($category) {
        $function = 'get' . \Input::get('category');
        $function = 'get' . $category;
        $this->$function();

        return $this->param;
    }

    public function getDivision() {
        $params      = [
            'object'        => '\App\Division',
            'display'       => [
                'title' => '部署マスタ',
                'route' => $this->route . '/Division',
                'h2'    => '部署マスタ',
            ],
            'table_columns' => [
                ['row' => [['division_id', '部署コード']]],
                ['row' => [['division_name', '部署名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'division_id' => 'asc',
            ],
            'csv'           => [
                'columns'       => [
                    'division_id',
                    'division_name',
                ],
                'kanji_columns' => [
                    '部署コード',
                    '部署名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'division_id', '部署コード'],
                    [1, 'division_name', '部署名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'division_id'   => 'required|integer',
                    'division_name' => 'required|min:1',
                ],
                'types'         => [
                    'division_id' => 'integer',
                ],
                'flags'         => [
                    'division_id'   => 1,
                    'division_name' => 1,
                ],
                'keys'          => ['division_id'],
            ],
        ];
        $this->param = $params;
    }

    public function getWorkType() {
        $params      = [
            'object'        => '\App\WorkType',
            'display'       => [
                'title' => '勤務時間マスタ',
                'route' => $this->route . '/WorkType',
                'h2'    => '勤務時間マスタ',
            ],
            'table_columns' => [
                ['row' => [['work_type_id', '勤務形態コード']]],
                ['row' => [['work_type_name', '勤務形態名', 'class' => 'text-left']]],
                ['row' => [['work_start_time', '勤務開始時間', 'class' => 'text-left'],]],
                ['row' => [['work_end_time', '勤務終了時間', 'class' => 'text-left'],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'work_type_id' => 'asc',
            ],
            'csv'           => [
                'columns'       => [
                    'work_type_id',
                    'work_type_name',
                    'work_start_time',
                    'work_end_time',
                ],
                'kanji_columns' => [
                    '勤務形態コード',
                    '勤務形態名',
                    '勤務開始時間',
                    '勤務終了時間',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'work_type_id', '勤務形態コード',],
                    [1, 'work_type_name', '勤務形態名', 'class' => 'text-left'],
                    [1, 'work_start_time', '勤務開始時間', 'class' => 'text-left'],
                    [1, 'work_end_time', '勤務終了時間', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'work_type_id'   => 'required|integer',
                    'work_type_name' => 'required|min:1',
                ],
                'types'         => [
                    'work_type_id'    => 'integer',
                    'work_start_time' => 'time',
                    'work_end_time'   => 'time',
                ],
                'flags'         => [
                    'work_type_id'    => 1,
                    'work_type_name'  => 1,
                    'work_start_time' => 1,
                    'work_end_time'   => 1,
                ],
                'keys'          => ['work_type_id'],
            ],
        ];
        $this->param = $params;
    }

    public function getRest() {
        $params      = [
            'object'        => '\App\Rest',
            'display'       => [
                'title' => '休暇マスタ',
                'route' => $this->route . '/Rest',
                'h2'    => '休暇マスタ',
            ],
            'table_columns' => [
                ['row' => [['rest_reason_id', '休暇理由コード',]]],
                ['row' => [['rest_reason_name', '休暇理由名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'rest_reason_id' => 'asc',
            ],
            'csv'           => [
                'columns'       => [
                    'rest_reason_id',
                    'rest_reason_name',
                ],
                'kanji_columns' => [
                    '休暇理由コード',
                    '休暇理由名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'rest_reason_id', '休暇理由コード',],
                    [1, 'rest_reason_name', '休暇理由名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'rest_reason_id'   => 'required|integer',
                    'rest_reason_name' => 'required|min:1',
                ],
                'types'         => [
                    'rest_reason_id' => 'integer',
                ],
                'flags'         => [
                    'rest_reason_id'   => 1,
                    'rest_reason_name' => 1,
                ],
                'keys'          => ['rest_reason_id',],
            ],
        ];
        $this->param = $params;
    }

    public function getHoliday() {
        $params      = [
            'object'        => '\App\Holiday',
            'display'       => [
                'title' => '休日マスタ',
                'route' => $this->route . '/Holiday',
                'h2'    => '休日マスタ',
            ],
            'table_columns' => [
                ['row' => [['holiday', '祝日',]]],
                ['row' => [['holiday_name', '祝日名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'holiday' => 'desc',
            ],
            'csv'           => [
                'columns'       => [
                    'holiday',
                    'holiday_name',
                ],
                'kanji_columns' => [
                    '祝日',
                    '祝日名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'holiday', '祝日',],
                    [1, 'holiday_name', '祝日名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'holiday'      => 'required|date',
                    'holiday_name' => 'required|min:1',
                ],
                'types'         => [
                    'holiday' => 'date',
                ],
                'flags'         => [
                    'holiday'      => 1,
                    'holiday_name' => 1,
                ],
                'keys'          => ['holiday'],
            ],
        ];
        $this->param = $params;
    }

}