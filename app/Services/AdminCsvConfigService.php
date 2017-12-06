<?php

namespace App\Services;

//use App\Http\Requests;

class AdminCsvConfigService
{

    protected $param;

    public function getter($category) {
//        $function = 'get' . \Input::get('category');
        $function = 'get' . $category;
        $this->$function();

        return $this->param;
    }

    public function getZenonCsv() {
        $params      = [
            'object'        => '\App\ZenonCsv',
            'display'       => [
                'title' => '全オン還元CSVファイル設定',
                'route' => '/admin/super_user/config/Admin/ZenonCsv',
                'h2'    => '全オン還元CSVファイル設定',
            ],
            'table_columns' => [
                ['row' => [['is_process', '処理']]],
                ['row' => [['zenon_format_id', 'フォーマットID']]],
                [
                    'row' => [
                        ['zenon_data_name', '全オンデータ名', 'class' => 'small text-left'],
                        ['identifier', '識別子', 'class' => 'small text-left'],
                    ]
                ],
                [
                    'row' => [
                        ['reference_return_date', '目安還元日', 'class' => 'text-left small']
                    ]
                ],
                [
                    'row' => [
                        ['cycle', 'サイクル'],
//                        ['is_monthly', '月次'],
//                        ['is_daily', '日次']
                    ]
                ],
                [
                    'row' => [
                        ['first_column_position', '開始'],
                        ['last_column_position', '終了'],
                        ['column_length', 'カラム長']
                    ]
                ],
                [
                    'row' => [
                        ['database_name', 'DB名', 'class' => 'text-left small'],
                        ['table_name', 'テーブル名', 'class' => 'text-left small'],
                    ]
                ],
                [
                    'row' => [
                        ['is_cumulative', '累積', 'class' => 'small']
                    ]
                ],
                [
                    'row' => [
                        ['is_account_convert', '変換', 'class' => 'small text-left'],
                        ['subject_column_name', '変換科目名', 'class' => 'small text-left'],
                        ['account_column_name', '変換口座名', 'class' => 'small text-left'],
                    ]
                ],
                [
                    'row' => [
                        ['is_split', '分割', 'class' => 'small text-left'],
                        ['split_foreign_key_1', '分割時キー1', 'class' => 'small text-left'],
                        ['split_foreign_key_2', '分割時キー2', 'class' => 'small text-left'],
                        ['split_foreign_key_3', '分割時キー3', 'class' => 'small text-left'],
                        ['split_foreign_key_4', '分割時キー4', 'class' => 'small text-left'],
                    ]
                ],
            ],
            'table_orders'  => [
                'is_process'      => 'desc',
                'zenon_format_id' => 'asc',
            ],
            'table_search'  => [
                'zenon_format_id'       => ['column_name' => 'zenon_format_id', 'display' => 'フォーマットID', 'type' => 'integer'],
                'zenon_data_name'       => ['column_name' => 'zenon_data_name', 'display' => '全オンデータ名', 'type' => 'string'],
                'identifier'            => ['column_name' => 'identifier', 'display' => '識別子', 'type' => 'string'],
                'reference_return_date' => ['column_name' => 'reference_return_date', 'display' => '目安還元日', 'type' => 'string'],
                'cycle'                 => ['column_name' => 'cycle', 'display' => 'サイクル', 'type' => 'string'],
                'table_name'            => ['column_name' => 'table_name', 'display' => 'テーブル名', 'type' => 'string'],
                'is_cumulative'         => ['column_name' => 'is_cumulative', 'display' => '累積', 'type' => 'integer'],
                'is_account_convert'    => ['column_name' => 'is_account_convert', 'display' => '変換', 'type' => 'integer'],
                'is_process'            => ['column_name' => 'is_process', 'display' => '分割', 'type' => 'integer'],
            ],
            'csv'           => [
                'columns'       => [
                    'id',
                    'identifier',
                    'zenon_data_type_id',
                    'zenon_data_name',
                    'first_column_position',
                    'last_column_position',
                    'column_length',
                    'reference_return_date',
                    'cycle',
                    'database_name',
                    'table_name',
                    'is_cumulative',
                    'is_account_convert',
                    'is_process',
                    'is_split',
                    'zenon_format_id',
                    'account_column_name',
                    'subject_column_name',
                    'split_foreign_key_1',
                    'split_foreign_key_2',
                    'split_foreign_key_3',
                    'split_foreign_key_4',
                ],
                'kanji_columns' => [
                    'No',
                    '識別子',
                    '全オンデータ種類',
                    '全オンデータ名',
                    '開始カラム位置',
                    '終了カラム位置',
                    'カラム長',
                    '目安還元日',
                    'サイクル',
                    'データベース名',
                    'テーブル名',
                    '累積フラグ',
                    '口座変換フラグ',
                    '処理フラグ',
                    'テーブル分割フラグ',
                    '全オンフォーマットID',
                    '変換口座カラム名',
                    '変換科目カラム名',
                    '分割時キー1',
                    '分割時キー2',
                    '分割時キー3',
                    '分割時キー4',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* POST_flag, kanji_name, [format], [class] */
                    [1, 'id', 'No',],
                    [1, 'identifier', '識別子',],
                    [1, 'zenon_data_type_id', '全オンデータ種類',],
                    [1, 'zenon_data_name', '全オンデータ名',],
                    [1, 'first_column_position', '開始カラム位置',],
                    [1, 'last_column_position', '終了カラム位置',],
                    [1, 'column_length', 'カラム長',],
                    [1, 'reference_return_date', '目安還元日',],
                    [1, 'cycle', 'サイクル',],
                    [1, 'database_name', 'データベース名',],
                    [1, 'table_name', 'テーブル名',],
                    [1, 'is_cumulative', '累積フラグ',],
                    [1, 'is_account_convert', '口座変換フラグ',],
                    [1, 'is_process', '処理フラグ',],
                    [1, 'is_split', 'テーブル分割フラグ',],
                    [1, 'zenon_format_id', '全オンフォーマットID',],
                    [1, 'account_column_name', '変換口座カラム名',],
                    [1, 'subject_column_name', '変換科目カラム名',],
                    [1, 'split_foreign_key_1', '分割時キー1',],
                    [1, 'split_foreign_key_2', '分割時キー2',],
                    [1, 'split_foreign_key_3', '分割時キー3',],
                    [1, 'split_foreign_key_4', '分割時キー4',],
                ],
                'rules'         => [
                    'id'                    => 'required|integer',
                    'identifier'            => 'required|min:4',
                    'zenon_data_type_id'    => 'required|integer',
                    'first_column_position' => 'required|integer',
                    'last_column_position'  => 'required|integer|min:1',
                    'column_length'         => 'required|integer|min:1',
                    'cycle'                 => 'required|min:1|max:1',
                    'database_name'         => 'required|min:1',
//                    'table_name'            => 'required|min:1',
                    'is_cumulative'         => 'required|boolean',
                    'is_account_convert'    => 'required|boolean',
                    'is_process'            => 'required|boolean',
                    'is_split'              => 'required|boolean',
                    'zenon_format_id'       => 'required|integer',
                ],
                'types'         => [
                    'id'                    => 'integer',
                    'zenon_data_type_id'    => 'integer',
                    'first_column_position' => 'integer',
                    'last_column_position'  => 'integer',
                    'column_length'         => 'integer',
                    'is_cumulative'         => 'integer',
                    'is_account_convert'    => 'integer',
                    'is_process'            => 'integer',
                    'is_split'              => 'integer',
                    'zenon_format_id'       => 'integer',
                ],
                'flags'         => [
                    'id'                    => 1,
                    'identifier'            => 1,
                    'zenon_data_type_id'    => 1,
                    'zenon_data_name'       => 1,
                    'first_column_position' => 1,
                    'last_column_position'  => 1,
                    'column_length'         => 1,
                    'reference_return_date' => 1,
                    'cycle'                 => 1,
                    'database_name'         => 1,
                    'table_name'            => 1,
                    'is_cumulative'         => 1,
                    'is_account_convert'    => 1,
                    'is_process'            => 1,
                    'is_split'              => 1,
                    'zenon_format_id'       => 1,
                    'account_column_name'   => 1,
                    'subject_column_name'   => 1,
                    'split_foreign_key_1'   => 1,
                    'split_foreign_key_2'   => 1,
                    'split_foreign_key_3'   => 1,
                    'split_foreign_key_4'   => 1,
                ],
                'keys'          => ['id'],
            ],
        ];
        $this->param = $params;
    }

