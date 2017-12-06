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
            'join'          => [],
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
            'table_search'  => [
                /**
                 * key         = インプットフォーム名
                 * column_name = クエリ検索時のカラム名
                 * display     = ラベル名
                 * type        = 型（string型のみlike検索）
                 */
                'division_id'   => ['column_name' => 'division_id', 'display' => '部署コード', 'type' => 'integer'],
                'division_name' => ['column_name' => 'division_name', 'display' => '部署名', 'type' => 'string'],
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
                    /* POST_flag, kanji_name, [format], [class] */
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
            'join'          => [],
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
            'table_search'  => [
                'work_type_id'    => ['column_name' => 'work_type_id', 'display' => '勤務形態コード', 'type' => 'integer'],
                'work_type_name'  => ['column_name' => 'work_type_name', 'display' => '勤務形態名', 'type' => 'string'],
                'work_start_time' => ['column_name' => 'work_start_time', 'display' => '勤務開始時間', 'type' => 'string'],
                'work_end_time'   => ['column_name' => 'work_end_time', 'display' => '勤務終了時間', 'type' => 'string'],
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
                    /* POST_flag, kanji_name, [format], [class] */
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
            'join'          => [],
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
            'table_search'  => [
                'rest_reason_id'   => ['column_name' => 'rest_reason_id', 'display' => '休暇理由コード', 'type' => 'integer'],
                'rest_reason_name' => ['column_name' => 'rest_reason_name', 'display' => '休暇理由名', 'type' => 'string'],
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
                    /* POST_flag, kanji_name, [format], [class] */
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
            'join'          => [],
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
            'table_search'  => [
                'holiday'      => ['column_name' => 'holiday', 'display' => '祝日', 'type' => 'string'],
                'holiday_name' => ['column_name' => 'holiday_name', 'display' => '祝日名', 'type' => 'string'],
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
                    /* POST_flag, kanji_name, [format], [class] */
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

    public function getRosterUser() {
        $params      = [
            'object'        => '\App\RosterUser',
            'join'          => [
                ['db' => 'laravel_db.users', 'left' => 'roster_users.user_id', 'right' => 'users.id',],
                ['db' => 'sinren_db.sinren_users', 'left' => 'roster_users.user_id', 'right' => 'sinren_users.user_id',],
                ['db' => 'sinren_db.sinren_divisions', 'left' => 'sinren_users.division_id', 'right' => 'sinren_divisions.division_id',],
            ],
            'display'       => [
                'title' => 'ユーザーマスタ',
                'route' => $this->route . '/RosterUser',
                'h2'    => '勤怠管理システム ユーザーマスタ',
            ],
            'table_columns' => [
                ['row' => [['user_id', 'ユーザーID',]]],
                ['row' => [['division_name', '部署名',]]],
                ['row' => [['last_name', '姓'], ['first_name', '名']]],
                ['row' => [['staff_number', '職員番号',]]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'sinren_users.division_id' => 'asc',
                'roster_users.user_id'     => 'asc',
            ],
            'table_search'  => [
                'division_name' => ['column_name' => 'sinren_divisions.division_name', 'display' => '部署名', 'type' => 'string'],
                'last_name'     => ['column_name' => 'users.last_name', 'display' => '姓', 'type' => 'string'],
                'first_name'    => ['column_name' => 'users.first_name', 'display' => '名', 'type' => 'string'],
                'staff_number'  => ['column_name' => 'staff_number', 'display' => '職員番号', 'type' => 'integer'],
            ],
            'csv'           => [
                'columns'       => [
                    'roster_users.user_id',
                    'sinren_divisions.division_name',
                    'users.last_name',
                    'users.first_name',
                    'roster_users.staff_number',
                ],
                'kanji_columns' => [
                    'ユーザーID',
                    '部署名',
                    '姓',
                    '名',
                    '職員番号',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* POST_flag, kanji_name, [format], [class] */
                    [1, 'user_id', 'ユーザーID',],
                    [0, 'division_name', '部署名',],
                    [0, 'last_name', '姓',],
                    [0, 'first_name', '名',],
                    [1, 'staff_number', '職員番号', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'user_id'      => 'required|exists:mysql_roster.roster_users,user_id',
                    'staff_number' => 'required|integer',
                ],
                'types'         => [
                    'user_id'      => 'integer',
                    'staff_number' => 'integer',
                ],
                'flags'         => [
                    'user_id'      => 1,
                    'staff_number' => 1,
                ],
                'keys'          => ['user_id'],
            ],
        ];
        $this->param = $params;
    }

}
