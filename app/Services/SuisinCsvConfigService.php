<?php

namespace App\Services;

//use App\Http\Requests;

class SuisinCsvConfigService
{

    protected $param;
    protected $route = '/admin/suisin/config/Suisin';

    public function getter($category) {
        $function = 'get' . \Input::get('category');
        $function = 'get' . $category;
        $this->$function();

        return $this->param;
    }

    public function getDepositGist() {
        $params      = [
            'object'        => '\App\Models\Deposit\Gist',
            'display'       => [
                'title' => '摘要コード',
                'route' => $this->route . '/DepositGist',
                'h2'    => '全オン・ビジネスネット摘要リスト',
            ],
            'join'          => [],
            'table_columns' => [
                ['row' => [['gist_code', '摘要コード', 'format' => '%03d']]],
                ['row' => [['display_gist', '表示摘要', 'class' => 'text-left']]],
                ['row' => [['zenon_gist', '全オン摘要', 'class' => 'text-left']]],
                ['row' => [['keizai_gist_kanji', 'ビジネスネット 漢字摘要', 'class' => 'text-left']]],
                ['row' => [['keizai_gist_full_kana', 'ビジネスネット カナ摘要', 'class' => 'text-left']]],
                ['row' => [['keizai_gist_half_kana', 'ビジネスネット ｶﾅ摘要', 'class' => 'text-left']]],
                ['row' => [['is_keizai', 'ビジネスネット 経済フラグ',]]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'gist_code' => 'asc',
            ],
            'table_search'  => [
                /**
                 * key         = インプットフォーム名
                 * column_name = クエリ検索時のカラム名
                 * display     = ラベル名
                 * type        = 型（string型のみlike検索）
                 */
                'gist_code'             => ['column_name' => 'gist_code', 'display' => '摘要コード', 'type' => 'integer'],
                'display_gist'          => ['column_name' => 'display_gist', 'display' => '表示摘要', 'type' => 'string'],
                'keizai_gist_kanji'     => ['column_name' => 'keizai_gist_kanji', 'display' => 'ビジネスネット 漢字摘要', 'type' => 'string'],
                'keizai_gist_full_kana' => ['column_name' => 'keizai_gist_full_kana', 'display' => 'ビジネスネット カナ摘要', 'type' => 'string'],
                'is_keizai'             => ['column_name' => 'is_keizai', 'display' => 'ビジネスネット 経済フラグ', 'type' => 'boolean'],
            ],
            'csv'           => [
                'columns'       => [
                    'gist_code',
                    'display_gist',
                    'zenon_gist',
                    'keizai_gist_kanji',
                    'keizai_gist_half_kana',
                    'keizai_gist_full_kana',
                    'is_keizai',
                ],
                'kanji_columns' => [
                    '摘要コード',
                    '表示摘要名',
                    '全オン摘要',
                    'ビジネスネット 漢字摘要',
                    'ビジネスネット カナ摘要',
                    'ビジネスネット ｶﾅ摘要',
                    'ビジネスネット 経済フラグ',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'gist_code', '摘要コード', 'format' => '%03d'],
                    [1, 'display_gist', '表示摘要', 'class' => 'text-left'],
                    [1, 'zenon_gist', '全オン摘要', 'class' => 'text-left'],
                    [1, 'keizai_gist_kanji', '漢字摘要', 'class' => 'text-left'],
                    [1, 'keizai_gist_full_kana', 'カナ摘要', 'class' => 'text-left'],
                    [1, 'keizai_gist_half_kana', 'ｶﾅ摘要', 'class' => 'text-left'],
                    [1, 'is_keizai', '経済フラグ',],
                ],
                'rules'         => [
                    'gist_code'    => 'required|integer',
                    'display_gist' => 'required|min:1',
                    'is_keizai'    => 'required|boolean',
                ],
                'types'         => [
                    'gist_code' => 'integer',
                    'is_keizai' => 'boolean',
                ],
                'flags'         => [
                    'display_gist'          => 1,
                    'zenon_gist'            => 1,
                    'keizai_gist_kanji'     => 1,
                    'keizai_gist_half_kana' => 1,
                    'keizai_gist_full_kana' => 1,
                    'is_keizai'             => 1,
                ],
                'keys'          => ['gist_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getDepositCategory() {
        $params      = [
            'object'        => '\App\Models\Deposit\Category',
            'join'          => [
                ['db' => 'master_db.subject_codes', 'left' => 'deposit_category_codes.subject_code', 'right' => 'subject_codes.subject_code',],
            ],
            'as'            => [
                'table'   => 'deposit_category_codes',
                'columns' => [
                    'subject_code',
                ],
            ],
            'display'       => [
                'title' => '貯金種類コード',
                'route' => $this->route . '/DepositCategory',
                'h2'    => '貯金種類コード',
            ],
            'table_columns' => [
                ['row' => [['subject_code', '科目コード', 'format' => '%02d']]],
                ['row' => [['subject_name', '科目名', 'class' => 'text-left']]],
                ['row' => [['category_code', '貯金種類コード', 'format' => '%03d']]],
                ['row' => [['category_name', '貯金種類名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'deposit_category_codes.subject_code'  => 'asc',
                'deposit_category_codes.category_code' => 'asc',
            ],
            'table_search'  => [
                'subject_code'  => ['column_name' => 'deposit_category_codes.subject_code', 'display' => '科目コード', 'type' => 'integer'],
                'subject_name'  => ['column_name' => 'subject_codes.subject_name', 'display' => '科目名', 'type' => 'string'],
                'category_code' => ['column_name' => 'deposit_category_codes.category_code', 'display' => '種類コード', 'type' => 'integer'],
                'category_name' => ['column_name' => 'deposit_category_codes.category_name', 'display' => '種類名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'deposit_category_codes.subject_code',
                    'subject_codes.subject_name',
                    'deposit_category_codes.category_code',
                    'deposit_category_codes.category_name',
                ],
                'kanji_columns' => [
                    '科目コード',
                    '科目名',
                    '貯金種類コード',
                    '貯金種類名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'subject_code', '科目コード', 'format' => '%02d'],
                    [0, 'subject_name', '科目名', 'class' => 'text-left'],
                    [1, 'category_code', '貯金種類コード', 'format' => '%03d'],
                    [1, 'category_name', '貯金種類名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'subject_code'  => 'required|exists:mysql_master.subject_codes,subject_code',
                    'category_code' => 'required|min:1',
                    'category_name' => 'required|min:1',
                ],
                'types'         => [
                    'subject_code'  => 'integer',
                    'category_code' => 'integer',
                ],
                'flags'         => [
                    'subject_code'  => 1,
                    'category_code' => 1,
                    'category_name' => 1,
                ],
                'keys'          => ['subject_code', 'category_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getDepositBankbookType() {
        $params      = [
            'object'        => '\App\Models\Deposit\BankbookType',
            'join'          => [],
            'display'       => [
                'title' => '通証タイプ',
                'route' => $this->route . '/DepositBankbookType',
                'h2'    => '通証タイプ',
            ],
            'table_columns' => [
                ['row' => [['bankbook_deed_type', '通証タイプ', 'format' => '%02d']]],
                ['row' => [['bankbook_deed_name', '通証名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'bankbook_deed_type' => 'asc',
            ],
            'table_search'  => [
                'bankbook_deed_type' => ['column_name' => 'bankbook_deed_type', 'display' => '通証タイプ', 'type' => 'integer'],
                'bankbook_deed_name' => ['column_name' => 'bankbook_deed_name', 'display' => '通証名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'bankbook_deed_type',
                    'bankbook_deed_name',
                ],
                'kanji_columns' => [
                    '通証タイプ',
                    '通証名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'bankbook_deed_type', '通証タイプ', 'format' => '%02d'],
                    [1, 'bankbook_deed_name', '通証名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'bankbook_deed_type' => 'required|integer',
                    'bankbook_deed_name' => 'required|min:1',
                ],
                'types'         => [
                    'bankbook_deed_type' => 'integer',
                ],
                'flags'         => [
                    'bankbook_deed_type' => 1,
                    'bankbook_deed_name' => 1,
                ],
                'keys'          => ['bankbook_deed_type',],
            ],
        ];
        $this->param = $params;
    }

    public function getSubject() {
        $params      = [
            'object'        => '\App\Models\Common\Subject',
            'join'          => [],
            'display'       => [
                'title' => '科目コード',
                'route' => $this->route . '/Subject',
                'h2'    => '科目コード',
            ],
            'table_columns' => [
                ['row' => [['subject_code', '科目コード', 'format' => '%02d']]],
                ['row' => [['subject_name', '科目名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'subject_code' => 'asc',
            ],
            'table_search'  => [
                'subject_code' => ['column_name' => 'subject_code', 'display' => '科目コード', 'type' => 'integer'],
                'subject_name' => ['column_name' => 'subject_name', 'display' => '科目名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'subject_code',
                    'subject_name',
                ],
                'kanji_columns' => [
                    '科目コード',
                    '科目名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'subject_code', '科目コード', 'format' => '%02d'],
                    [1, 'subject_name', '科目名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'subject_code' => 'required|integer',
                    'subject_name' => 'required|min:1',
                ],
                'types'         => [
                    'subject_code' => 'integer',
                ],
                'flags'         => [
                    'subject_code' => 1,
                    'subject_name' => 1,
                ],
                'keys'          => ['subject_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getIndustry() {
        $params      = [
            'object'        => '\App\Models\Common\Industry',
            'join'          => [],
            'display'       => [
                'title' => '業種コード',
                'route' => $this->route . '/Industry',
                'h2'    => '業種コード',
            ],
            'table_columns' => [
                ['row' => [['industry_code', '業種コード', 'format' => '%03d']]],
                ['row' => [['industry_name', '業種名', 'class' => 'text-left']]],
                ['row' => [['industry_content', '業種内容', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'industry_code' => 'asc',
            ],
            'table_search'  => [
                'industry_code'    => ['column_name' => 'industry_code', 'display' => '業種コード', 'type' => 'integer'],
                'industry_name'    => ['column_name' => 'industry_name', 'display' => '業種名', 'type' => 'string'],
                'industry_content' => ['column_name' => 'industry_content', 'display' => '業種内容', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'industry_code',
                    'industry_name',
                    'industry_content',
                ],
                'kanji_columns' => [
                    '業種コード',
                    '業種名',
                    '業種内容',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'industry_code', '業種コード', 'format' => '%03d'],
                    [1, 'industry_name', '業種名', 'class' => 'text-left'],
                    [1, 'industry_content', '業種内容', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'industry_code' => 'required|integer',
                    'industry_name' => 'required|min:1',
                ],
                'types'         => [
                    'industry_code' => 'integer',
                ],
                'flags'         => [
                    'industry_code'    => 1,
                    'industry_name'    => 1,
                    'industry_content' => 1,
                ],
                'keys'          => ['industry_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getQualification() {
        $params      = [
            'object'        => '\App\Models\Common\Qualification',
            'join'          => [],
            'display'       => [
                'title' => '資格区分',
                'route' => $this->route . '/Qualification',
                'h2'    => '資格区分',
            ],
            'table_columns' => [
                ['row' => [['qualification_code', '資格区分', 'format' => '%03d']]],
                ['row' => [['qualification_type', '資格種類', 'class' => 'text-left']]],
                ['row' => [['qualification_name', '資格名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'qualification_code' => 'asc',
            ],
            'table_search'  => [
                'qualification_code' => ['column_name' => 'qualification_code', 'display' => '資格区分', 'type' => 'integer'],
                'qualification_type' => ['column_name' => 'qualification_type', 'display' => '資格種類', 'type' => 'string'],
                'qualification_name' => ['column_name' => 'qualification_name', 'display' => '資格名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'qualification_code',
                    'qualification_type',
                    'qualification_name',
                ],
                'kanji_columns' => [
                    '資格区分',
                    '資格種類',
                    '資格名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'qualification_code', '資格区分', 'format' => '%03d'],
                    [1, 'qualification_type', '資格種類', 'class' => 'text-left'],
                    [1, 'qualification_name', '資格名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'qualification_code' => 'required|integer',
                    'qualification_type' => 'required|min:1',
                    'qualification_name' => 'required|min:1',
                ],
                'types'         => [
                    'qualification_code' => 'integer',
                ],
                'flags'         => [
                    'qualification_code' => 1,
                    'qualification_type' => 1,
                    'qualification_name' => 1,
                ],
                'keys'          => ['qualification_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getPersonality() {
        $params      = [
            'object'        => '\App\Models\Common\Personality',
            'join'          => [],
            'display'       => [
                'title' => '人格コード',
                'route' => $this->route . '/Personality',
                'h2'    => '人格コード',
            ],
            'table_columns' => [
                ['row' => [['personality_code', '人格コード', 'format' => '%03d']]],
                ['row' => [['personality_name', '人格名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'personality_code' => 'asc',
            ],
            'table_search'  => [
                'personality_code' => ['column_name' => 'personality_code', 'display' => '人格コード', 'type' => 'integer'],
                'personality_name' => ['column_name' => 'personality_name', 'display' => '人格名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'personality_code',
                    'personality_name',
                ],
                'kanji_columns' => [
                    '人格コード',
                    '人格名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'personality_code', '人格コード', 'format' => '%03d'],
                    [1, 'personality_name', '人格名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'personality_code' => 'required|integer',
                    'personality_name' => 'required|min:1',
                ],
                'types'         => [
                    'personality_code' => 'integer',
                ],
                'flags'         => [
                    'personality_code' => 1,
                    'personality_name' => 1,
                ],
                'keys'          => ['personality_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getPrefecture() {
        $params      = [
            'object'        => '\App\Models\Common\Prefecture',
            'join'          => [],
            'display'       => [
                'title' => '県コード',
                'route' => $this->route . '/Prefecture',
                'h2'    => '県コード',
            ],
            'table_columns' => [
                ['row' => [['prefecture_code', '県コード', 'format' => '%04d']]],
                ['row' => [['prefecture_name', '県名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'prefecture_code' => 'asc',
            ],
            'table_search'  => [
                'prefecture_code' => ['column_name' => 'prefecture_code', 'display' => '県コード', 'type' => 'integer'],
                'prefecture_name' => ['column_name' => 'prefecture_name', 'display' => '県名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'prefecture_code',
                    'prefecture_name',
                ],
                'kanji_columns' => [
                    '県コード',
                    '県名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'prefecture_code', '県コード', 'format' => '%04d'],
                    [1, 'prefecture_name', '県名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'prefecture_code' => 'required|integer',
                    'prefecture_name' => 'required',
                ],
                'types'         => [
                    'prefecture_code' => 'integer',
                ],
                'flags'         => [
                    'prefecture_code' => 1,
                    'prefecture_name' => 1,
                ],
                'keys'          => ['prefecture_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getStore() {
        $params      = [
            'object'        => '\App\Models\Common\Store',
            'join'          => [
                ['db' => 'master_db.prefecture_codes', 'left' => 'stores.prefecture_code', 'right' => 'prefecture_codes.prefecture_code',],
            ],
            'as'            => [
                'table'   => 'stores',
                'columns' => [
                    'prefecture_code',
                ],
            ],
            'display'       => [
                'title' => '店番',
                'route' => $this->route . '/Store',
                'h2'    => '店番',
            ],
            'table_columns' => [
                ['row' => [['prefecture_code', '県コード', 'format' => '%04d']]],
                ['row' => [['store_number', '店番', 'class' => 'text-left', 'format' => '%03d']]],
                ['row' => [['prefecture_name', '県名', 'class' => 'text-left']]],
                ['row' => [['store_name', '店舗名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'prefecture_codes.prefecture_code' => 'asc',
                'stores.store_number'              => 'asc',
            ],
            'table_search'  => [
                'prefecture_code' => ['column_name' => 'stores.prefecture_code', 'display' => '県コード', 'type' => 'integer'],
                'prefecture_name' => ['column_name' => 'prefecture_codes.prefecture_name', 'display' => '県名', 'type' => 'string'],
                'store_number'    => ['column_name' => 'store_number', 'display' => '店舗コード', 'type' => 'integer'],
                'store_name'      => ['column_name' => 'store_name', 'display' => '店舗名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'stores.prefecture_code',
                    'stores.store_number',
                    'prefecture_codes.prefecture_name',
                    'stores.store_name',
                ],
                'kanji_columns' => [
                    '県コード',
                    '店番',
                    '県名',
                    '店舗名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'prefecture_code', '県コード', 'format' => '%04d'],
                    [1, 'store_number', '店番', 'format' => '%03d'],
                    [0, 'prefecture_name', '県名', 'class' => 'text-left'],
                    [1, 'store_name', '店舗名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'prefecture_code' => 'required|integer|exists:mysql_master.prefecture_codes,prefecture_code',
                    'store_number'    => 'required|integer',
                    'store_name'      => 'required|min:1',
                ],
                'types'         => [
                    'prefecture_code' => 'required|exists:mysql_master.prefecture_codes,prefecture_code',
                    'store_number'    => 'required|integer',
                ],
                'flags'         => [
                    'prefecture_code' => 1,
                    'store_number'    => 1,
                    'store_name'      => 1,
                ],
                'keys'          => ['prefecture_code', 'store_number'],
            ],
        ];
        $this->param = $params;
    }

    public function getSmallStore() {
        $params      = [
            'object'        => '\App\Models\Common\SmallStore',
            'join'          => [
                ['db' => 'master_db.prefecture_codes', 'left' => 'small_stores.prefecture_code', 'right' => 'prefecture_codes.prefecture_code',],
                ['db' => 'master_db.stores', 'left' => 'small_stores.store_number', 'right' => 'stores.store_number',],
                ['db' => 'master_db.control_stores', 'left' => 'small_stores.control_store_code', 'right' => 'control_stores.control_store_code',],
            ],
            'as'            => [
                'table'   => 'small_stores',
                'columns' => [
                    'prefecture_code',
                    'store_number',
                    'control_store_code',
                ],
            ],
            'display'       => [
                'title' => '小規模店番',
                'route' => $this->route . '/SmallStore',
                'h2'    => '小規模店番',
            ],
            'table_columns' => [
                ['row' =>
                    [
                        ['prefecture_code', '県コード', 'format' => '%04d'],
                        ['prefecture_name', '県名',],
                    ]
                ],
                ['row' =>
                    [
                        ['store_number', '店番', 'format' => '%03d'],
                        ['store_name', '店名',],
                    ]
                ],
                ['row' =>
                    [
                        ['control_store_code', '管轄店舗'],
                        ['control_store_name', '管轄店舗名'],
                    ]
                ],
                ['row' =>
                    [
                        ['small_store_number', '小規模店番', 'format' => '%03d'],
                        ['small_store_name', '小規模店名'],
                    ]
                ],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'small_stores.prefecture_code'    => 'asc',
                'small_stores.store_number'       => 'asc',
                'small_stores.control_store_code' => 'asc',
                'small_stores.small_store_number' => 'asc',
            ],
            'table_search'  => [
                'store_number'       => ['column_name' => 'small_stores.store_number', 'display' => '店舗コード', 'type' => 'integer'],
                'store_name'         => ['column_name' => 'stores.store_name', 'display' => '店舗名', 'type' => 'string'],
                'small_store_number' => ['column_name' => 'small_store_number', 'display' => '小規模店番', 'type' => 'integer'],
                'small_store_name'   => ['column_name' => 'small_store_name', 'display' => '小規模店名', 'type' => 'string'],
                'control_store_code' => ['column_name' => 'small_stores.control_store_code', 'display' => '管轄店舗コード', 'type' => 'integer'],
                'control_store_name' => ['column_name' => 'control_stores.control_store_name', 'display' => '管轄店舗名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'small_stores.prefecture_code',
                    'small_stores.store_number',
                    'small_stores.control_store_code',
                    'small_stores.small_store_number',
                    'prefecture_codes.prefecture_name', // ignore
                    'stores.store_name', // ignore
                    'control_stores.control_store_name', // ignore
                    'small_stores.small_store_name',
                ],
                'kanji_columns' => [
                    '県コード',
                    '店番',
                    '管轄店舗',
                    '小規模店番',
                    '県名',
                    '店名',
                    '管轄店舗名',
                    '小規模店名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'prefecture_code', '県コード', 'format' => '%04d'],
                    [1, 'store_number', '店番', 'format' => '%03d'],
                    [1, 'control_store_code', '管轄店舗'],
                    [1, 'small_store_number', '小規模店番', 'format' => '%03d'],
                    [0, 'prefecture_name', '県名', 'class' => 'text-left'],
                    [0, 'store_name', '店名', 'class' => 'text-left'],
                    [0, 'control_store_name', '管轄店名', 'class' => 'text-left'],
                    [1, 'small_store_name', '小規模店名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'prefecture_code'    => 'required|integer|exists:mysql_master.prefecture_codes,prefecture_code',
                    'store_number'       => 'required|integer|exists:mysql_master.stores,store_number',
                    'small_store_number' => 'required|integer',
                    'control_store_code' => 'required|integer',
                    'small_store_name'   => 'required|min:1',
                ],
                'types'         => [
                    'prefecture_code'    => 'integer',
                    'store_number'       => 'integer',
                    'small_store_number' => 'integer',
                    'control_store_code' => 'integer',
                ],
                'flags'         => [
                    'prefecture_code'    => 1,
                    'store_number'       => 1,
                    'small_store_number' => 1,
                    'control_store_code' => 1,
                    'small_store_name'   => 1,
                ],
                'keys'          => ['prefecture_code', 'store_number', 'small_store_number'],
            ],
        ];
        $this->param = $params;
    }

    public function getArea() {
        $params      = [
            'object'        => '\App\Models\Common\Area',
            'join'          => [
                ['db' => 'master_db.prefecture_codes', 'left' => 'area_codes.prefecture_code', 'right' => 'prefecture_codes.prefecture_code',],
                ['db' => 'master_db.stores', 'left' => 'area_codes.store_number', 'right' => 'stores.store_number',],
                ['db' => 'master_db.small_stores', 'left' => 'area_codes.small_store_number', 'right' => 'small_stores.small_store_number',],
            ],
            'as'            => [
                'table'   => 'area_codes',
                'columns' => [
                    'prefecture_code',
                    'store_number',
                    'small_store_number',
                ],
            ],
            'display'       => [
                'title' => '地区コード',
                'route' => $this->route . '/Area',
                'h2'    => '地区コード',
            ],
            'table_columns' => [
                ['row' =>
                    [
                        ['prefecture_code', '県コード', 'format' => '%04d'],
                        ['prefecture_name', '県名',],
                    ]
                ],
                ['row' =>
                    [
                        ['store_number', '店番', 'format' => '%03d'],
                        ['store_name', '店名',],
                    ]
                ],
                ['row' =>
                    [
                        ['small_store_number', '小規模店番', 'format' => '%03d'],
                        ['small_store_name', '小規模店名',],
                    ]
                ],
                ['row' =>
                    [
                        ['area_code', '地区コード', 'format' => '%03d'],
                        ['area_name', '地区名',],
                    ]
                ],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'area_codes.prefecture_code'    => 'asc',
                'area_codes.store_number'       => 'asc',
                'area_codes.small_store_number' => 'asc',
                'area_codes.area_code'          => 'asc',
            ],
            'table_search'  => [
                'store_number'       => ['column_name' => 'area_codes.store_number', 'display' => '店舗コード', 'type' => 'integer'],
                'store_name'         => ['column_name' => 'stores.store_name', 'display' => '店舗名', 'type' => 'string'],
                'small_store_number' => ['column_name' => 'area_codes.small_store_number', 'display' => '小規模店番', 'type' => 'integer'],
                'small_store_name'   => ['column_name' => 'small_store_name', 'display' => '小規模店名', 'type' => 'string'],
                'area_code'          => ['column_name' => 'area_code', 'display' => '地区コード', 'type' => 'integer'],
                'area_name'          => ['column_name' => 'area_name', 'display' => '地区名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'area_codes.prefecture_code',
                    'area_codes.store_number',
                    'area_codes.small_store_number',
                    'area_codes.area_code',
                    'prefecture_codes.prefecture_name', // ignore
                    'stores.store_name', // ignore
                    'small_stores.small_store_name', // ignore
                    'area_codes.area_name',
                ],
                'kanji_columns' => [
                    '県コード',
                    '店番',
                    '小規模店番',
                    '地区コード',
                    '県名',
                    '店名',
                    '小規模店名',
                    '地区名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'prefecture_code', '県コード', 'format' => '%04d'],
                    [1, 'store_number', '店番', 'format' => '%03d'],
                    [1, 'small_store_number', '小規模店番', 'format' => '%03d'],
                    [0, 'prefecture_name', '県名', 'class' => 'text-left'],
                    [0, 'store_name', '店名', 'class' => 'text-left'],
                    [0, 'small_store_name', '小規模店名', 'class' => 'text-left'],
                    [1, 'area_code', '地区コード', 'format' => '%03d'],
                    [1, 'area_name', '地区名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'prefecture_code'    => 'required|integer|exists:mysql_master.prefecture_codes,prefecture_code',
                    'store_number'       => 'required|integer|exists:mysql_master.stores,store_number',
                    'small_store_number' => 'required|integer|exists:mysql_master.small_stores,small_store_number',
                    'area_code'          => 'required|integer',
                    'area_name'          => 'required|min:1',
                ],
                'types'         => [
                    'prefecture_code'    => 'integer',
                    'store_number'       => 'integer',
                    'small_store_number' => 'integer',
                    'area_code'          => 'integer',
                ],
                'flags'         => [
                    'prefecture_code'    => 1,
                    'store_number'       => 1,
                    'small_store_number' => 1,
                    'area_code'          => 1,
                    'area_name'          => 1,
                ],
                'keys'          => ['prefecture_code', 'store_number', 'small_store_number', 'area_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getControlStore() {
        $params      = [
            'object'        => '\App\ControlStore',
            'join'          => [
                ['db' => 'master_db.prefecture_codes', 'left' => 'control_stores.prefecture_code', 'right' => 'prefecture_codes.prefecture_code',],
            ],
            'as'            => [
                'table'   => 'control_stores',
                'columns' => [
                    'prefecture_code',
                ],
            ],
            'display'       => [
                'title' => '管轄店舗',
                'route' => $this->route . '/ControlStore',
                'h2'    => '管轄店舗',
            ],
            'table_columns' => [
                ['row' =>
                    [
                        ['prefecture_code', '県コード', 'format' => '%04d'],
                        ['prefecture_name', '県名',],
                    ]
                ],
                ['row' =>
                    [
                        ['control_store_code', '管轄店舗コード', 'format' => '%03d'],
                        ['control_store_name', '管轄店舗名',],
                    ]
                ],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'control_stores.prefecture_code'    => 'asc',
                'control_stores.control_store_code' => 'asc',
            ],
            'table_search'  => [
                'prefecture_code'    => ['column_name' => 'control_stores.prefecture_code', 'display' => '県コード', 'type' => 'integer'],
                'prefecture_name'    => ['column_name' => 'prefecture_codes.prefecture_name', 'display' => '県名', 'type' => 'string'],
                'control_store_code' => ['column_name' => 'control_stores.control_store_code', 'display' => '管轄店舗コード', 'type' => 'integer'],
                'control_store_name' => ['column_name' => 'control_stores.control_store_name', 'display' => '管轄店舗名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'control_stores.prefecture_code',
                    'control_stores.control_store_code',
                    'prefecture_codes.prefecture_name', // ignore
                    'control_stores.control_store_name',
                ],
                'kanji_columns' => [
                    '県コード',
                    '管轄店舗コード',
                    '県名',
                    '管轄店舗名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'prefecture_code', '県コード', 'format' => '%04d'],
                    [1, 'control_store_code', '管轄店舗',],
                    [0, 'prefecture_name', '県名', 'class' => 'text-left'],
                    [1, 'control_store_name', '管轄店舗名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'prefecture_code'    => 'required|integer|exists:mysql_master.prefecture_codes,prefecture_code',
                    'control_store_code' => 'required|integer',
                    'control_store_name' => 'required|min:1',
                ],
                'types'         => [
                    'prefecture_code' => 'integer',
                    'control_store'   => 'integer',
                ],
                'flags'         => [
                    'prefecture_code'    => 1,
                    'control_store_code' => 1,
                    'control_store_name' => 1,
                ],
                'keys'          => ['prefecture_code', 'control_store_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getConsignor() {
        $params      = [
            'object'        => '\App\Consignor',
            'join'          => [
                ['db' => 'suisin_db.consignor_groups', 'left' => 'consignors.consignor_group_id', 'right' => 'consignor_groups.id',],
            ],
            'as'            => [
                'table'   => 'consignors',
                'columns' => [
                    'consignor_group_id',
                ],
            ],
            'display'       => [
                'title' => '委託者リスト',
                'route' => $this->route . '/Consignor',
                'h2'    => '委託者リスト',
            ],
            'table_columns' => [
                ['row' => [['consignor_code', '委託者コード', 'format' => '%05d']]],
                ['row' => [['consignor_name', '委託者名', 'class' => 'text-left']]],
                ['row' => [['display_consignor_name', '表示委託者名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['consignor_group_id', 'グループコード'],
                        ['group_name', 'グループ名'],
                    ]
                ],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'consignors.consignor_code'     => 'asc',
                'consignors.consignor_group_id' => 'asc',
            ],
            'table_search'  => [
                'consignor_code'         => ['column_name' => 'consignors.consignor_code', 'display' => '委託者コード', 'type' => 'integer'],
                'display_consignor_name' => ['column_name' => 'consignors.display_consignor_name', 'display' => '表示委託者名', 'type' => 'string'],
                'consignor_group_id'     => ['column_name' => 'consignors.consignor_group_id', 'display' => '委託者グループコード', 'type' => 'integer'],
                'group_name'             => ['column_name' => 'consignor_groups.group_name', 'display' => '委託者グループ名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'consignors.consignor_code',
                    'consignors.consignor_name',
                    'consignors.display_consignor_name',
                    'consignors.consignor_group_id',
                    'consignor_groups.group_name',
                ],
                'kanji_columns' => [
                    '委託者コード',
                    '委託者名',
                    '委託者表示名',
                    'グループコード',
                    'グループ名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'consignor_code', '委託者コード', 'format' => '%05d'],
                    [0, 'consignor_name', '委託者名', 'class' => 'text-left'],
                    [1, 'display_consignor_name', '表示委託者名', 'class' => "text-left"],
                    [1, 'consignor_group_id', 'グループコード'],
                    [0, 'group_name', 'グループ名'],
                ],
                'rules'         => [
                    'consignor_code'         => 'required|integer|exists:mysql_suisin.consignors,consignor_code',
                    'consignor_group_id'     => 'required|integer|exists:mysql_suisin.consignor_groups,id',
                    'display_consignor_name' => 'required|min:1',
                ],
                'types'         => [
                    'consignor_code'     => 'integer',
                    'consignor_group_id' => 'integer',
                ],
                'flags'         => [
                    'display_consignor_name' => 1,
                    'consignor_group_id'     => 1,
                ],
                'keys'          => ['consignor_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getConsignorGroup() {
        $params      = [
            'object'        => '\App\ConsignorGroup',
            'join'          => [],
            'display'       => [
                'title' => '委託者グループ',
                'route' => $this->route . '/ConsignorGroup',
                'h2'    => '委託者グループ',
            ],
            'table_columns' => [
                ['row' => [['id', 'グループコード',]]],
                ['row' => [['group_name', 'グループ名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_search'  => [
                'id'         => ['column_name' => 'id', 'display' => '委託者グループコード', 'type' => 'integer'],
                'group_name' => ['column_name' => 'group_name', 'display' => '委託者グループ名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'id',
                    'group_name',
                ],
                'kanji_columns' => [
                    'グループコード',
                    'グループ名'
                ],
            ],
            'table_orders'  => [
                'id' => 'asc',
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'id', 'グループコード',],
                    [1, 'group_name', 'グループ名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'id'         => 'required|integer|min:1|max:100',
                    'group_name' => 'required|min:1',
                ],
                'types'         => [
                    'id' => 'integer',
                ],
                'flags'         => [
                    'group_name' => 1,
//                    'modify_user_id' => 1,
                ],
                'keys'          => ['id'],
            ],
        ];
        $this->param = $params;
    }

    public function getDepositTaxation() {
        $params      = [
            'object'        => '\App\Models\Deposit\Taxation',
            'join'          => [],
            'display'       => [
                'title' => '課税区分',
                'route' => $this->route . '/DepositTaxation',
                'h2'    => '課税区分',
            ],
            'table_columns' => [
                ['row' => [['taxation_code', '課税コード',],]],
                ['row' => [['taxation_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'deposit_taxation_codes.taxation_code' => 'asc',
            ],
            'table_search'  => [
                'taxation_code' => ['column_name' => 'taxation_code', 'display' => '課税コード', 'type' => 'integer'],
                'taxation_name' => ['column_name' => 'taxation_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'deposit_taxation_codes.taxation_code',
                    'deposit_taxation_codes.taxation_name',
                ],
                'kanji_columns' => [
                    '課税コード',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'taxation_code', '課税コード',],
                    [1, 'taxation_name', '表示名',],
                ],
                'rules'         => [
                    'taxation_code' => 'required|integer',
                    'taxation_name' => 'required|min:1',
                ],
                'types'         => [
                    'taxation_code' => 'integer',
                ],
                'flags'         => [
                    'taxation_code' => 1,
                    'taxation_name' => 1,
                ],
                'keys'          => ['taxation_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getDepositTerm() {
        $params      = [
            'object'        => '\App\Models\Deposit\Term',
            'join'          => [],
            'display'       => [
                'title' => '期間コード',
                'route' => $this->route . '/DepositTerm',
                'h2'    => '期間コード',
            ],
            'table_columns' => [
                ['row' => [['term_code', '期間コード',],]],
                ['row' => [['term_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'deposit_term_codes.term_code' => 'asc',
            ],
            'table_search'  => [
                'term_code' => ['column_name' => 'term_code', 'display' => '期間コード', 'type' => 'integer'],
                'term_name' => ['column_name' => 'term_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'deposit_term_codes.term_code',
                    'deposit_term_codes.term_name',
                ],
                'kanji_columns' => [
                    '期間コード',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'term_code', '期間コード',],
                    [1, 'term_name', '表示名',],
                ],
                'rules'         => [
                    'term_code' => 'required|integer',
                    'term_name' => 'required|min:1',
                ],
                'types'         => [
                    'term_code' => 'integer',
                ],
                'flags'         => [
                    'term_code' => 1,
                    'term_name' => 1,
                ],
                'keys'          => ['term_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getDepositContinuation() {
        $params      = [
            'object'        => '\App\Models\Deposit\Continuation',
            'join'          => [],
            'display'       => [
                'title' => '継続区分',
                'route' => $this->route . '/DepositContinuation',
                'h2'    => '継続区分',
            ],
            'table_columns' => [
                ['row' => [['continuation_code', '継続区分',],]],
                ['row' => [['continuation_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'deposit_continuation_codes.continuation_code' => 'asc',
            ],
            'table_search'  => [
                'continuation_code' => ['column_name' => 'continuation_code', 'display' => '継続区分', 'type' => 'integer'],
                'continuation_name' => ['column_name' => 'continuation_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'deposit_continuation_codes.continuation_code',
                    'deposit_continuation_codes.continuation_name',
                ],
                'kanji_columns' => [
                    '継続区分',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'continuation_code', '継続区分',],
                    [1, 'continuation_name', '表示名',],
                ],
                'rules'         => [
                    'continuation_code' => 'required|integer',
                    'continuation_name' => 'required|min:1',
                ],
                'types'         => [
                    'continuation_code' => 'integer',
                ],
                'flags'         => [
                    'continuation_code' => 1,
                    'continuation_name' => 1,
                ],
                'keys'          => ['continuation_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanCategory() {
        $params      = [
            'object'        => '\App\Models\Loan\Category',
            'join'          => [],
            'display'       => [
                'title' => '貸付種類',
                'route' => $this->route . '/LoanCategory',
                'h2'    => '貸付種類',
            ],
            'table_columns' => [
                ['row' => [['loan_category_code', '貸付種類',],]],
                ['row' => [['loan_category_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_category_codes.loan_category_code' => 'asc',
            ],
            'table_search'  => [
                'loan_category_code' => ['column_name' => 'loan_category_code', 'display' => '貸付種類', 'type' => 'integer'],
                'loan_category_name' => ['column_name' => 'loan_category_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_category_codes.loan_category_code',
                    'loan_category_codes.loan_category_name',
                ],
                'kanji_columns' => [
                    '貸付種類',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'loan_category_code', '貸付種類',],
                    [1, 'loan_category_name', '表示名',],
                ],
                'rules'         => [
                    'loan_category_code' => 'required|integer',
                    'loan_category_name' => 'required|min:1',
                ],
                'types'         => [
                    'loan_category_code' => 'integer',
                ],
                'flags'         => [
                    'loan_category_code' => 1,
                    'loan_category_name' => 1,
                ],
                'keys'          => ['loan_category_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanCollateral() {
        $params      = [
            'object'        => '\App\Models\Loan\Collateral',
            'join'          => [],
            'display'       => [
                'title' => '担保コード',
                'route' => $this->route . '/LoanCollateral',
                'h2'    => '担保コード',
            ],
            'table_columns' => [
                ['row' => [['collateral_code', '担保コード',],]],
                ['row' => [['collateral_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_collateral_codes.collateral_code' => 'asc',
            ],
            'table_search'  => [
                'collateral_code' => ['column_name' => 'collateral_code', 'display' => '担保コード', 'type' => 'integer'],
                'collateral_name' => ['column_name' => 'collateral_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_collateral_codes.collateral_code',
                    'loan_collateral_codes.collateral_name',
                ],
                'kanji_columns' => [
                    '担保コード',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'collateral_code', '担保コード',],
                    [1, 'collateral_name', '表示名',],
                ],
                'rules'         => [
                    'collateral_code' => 'required|integer',
                    'collateral_name' => 'required|min:1',
                ],
                'types'         => [
                    'collateral_code' => 'integer',
                ],
                'flags'         => [
                    'collateral_code' => 1,
                    'collateral_name' => 1,
                ],
                'keys'          => ['collateral_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanFishery() {
        $params      = [
            'object'        => '\App\Models\Loan\Fishery',
            'join'          => [],
            'display'       => [
                'title' => '漁業形態',
                'route' => $this->route . '/LoanFishery',
                'h2'    => '漁業形態',
            ],
            'table_columns' => [
                ['row' => [['fishery_form_code', '漁業形態',],]],
                ['row' => [['fishery_form_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_fishery_form_codes.fishery_form_code' => 'asc',
            ],
            'table_search'  => [
                'fishery_form_code' => ['column_name' => 'fishery_form_code', 'display' => '漁業形態', 'type' => 'integer'],
                'fishery_form_name' => ['column_name' => 'fishery_form_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_fishery_form_codes.fishery_form_code',
                    'loan_fishery_form_codes.fishery_form_name',
                ],
                'kanji_columns' => [
                    '漁業形態',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'fishery_form_code', '漁業形態',],
                    [1, 'fishery_form_name', '表示名',],
                ],
                'rules'         => [
                    'fishery_form_code' => 'required|integer',
                    'fishery_form_name' => 'required|min:1',
                ],
                'types'         => [
                    'fishery_form_code' => 'integer',
                ],
                'flags'         => [
                    'fishery_form_code' => 1,
                    'fishery_form_name' => 1,
                ],
                'keys'          => ['fishery_form_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanFund() {
        $params      = [
            'object'        => '\App\Models\Loan\Fund',
            'join'          => [],
            'display'       => [
                'title' => '資金区分',
                'route' => $this->route . '/LoanFund',
                'h2'    => '資金区分',
            ],
            'table_columns' => [
                ['row' => [['fund_code', '資金区分',],]],
                ['row' => [['fund_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_fund_codes.fund_code' => 'asc',
            ],
            'table_search'  => [
                'fund_code' => ['column_name' => 'fund_code', 'display' => '資金区分', 'type' => 'integer'],
                'fund_name' => ['column_name' => 'fund_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_fund_codes.fund_code',
                    'loan_fund_codes.fund_name',
                ],
                'kanji_columns' => [
                    '資金区分',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'fund_code', '資金区分',],
                    [1, 'fund_name', '表示名',],
                ],
                'rules'         => [
                    'fund_code' => 'required|integer',
                    'fund_name' => 'required|min:1',
                ],
                'types'         => [
                    'fund_code' => 'integer',
                ],
                'flags'         => [
                    'fund_code' => 1,
                    'fund_name' => 1,
                ],
                'keys'          => ['fund_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanFundAuxiliary() {
        $params      = [
            'object'        => '\App\Models\Loan\FundAuxiliary',
            'join'          => [],
            'display'       => [
                'title' => '資金補助区分',
                'route' => $this->route . '/LoanFundAuxiliary',
                'h2'    => '資金補助区分',
            ],
            'table_columns' => [
                ['row' => [['fund_auxiliary_code', '資金補助区分',],]],
                ['row' => [['fund_auxiliary_category', '資金補助分類',],]],
                ['row' => [['fund_auxiliary_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_fund_auxiliary_codes.fund_auxiliary_code' => 'asc',
            ],
            'table_search'  => [
                'fund_auxiliary_code'     => ['column_name' => 'fund_auxiliary_code', 'display' => '資金補助区分', 'type' => 'integer'],
                'fund_auxiliary_category' => ['column_name' => 'fund_auxiliary_category', 'display' => '資金補助分類', 'type' => 'string'],
                'fund_auxiliary_name'     => ['column_name' => 'fund_auxiliary_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_fund_auxiliary_codes.fund_auxiliary_code',
                    'loan_fund_auxiliary_codes.fund_auxiliary_category',
                    'loan_fund_auxiliary_codes.fund_auxiliary_name',
                ],
                'kanji_columns' => [
                    '資金補助区分',
                    '資金補助分類',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'fund_auxiliary_code', '資金補助区分',],
                    [1, 'fund_auxiliary_category', '資金補助分類',],
                    [1, 'fund_auxiliary_name', '表示名',],
                ],
                'rules'         => [
                    'fund_auxiliary_code'     => 'required|integer',
                    'fund_auxiliary_category' => 'required|min:1',
                    'fund_auxiliary_name'     => 'required|min:1',
                ],
                'types'         => [
                    'fund_auxiliary_code' => 'integer',
                ],
                'flags'         => [
                    'fund_auxiliary_code'     => 1,
                    'fund_auxiliary_category' => 1,
                    'fund_auxiliary_name'     => 1,
                ],
                'keys'          => ['fund_auxiliary_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanFundUsageCode() {
        $params      = [
            'object'        => '\App\Models\Loan\FundUsageCode',
            'join'          => [],
            'display'       => [
                'title' => '資金使途区分',
                'route' => $this->route . '/LoanFundUsageCode',
                'h2'    => '資金使途区分',
            ],
            'table_columns' => [
                ['row' => [['fund_usage_code', '資金使途区分',],]],
                ['row' => [['fund_usage_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_fund_usage_codes.fund_usage_code' => 'asc',
            ],
            'table_search'  => [
                'fund_usage_code' => ['column_name' => 'fund_usage_code', 'display' => '資金使途区分', 'type' => 'integer'],
                'fund_usage_name' => ['column_name' => 'fund_usage_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_fund_usage_codes.fund_usage_code',
                    'loan_fund_usage_codes.fund_usage_name',
                ],
                'kanji_columns' => [
                    '資金使途区分',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'fund_usage_code', '資金使途区分',],
                    [1, 'fund_usage_name', '表示名',],
                ],
                'rules'         => [
                    'fund_usage_code' => 'required|integer',
                    'fund_usage_name' => 'required|min:1',
                ],
                'types'         => [
                    'fund_usage_code' => 'integer',
                ],
                'flags'         => [
                    'fund_usage_code' => 1,
                    'fund_usage_name' => 1,
                ],
                'keys'          => ['fund_usage_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanJifuriCode() {
        $params      = [
            'object'        => '\App\Models\Loan\JifuriCode',
            'join'          => [],
            'display'       => [
                'title' => '自振区分',
                'route' => $this->route . '/LoanJifuriCode',
                'h2'    => '自振区分',
            ],
            'table_columns' => [
                ['row' => [['jifuri_code', '自振区分',],]],
                ['row' => [['jifuri_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_jifuri_codes.jifuri_code' => 'asc',
            ],
            'table_search'  => [
                'jifuri_code' => ['column_name' => 'jifuri_code', 'display' => '自振区分', 'type' => 'integer'],
                'jifuri_name' => ['column_name' => 'jifuri_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_jifuri_codes.jifuri_code',
                    'loan_jifuri_codes.jifuri_name',
                ],
                'kanji_columns' => [
                    '自振区分',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'jifuri_code', '自振区分',],
                    [1, 'jifuri_name', '表示名',],
                ],
                'rules'         => [
                    'jifuri_code' => 'required|integer',
                    'jifuri_name' => 'required|min:1',
                ],
                'types'         => [
                    'jifuri_code' => 'integer',
                ],
                'flags'         => [
                    'jifuri_code' => 1,
                    'jifuri_name' => 1,
                ],
                'keys'          => ['jifuri_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanPhasedMoneyRate() {
        $params      = [
            'object'        => '\App\Models\Loan\PhasedMoneyRate',
            'join'          => [],
            'display'       => [
                'title' => '段階金利制区分',
                'route' => $this->route . '/LoanPhasedMoneyRate',
                'h2'    => '段階金利制区分',
            ],
            'table_columns' => [
                ['row' => [['phased_money_rate_code', '段階金利制区分',],]],
                ['row' => [['phased_money_rate_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_phased_money_rate_codes.phased_money_rate_code' => 'asc',
            ],
            'table_search'  => [
                'phased_money_rate_code' => ['column_name' => 'phased_money_rate_code', 'display' => '段階金利制区分', 'type' => 'integer'],
                'phased_money_rate_name' => ['column_name' => 'phased_money_rate_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_phased_money_rate_codes.phased_money_rate_code',
                    'loan_phased_money_rate_codes.phased_money_rate_name',
                ],
                'kanji_columns' => [
                    '段階金利制区分',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'phased_money_rate_code', '段階金利制区分',],
                    [1, 'phased_money_rate_name', '表示名',],
                ],
                'rules'         => [
                    'phased_money_rate_code' => 'required|integer',
                    'phased_money_rate_name' => 'required|min:1',
                ],
                'types'         => [
                    'phased_money_rate_code' => 'integer',
                ],
                'flags'         => [
                    'phased_money_rate_code' => 1,
                    'phased_money_rate_name' => 1,
                ],
                'keys'          => ['phased_money_rate_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanSecurityInstitution() {
        $params      = [
            'object'        => '\App\Models\Loan\SecurityInstitution',
            'join'          => [],
            'display'       => [
                'title' => '保証機関コード',
                'route' => $this->route . '/LoanSecurityInstitution',
                'h2'    => '保証機関コード',
            ],
            'table_columns' => [
                ['row' => [['security_institution_code', '保証機関コード',],]],
                ['row' => [['security_institution_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_security_institution_codes.security_institution_code' => 'asc',
            ],
            'table_search'  => [
                'security_institution_code' => ['column_name' => 'security_institution_code', 'display' => '保証機関コード', 'type' => 'integer'],
                'security_institution_name' => ['column_name' => 'security_institution_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_security_institution_codes.security_institution_code',
                    'loan_security_institution_codes.security_institution_name',
                ],
                'kanji_columns' => [
                    '保証機関コード',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'security_institution_code', '保証機関コード',],
                    [1, 'security_institution_name', '表示名',],
                ],
                'rules'         => [
                    'security_institution_code' => 'required|integer',
                    'security_institution_name' => 'required|min:1',
                ],
                'types'         => [
                    'security_institution_code' => 'integer',
                ],
                'flags'         => [
                    'security_institution_code' => 1,
                    'security_institution_name' => 1,
                ],
                'keys'          => ['security_institution_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanSubsidy() {
        $params      = [
            'object'        => '\App\Models\Loan\Subsidy',
            'join'          => [],
            'display'       => [
                'title' => '利子補給・助成区分',
                'route' => $this->route . '/LoanSubsidy',
                'h2'    => '利子補給・助成区分',
            ],
            'table_columns' => [
                ['row' => [['subsidy_code', '利子補給・助成区分',],]],
                ['row' => [['subsidy_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_subsidy_codes.subsidy_code' => 'asc',
            ],
            'table_search'  => [
                'subsidy_code' => ['column_name' => 'subsidy_code', 'display' => '利子補給・助成区分', 'type' => 'integer'],
                'subsidy_name' => ['column_name' => 'subsidy_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_subsidy_codes.subsidy_code',
                    'loan_subsidy_codes.subsidy_name',
                ],
                'kanji_columns' => [
                    '利子補給・助成区分',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'subsidy_code', '利子補給・助成区分',],
                    [1, 'subsidy_name', '表示名',],
                ],
                'rules'         => [
                    'subsidy_code' => 'required|integer',
                    'subsidy_name' => 'required|min:1',
                ],
                'types'         => [
                    'subsidy_code' => 'integer',
                ],
                'flags'         => [
                    'subsidy_code' => 1,
                    'subsidy_name' => 1,
                ],
                'keys'          => ['subsidy_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanSubsidyCalculation() {
        $params      = [
            'object'        => '\App\Models\Loan\SubsidyCalculation',
            'join'          => [],
            'display'       => [
                'title' => '利子補給・助成計算区分',
                'route' => $this->route . '/LoanSubsidyCalculation',
                'h2'    => '利子補給・助成計算区分',
            ],
            'table_columns' => [
                ['row' => [['subsidy_calculation_code', '利子補給・助成計算区分',],]],
                ['row' => [['subsidy_calculation_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_subsidy_calculation_codes.subsidy_calculation_code' => 'asc',
            ],
            'table_search'  => [
                'subsidy_calculation_code' => ['column_name' => 'subsidy_calculation_code', 'display' => '利子補給・助成計算区分', 'type' => 'integer'],
                'subsidy_calculation_name' => ['column_name' => 'subsidy_calculation_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_subsidy_calculation_codes.subsidy_calculation_code',
                    'loan_subsidy_calculation_codes.subsidy_calculation_name',
                ],
                'kanji_columns' => [
                    '利子補給・助成計算区分',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'subsidy_calculation_code', '利子補給・助成計算区分',],
                    [1, 'subsidy_calculation_name', '表示名',],
                ],
                'rules'         => [
                    'subsidy_calculation_code' => 'required|integer',
                    'subsidy_calculation_name' => 'required|min:1',
                ],
                'types'         => [
                    'subsidy_calculation_code' => 'integer',
                ],
                'flags'         => [
                    'subsidy_calculation_code' => 1,
                    'subsidy_calculation_name' => 1,
                ],
                'keys'          => ['subsidy_calculation_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanSubsidyInstitution() {
        $params      = [
            'object'        => '\App\Models\Loan\SubsidyInstitution',
            'join'          => [],
            'display'       => [
                'title' => '利子補給・助成機関区分',
                'route' => $this->route . '/LoanSubsidyInstitution',
                'h2'    => '利子補給・助成機関区分',
            ],
            'table_columns' => [
                ['row' => [['subsidy_institution_code', '利子補給・助成機関区分',],]],
                ['row' => [['subsidy_institution_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_subsidy_institution_codes.subsidy_institution_code' => 'asc',
            ],
            'table_search'  => [
                'subsidy_institution_code' => ['column_name' => 'subsidy_institution_code', 'display' => '利子補給・助成機関区分', 'type' => 'integer'],
                'subsidy_institution_name' => ['column_name' => 'subsidy_institution_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_subsidy_institution_codes.subsidy_institution_code',
                    'loan_subsidy_institution_codes.subsidy_institution_name',
                ],
                'kanji_columns' => [
                    '利子補給・助成機関区分',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'subsidy_institution_code', '利子補給・助成機関区分',],
                    [1, 'subsidy_institution_name', '表示名',],
                ],
                'rules'         => [
                    'subsidy_institution_code' => 'required|integer',
                    'subsidy_institution_name' => 'required|min:1',
                ],
                'types'         => [
                    'subsidy_institution_code' => 'integer',
                ],
                'flags'         => [
                    'subsidy_institution_code' => 1,
                    'subsidy_institution_name' => 1,
                ],
                'keys'          => ['subsidy_institution_code',],
            ],
        ];
        $this->param = $params;
    }

    public function getLoanFundUsage() {
        $params      = [
            'object'        => '\App\Models\Loan\FundUsage',
            'join'          => [],
            'display'       => [
                'title' => '資金用途',
                'route' => $this->route . '/LoanFundUsage',
                'h2'    => '資金用途',
            ],
            'table_columns' => [
                ['row' => [['fund_usage', '資金用途',],]],
                ['row' => [['fund_usage_name', '表示名',],]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'loan_fund_usages.fund_usage' => 'asc',
            ],
            'table_search'  => [
                'fund_usage'      => ['column_name' => 'fund_usage', 'display' => '資金用途', 'type' => 'integer'],
                'fund_usage_name' => ['column_name' => 'fund_usage_name', 'display' => '表示名', 'type' => 'string'],
            ],
            'csv'           => [
                'columns'       => [
                    'loan_fund_usages.fund_usage',
                    'loan_fund_usages.fund_usage_name',
                ],
                'kanji_columns' => [
                    '資金用途',
                    '表示名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
                    [1, 'fund_usage', '資金用途',],
                    [1, 'fund_usage_name', '表示名',],
                ],
                'rules'         => [
                    'fund_usage'      => 'required|integer',
                    'fund_usage_name' => 'required|min:1',
                ],
                'types'         => [
                    'fund_usage' => 'integer',
                ],
                'flags'         => [
                    'fund_usage'      => 1,
                    'fund_usage_name' => 1,
                ],
                'keys'          => ['fund_usage',],
            ],
        ];
        $this->param = $params;
    }

}