    public function getZenonTable() {
        $params      = [
            'object'        => '\App\ZenonTable',
            'display'       => [
                'title' => 'MySQL側 全オンテーブル設定',
                'route' => '/admin/super_user/config/Admin/ZenonTable',
                'h2'    => 'MySQL側 全オンテーブル設定',
            ],
            'join'          => [
                ['db' => 'suisin_db.zenon_data_csv_files', 'left' => 'zenon_table_column_configs.zenon_format_id', 'right' => 'zenon_data_csv_files.zenon_format_id',],
            ],
            'as'            => [
                'table'   => 'zenon_table_column_configs',
                'columns' => [
                    'zenon_format_id',
                ],
            ],
            'table_columns' => [
//                ['row' => [['zenon_format_id', 'ID',],]],
                [
                    'row' => [
                        ['zenon_data_name', 'データ名',],
                        ['identifier', '識別子',],
                    ]
                ],
                ['row' =>
                    [
                        ['column_name', 'カラム名', 'class' => 'text-left',],
                        ['japanese_column_name', '日本語カラム名', 'class' => 'text-left',],
                    ]
                ],
                ['row' => [['column_type', 'カラム型',],]],
                [
                    'row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'zenon_table_column_configs.zenon_format_id' => 'asc',
            ],
            'table_search'  => [
                'identifier'           => ['column_name' => 'identifier', 'display' => '識別子', 'type' => 'string'],
                'zenon_data_name'      => ['column_name' => 'zenon_data_name', 'display' => 'データ名', 'type' => 'string'],
                'column_name'          => ['column_name' => 'column_name', 'display' => 'カラム名', 'type' => 'string'],
                'japanese_column_name' => ['column_name' => 'japanese_column_name', 'display' => '日本語カラム名', 'type' => 'string'],
                'column_type'          => ['column_name' => 'column_type', 'display' => 'カラム型', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
//                    'id',
                    'zenon_format_id',
                    'column_name',
                    'japanese_column_name',
                    'column_type',
                ],
                'kanji_columns' => [
//                    'No',
                    'フォーマットID',
                    'カラム名',
                    '日本語カラム名',
                    'データ型',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* POST_flag, kanji_name, [format], [class] */
//                    [1, 'id', 'No',],
                    [1, 'zenon_format_id', 'フォーマットID',],
                    [1, 'column_name', 'カラム名', 'class' => 'text-left',],
                    [1, 'japanese_column_name', '日本語カラム名', 'class' => 'text-left',],
                    [1, 'column_type', 'データ型', 'class' => 'text-left',],
                ],
                'rules'         => [
//                    'id'              => 'required|integer',
                    'zenon_format_id' => 'required|exists:mysql_suisin.zenon_data_csv_files,zenon_format_id',
                    'column_name'     => 'required|min:1',
                    'column_type'     => 'required|min:1',
                ],
                'types'         => [
//                    'id'              => 'integer',
                    'zenon_format_id' => 'integer',
                ],
                'flags'         => [
                    'id'                   => 1,
                    'zenon_format_id'      => 1,
                    'column_name'          => 1,
                    'japanese_column_name' => 1,
                    'column_type'          => 1,
                ],
                'keys'          => ['zenon_format_id', 'column_name'],
            ],
        ];
        $this->param = $params;
    }

    public function getZenonType() {
        $params      = [
            'object'        => '\App\ZenonType',
            'display'       => [
                'title' => '全オン還元データ種類',
                'route' => '/admin/super_user/config/Admin/ZenonType',
                'h2'    => '全オン還元データ種類',
            ],
            'table_columns' => [
                ['row' => [['data_type_name', 'カテゴリ名', 'class' => 'text-left']]],
                [
                    'row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'id' => 'asc',
            ],
            'table_search'  => [
                'data_type_name' => ['column_name' => 'data_type_name', 'display' => 'カテゴリ名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'id',
                    'data_type_name',
                ],
                'kanji_columns' => [
                    'No',
                    'カテゴリ名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* POST_flag, kanji_name, [format], [class] */
                    [1, 'id', 'No',],
                    [1, 'data_type_name', 'データ種類名', 'class' => 'text-left',],
                ],
                'rules'         => [
                    'id'             => 'required|integer',
                    'data_type_name' => 'required|min:1',
                ],
                'types'         => [
                    'id' => 'integer',
                ],
                'flags'         => [
                    'id'             => 1,
                    'data_type_name' => 1,
                ],
                'keys'          => ['id'],
            ],
        ];
        $this->param = $params;
    }

}
