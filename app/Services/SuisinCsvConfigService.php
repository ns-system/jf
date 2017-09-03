<?php

namespace App\Services;

//use App\Http\Requests;

class SuisinCsvConfigService
{

    protected $param;

    public function getter($category) {
        $function = 'get' . \Input::get('category');
        $function = 'get' . $category;
        $this->$function();

        return $this->param;
    }

    public function getDepositGist() {
        $params      = [
            'object'        => '\App\DepositGist',
            'display'       => [
                'title' => '摘要コード',
                'route' => '/admin/suisin/Suisin/DepositGist',
                'h2'    => '全オン・ビジネスネット摘要リスト',
            ],
            'table_columns' => [
                ['row' => [['gist_code', '摘要コード', 'format' => '%03d']]],
                ['row' => [['display_gist', '表示摘要', 'class' => 'text-left']]],
                ['row' => [['zenon_gist', '全オン摘要', 'class' => 'text-left']]],
                ['row' => [['keizai_gist_kanji', 'ビジネスネット 漢字摘要', 'class' => 'text-left']]],
                ['row' => [['keizai_gist_full_kana', 'ビジネスネット カナ摘要', 'class' => 'text-left']]],
                ['row' => [['keizai_gist_half_kana', 'ビジネスネット ｶﾅ摘要', 'class' => 'text-left']]],
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
            'csv'           => [
                'columns'       => [
                    'gist_code',
                    'display_gist',
                    'zenon_gist',
                    'keizai_gist_kanji',
                    'keizai_gist_half_kana',
                    'keizai_gist_full_kana',
                ],
                'kanji_columns' => [
                    '摘要コード',
                    '表示摘要名',
                    '全オン摘要',
                    'ビジネスネット 漢字摘要',
                    'ビジネスネット カナ摘要',
                    'ビジネスネット ｶﾅ摘要',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'gist_code', '摘要コード', 'format' => '%03d'],
                    [1, 'display_gist', '表示摘要', 'class' => 'text-left'],
                    [1, 'zenon_gist', '全オン摘要', 'class' => 'text-left'],
                    [1, 'keizai_gist_kanji', '漢字摘要', 'class' => 'text-left'],
                    [1, 'keizai_gist_full_kana', 'カナ摘要', 'class' => 'text-left'],
                    [1, 'keizai_gist_half_kana', 'ｶﾅ摘要', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'gist_code'    => 'required|integer',
                    'display_gist' => 'required|min:1',
                ],
                'types'         => [
                    'gist_code' => 'integer',
                ],
                'flags'         => [
                    'display_gist'          => 1,
                    'zenon_gist'            => 1,
                    'keizai_gist_kanji'     => 1,
                    'keizai_gist_half_kana' => 1,
                    'keizai_gist_full_kana' => 1,
                ],
                'keys'          => ['gist_code'],
            ],
        ];
        $this->param = $params;
    }

    public function getDepositCategory() {
        $params      = [
            'object'        => '\App\DepositCategory',
            'display'       => [
                'title' => '貯金種類コード',
                'route' => '/admin/suisin/Suisin/DepositCategory',
                'h2'    => '貯金種類コード',
            ],
            'table_columns' => [
                ['row' => [['subject_code', '科目コード', 'format' => '%02d']]],
                ['row' => [['category_code', '貯金種類コード', 'format' => '%03d']]],
                ['eloquent' => [
                        'model' => '\App\Subject',
                        'key'   => [
                            'local_key'   => 'subject_code',
                            'foreign_key' => 'subject_code',
                        ],
                        'row'   => [['subject_name', '科目名']],
                    ]
                ],
                ['row' => [['category_name', '貯金種類名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'subject_code'  => 'asc',
                'category_code' => 'asc',
            ],
            'csv'           => [
                'columns'       => [
                    'subject_code',
                    'category_code',
                    'category_name',
                ],
                'kanji_columns' => [
                    '科目コード',
                    '貯金種類コード',
                    '貯金種類名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'subject_code', '科目コード', 'format' => '%02d'],
                    [1, 'category_code', '貯金種類コード', 'format' => '%03d'],
                    [1, 'category_name', '貯金種類名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'gist_code'    => 'required|integer',
                    'display_gist' => 'required|min:1',
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
            'object'        => '\App\DepositBankbookType',
            'display'       => [
                'title' => '通証タイプ',
                'route' => '/admin/suisin/Suisin/DepositBankbookType',
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
                    /* display_flag, kanji_name, [format], [class] */
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

//    public function getDepositBankbookCode() {
//        $params      = [
//            'object'        => '\App\DepositBankbookCode',
//            'display'       => [
//                'title' => '通証区分',
//                'route' => '/admin/suisin/Suisin/DepositBankbookCode',
//                'h2'    => '通証区分',
//            ],
//            'table_columns' => [
//                ['row' => [['subject_code', '科目コード', 'format' => '%02d']]],
//                ['row' => [['bankbook_deed_code', '通証区分', 'format' => '%02d']]],
//                ['row' => [['bankbook_deed_name', '通証区分名', 'class' => 'text-left']]],
//                ['row' =>
//                    [
//                        ['created_at', '登録日', 'class' => 'small'],
//                        ['updated_at', '更新日', 'class' => 'small'],
//                    ]
//                ],
//            ],
//            'csv'           => [
//                'columns'       => [
//                    'subject_code',
//                    'bankbook_deed_code',
//                    'bankbook_deed_name',
//                ],
//                'kanji_columns' => [
//                    '科目コード',
//                    '通証区分',
//                    '通証区分名',
//                ],
//            ],
//            'import'        => [
//                'table_columns' => [
//                    /* display_flag, kanji_name, [format], [class] */
//                    [1, 'subject_code', '科目コード', 'format' => '%02d'],
//                    [1, 'bankbook_deed_code', '通証区分', 'format' => '%02d'],
//                    [1, 'bankbook_deed_name', '通証区分名', 'class' => 'text-left'],
//                ],
//                'types'         => [
//                    'subject_code'       => 'integer',
//                    'bankbook_deed_code' => 'integer',
//                ],
//                'flags'         => [
//                    'bankbook_deed_code' => 1,
//                    'subject_code'       => 1,
//                    'bankbook_deed_name' => 1,
//                ],
//                'keys'          => ['subject_code', 'bankbook_deed_code'],
//            ],
//        ];
//        $this->param = $params;
//    }

    public function getSubject() {
        $params      = [
            'object'        => '\App\Subject',
            'display'       => [
                'title' => '科目コード',
                'route' => '/admin/suisin/Suisin/Subject',
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
                    /* display_flag, kanji_name, [format], [class] */
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
            'object'        => '\App\Industry',
            'display'       => [
                'title' => '業種コード',
                'route' => '/admin/suisin/Suisin/Industry',
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
                    /* display_flag, kanji_name, [format], [class] */
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
            'object'        => '\App\Qualification',
            'display'       => [
                'title' => '資格区分',
                'route' => '/admin/suisin/Suisin/Qualification',
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
                    /* display_flag, kanji_name, [format], [class] */
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
            'object'        => '\App\Personality',
            'display'       => [
                'title' => '人格コード',
                'route' => '/admin/suisin/Suisin/Personality',
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
                    /* display_flag, kanji_name, [format], [class] */
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
            'object'        => '\App\Prefecture',
            'display'       => [
                'title' => '県コード',
                'route' => '/admin/suisin/Suisin/Prefecture',
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
                    /* display_flag, kanji_name, [format], [class] */
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
            'object'        => '\App\Store',
            'display'       => [
                'title' => '店番',
                'route' => '/admin/suisin/Suisin/Store',
                'h2'    => '店番',
            ],
            'table_columns' => [
                ['row' => [['prefecture_code', '県コード', 'format' => '%04d']]],
                ['row' => [['store_number', '店番', 'class' => 'text-left', 'format' => '%03d']]],
                ['eloquent' =>
                    [
                        'model' => '\App\Prefecture',
                        'key'   => [
                            'local_key'   => 'prefecture_code',
                            'foreign_key' => 'prefecture_code',
                        ],
                        'row'   => [['prefecture_name', '県名']],
                    ]
                ],
                ['row' => [
                        ['store_name', '店舗名', 'class' => 'text-left']
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
                'prefecture_code' => 'asc',
                'store_number'    => 'asc',
            ],
            'csv'           => [
                'columns'       => [
                    'prefecture_code',
                    'store_number',
                    'store_name',
                ],
                'kanji_columns' => [
                    '県コード',
                    '店番',
                    '店舗名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'prefecture_code', '県コード', 'format' => '%04d'],
                    [1, 'store_number', '店番', 'format' => '%03d'],
                    [1, 'store_name', '店舗名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'prefecture_code' => 'required|integer|exists:mysql_master.prefecture_codes,prefecture_code',
                    'store_number'    => 'required|integer',
                    'store_name'      => 'required|min:1',
                ],
                'types'         => [
                    'prefecture_code' => 'integer',
                    'store_number'    => 'integer',
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
            'object'        => '\App\SmallStore',
            'display'       => [
                'title' => '店番',
                'route' => '/admin/suisin/Suisin/SmallStore',
                'h2'    => '店番',
            ],
            'table_columns' => [
                ['row' => [['prefecture_code', '県コード', 'format' => '%04d']]],
                ['row' => [['store_number', '店番', 'format' => '%03d']]],
                ['row' => [['small_store_number', '小規模店番', 'format' => '%03d']]],
                ['eloquent' =>
                    [
                        'model' => '\App\Prefecture',
                        'key'   => [
                            'local_key'   => 'prefecture_code',
                            'foreign_key' => 'prefecture_code',
                        ],
                        'row'   => [['prefecture_name', '県名']],
                    ]
                ],
                ['eloquent' =>
                    [
                        'model' => '\App\Store',
                        'key'   => [
                            'local_key'   => 'store_number',
                            'foreign_key' => 'store_number',
                        ],
                        'row'   => [['store_name', '店舗名']],
                    ]
                ],
                ['row' => [['small_store_name', '小規模店舗名', 'class' => 'text-left']]],
                ['row' => [['control_store_code', '管轄店舗']]],
                ['eloquent' =>
                    [
                        'model' => '\App\ControlStore',
                        'key'   => [
                            'local_key'   => 'control_store_code',
                            'foreign_key' => 'control_store_code',
                        ],
                        'row'   => [['control_store_name', '管轄店舗名']],
                    ]
                ],
                ['row' => [['store_name', '店舗名', 'class' => 'text-left']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'prefecture_code'    => 'asc',
                'store_number'       => 'asc',
                'small_store_number' => 'asc',
            ],
            'csv'           => [
                'columns'       => [
                    'prefecture_code',
                    'store_number',
                    'small_store_number',
                    'control_store_code',
                    'small_store_name',
                ],
                'kanji_columns' => [
                    '県コード',
                    '店番',
                    '小規模店番',
                    '管轄店舗',
                    '小規模店舗名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'prefecture_code', '県コード', 'format' => '%04d'],
                    [1, 'store_number', '店番', 'format' => '%03d'],
                    [1, 'small_store_number', '小規模店番', 'format' => '%03d'],
                    [1, 'control_store_code', '管轄店舗'],
                    [1, 'small_store_name', '小規模店舗名', 'class' => 'text-left'],
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
            'object'        => '\App\Area',
            'display'       => [
                'title' => '地区コード',
                'route' => '/admin/suisin/Suisin/Area',
                'h2'    => '地区コード',
            ],
            'table_columns' => [
                ['row' => [['prefecture_code', '県コード', 'format' => '%04d']]],
                ['row' => [['store_number', '店番', 'format' => '%03d']]],
                ['row' => [['small_store_number', '小規模店番', 'format' => '%03d']]],
                ['row' => [['area_code', '地区コード', 'format' => '%03d']]],
                ['eloquent' =>
                    [
                        'model' => '\App\Prefecture',
                        'key'   => [
                            'local_key'   => 'prefecture_code',
                            'foreign_key' => 'prefecture_code',
                        ],
                        'row'   => [['prefecture_name', '県名']],
                    ]
                ],
                ['eloquent' =>
                    [
                        'model' => '\App\Store',
                        'key'   => [
                            'local_key'   => 'store_number',
                            'foreign_key' => 'store_number',
                        ],
                        'row'   => [['store_name', '店舗名']],
                    ]
                ],
                ['eloquent' =>
                    [
                        'model' => '\App\SmallStore',
                        'key'   => [
                            'local_key'   => 'small_store_number',
                            'foreign_key' => 'small_store_number',
                        ],
                        'row'   => [['small_store_name', '小規模店舗名']],
                    ]
                ],
                ['row' => [['area_name', '地区名']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'prefecture_code'    => 'asc',
                'store_number'       => 'asc',
                'small_store_number' => 'asc',
                'area_code'          => 'asc',
            ],
            'csv'           => [
                'columns'       => [
                    'prefecture_code',
                    'store_number',
                    'small_store_number',
                    'area_code',
                    'area_name',
                ],
                'kanji_columns' => [
                    '県コード',
                    '店番',
                    '小規模店番',
                    '管轄店舗',
                    '小規模店舗名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'prefecture_code', '県コード', 'format' => '%04d'],
                    [1, 'store_number', '店番', 'format' => '%03d'],
                    [1, 'small_store_number', '小規模店番', 'format' => '%03d'],
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
            'display'       => [
                'title' => '管轄店舗',
                'route' => '/admin/suisin/Suisin/ControlStore',
                'h2'    => '管轄店舗',
            ],
            'table_columns' => [
                ['row' => [['prefecture_code', '県コード', 'format' => '%04d']]],
                ['row' => [['control_store_code', '管轄店舗コード', 'format' => '%03d']]],
                ['eloquent' =>
                    [
                        'model' => '\App\Prefecture',
                        'key'   => [
                            'local_key'   => 'prefecture_code',
                            'foreign_key' => 'prefecture_code',
                        ],
                        'row'   => [['prefecture_name', '県名']],
                    ]
                ],
                ['row' => [['control_store_name', '管轄店舗名']]],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
            ],
            'table_orders'  => [
                'prefecture_code'    => 'asc',
                'store_number'       => 'asc',
                'small_store_number' => 'asc',
                'area_code'          => 'asc',
            ],
            'csv'           => [
                'columns'       => [
                    'prefecture_code',
                    'control_store_code',
                ],
                'kanji_columns' => [
                    '県コード',
                    '管轄店舗コード',
                    '管轄店舗名',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'prefecture_code', '県コード', 'format' => '%04d'],
                    [1, 'control_store_code', '管轄店舗',],
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
            'display'       => [
                'title' => '委託者リスト',
                'route' => '/admin/suisin/Suisin/Consignor',
                'h2'    => '委託者リスト',
            ],
            'table_columns' => [
                ['row' => [['consignor_code', '委託者コード', 'format' => '%05d']]],
                ['row' => [['consignor_name', '委託者名', 'class' => 'text-left']]],
                ['row' => [['display_consignor_name', '表示委託者名', 'class' => 'text-left']]],
                ['row' => [['consignor_group_id', 'グループコード']]],
                ['eloquent' =>
                    [
                        'model' => '\App\ConsignorGroup',
                        'key'   => [
                            'local_key'   => 'id',
                            'foreign_key' => 'consignor_group_id',
                        ],
                        'row'   => [['group_name', 'グループ名', 'class' => 'text-left']],
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
                'consignor_code'     => 'asc',
                'consignor_group_id' => 'asc',
            ],
            'csv'           => [
                'columns'       => [
                    'consignor_code',
                    'consignor_name',
                    'display_consignor_name',
                    'consignor_group_id',
                ],
                'kanji_columns' => [
                    '委託者コード',
                    '委託者名',
                    '委託者表示名',
                    'グループコード',
                ],
            ],
            'import'        => [
                'table_columns' => [
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'consignor_code', '委託者コード', 'format' => '%05d'],
                    [0, 'consignor_name', '委託者名', 'class' => 'text-left'],
                    [1, 'display_consignor_name', '表示委託者名', 'class' => "text-left"],
                    [1, 'consignor_group_id', 'グループコード'],
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
            'display'       => [
                'title' => '委託者グループ',
                'route' => '/admin/suisin/Suisin/ConsignorGroup',
                'h2'    => '委託者グループ',
            ],
            'table_columns' => [
                ['row' => [['id', 'グループコード',]]],
                ['row' => [['group_name', 'グループ名', 'class' => 'text-left']]],
//                ['eloquent' =>
//                    [
//                        'model' => '\App\User',
//                        'key'   => [
//                            'local_key'   => 'id',
//                            'foreign_key' => 'create_user_id',
//                        ],
//                        'row'   => [['name', '登録者']],
//                    ]
//                ],
//                ['eloquent' =>
//                    [
//                        'model' => '\App\User',
//                        'key'   => [
//                            'local_key'   => 'id',
//                            'foreign_key' => 'modify_user_id',
//                        ],
//                        'row'   => [['name', '更新者']],
//                    ]
//                ],
                ['row' =>
                    [
                        ['created_at', '登録日', 'class' => 'small'],
                        ['updated_at', '更新日', 'class' => 'small'],
                    ]
                ],
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
                    /* display_flag, kanji_name, [format], [class] */
                    [1, 'id', 'グループコード',],
                    [1, 'group_name', 'グループ名', 'class' => 'text-left'],
                ],
                'rules'         => [
                    'id'         => 'required|integer',
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

//
//    public function getZenonCsv() {
//        $params      = [
//            'object'        => '\App\ZenonCsv',
//            'display'       => [
//                'title' => '全オン還元CSVファイル設定',
//                'route' => '/admin/suisin/Suisin/ZenonCsv',
//                'h2'    => '全オン還元CSVファイル設定',
//            ],
//            'table_columns' => [
//                ['row' => [['is_process', '処理']]],
//                ['row' => [['zenon_format_id', 'フォーマットID']]],
//                [
//                    'row' => [
//                        ['zenon_data_name', '全オンデータ名', 'class' => 'small text-left'],
//                        ['csv_file_name', 'CSVファイル名', 'class' => 'small text-left'],
//                    ]
//                ],
//                [
//                    'row' => [
//                        ['reference_return_date', '目安還元日', 'class' => 'text-left small']
//                    ]
//                ],
//                [
//                    'row' => [
//                        ['is_monthly', '月次'],
//                        ['is_daily', '日次']
//                    ]
//                ],
//                [
//                    'row' => [
//                        ['first_column_position', '開始位置'],
//                        ['last_column_position', '終了位置'],
//                        ['column_length', 'カラム長']
//                    ]
//                ],
//                [
//                    'row' => [
//                        ['database_name', 'DB名', 'class' => 'text-left small'],
//                        ['table_name', 'テーブル名', 'class' => 'text-left small'],
//                    ]
//                ],
//                [
//                    'row' => [
//                        ['is_cumulative', '累積', 'class' => 'small']
//                    ]
//                ],
//                [
//                    'row' => [
//                        ['is_account_convert', '変換', 'class' => 'small text-left'],
//                        ['subject_column_name', '変換科目名', 'class' => 'small text-left'],
//                        ['account_column_name', '変換口座名', 'class' => 'small text-left'],
//                    ]
//                ],
//                [
//                    'row' => [
//                        ['is_split', '分割', 'class' => 'small text-left'],
//                        ['split_foreign_key_1', '分割時キー1', 'class' => 'small text-left'],
//                        ['split_foreign_key_2', '分割時キー2', 'class' => 'small text-left'],
//                        ['split_foreign_key_3', '分割時キー3', 'class' => 'small text-left'],
//                        ['split_foreign_key_4', '分割時キー4', 'class' => 'small text-left'],
//                    ]
//                ],
//            ],
//            'csv'           => [
//                'columns'       => [
//                    'id',
//                    'csv_file_name',
//                    'zenon_data_type_id',
//                    'zenon_data_name',
//                    'first_column_position',
//                    'last_column_position',
//                    'column_length',
//                    'reference_return_date',
//                    'is_daily',
//                    'is_monthly',
//                    'database_name',
//                    'table_name',
//                    'is_cumulative',
//                    'is_account_convert',
//                    'is_process',
//                    'is_split',
//                    'zenon_format_id',
//                    'account_column_name',
//                    'subject_column_name',
//                    'split_foreign_key_1',
//                    'split_foreign_key_2',
//                    'split_foreign_key_3',
//                    'split_foreign_key_4',
//                ],
//                'kanji_columns' => [
//                    'No',
//                    'CSVファイル名',
//                    '全オンデータ種類',
//                    '全オンデータ名',
//                    '開始カラム位置',
//                    '終了カラム位置',
//                    'カラム長',
//                    '目安還元日',
//                    '日次フラグ',
//                    '月次フラグ',
//                    'データベース名',
//                    'テーブル名',
//                    '累積フラグ',
//                    '口座変換フラグ',
//                    '処理フラグ',
//                    'テーブル分割フラグ',
//                    '全オンフォーマットID',
//                    '変換口座カラム名',
//                    '変換科目カラム名',
//                    '分割時キー1',
//                    '分割時キー2',
//                    '分割時キー3',
//                    '分割時キー4',
//                ],
//            ],
//            'import'        => [
//                'table_columns' => [
//                    /* display_flag, kanji_name, [format], [class] */
//                    [1, 'id', 'No',],
//                    [1, 'csv_file_name', 'CSVファイル名',],
//                    [1, 'zenon_data_type_id', '全オンデータ種類',],
//                    [1, 'zenon_data_name', '全オンデータ名',],
//                    [1, 'first_column_position', '開始カラム位置',],
//                    [1, 'last_column_position', '終了カラム位置',],
//                    [1, 'column_length', 'カラム長',],
//                    [1, 'reference_return_date', '目安還元日',],
//                    [1, 'is_daily', '日次フラグ',],
//                    [1, 'is_monthly', '月次フラグ',],
//                    [1, 'database_name', 'データベース名',],
//                    [1, 'table_name', 'テーブル名',],
//                    [1, 'is_cumulative', '累積フラグ',],
//                    [1, 'is_account_convert', '口座変換フラグ',],
//                    [1, 'is_process', '処理フラグ',],
//                    [1, 'is_split', 'テーブル分割フラグ',],
//                    [1, 'zenon_format_id', '全オンフォーマットID',],
//                    [1, 'account_column_name', '変換口座カラム名',],
//                    [1, 'subject_column_name', '変換科目カラム名',],
//                    [1, 'split_foreign_key_1', '分割時キー1',],
//                    [1, 'split_foreign_key_2', '分割時キー2',],
//                    [1, 'split_foreign_key_3', '分割時キー3',],
//                    [1, 'split_foreign_key_4', '分割時キー4',],
//                ],
//                'rules'         => [
//                    'id'                    => 'required|integer',
//                    'csv_file_name'         => 'required|min:4',
//                    'zenon_data_type_id'    => 'required|integer',
//                    'first_column_position' => 'required|integer',
//                    'last_column_position'  => 'required|integer|min:1',
//                    'column_length'         => 'required|integer|min:1',
//                    'is_daily'              => 'required|boolean',
//                    'is_monthly'            => 'required|boolean',
//                    'database_name'         => 'required|min:1',
////                    'table_name'            => 'required|min:1',
//                    'is_cumulative'         => 'required|boolean',
//                    'is_account_convert'    => 'required|boolean',
//                    'is_process'            => 'required|boolean',
//                    'is_split'              => 'required|boolean',
//                    'zenon_format_id'       => 'required|integer',
//                ],
//                'types'         => [
//                    'id'                    => 'integer',
//                    'zenon_data_type_id'    => 'integer',
//                    'first_column_position' => 'integer',
//                    'last_column_position'  => 'integer',
//                    'column_length'         => 'integer',
//                    'is_daily'              => 'integer',
//                    'is_monthly'            => 'integer',
//                    'is_cumulative'         => 'integer',
//                    'is_account_convert'    => 'integer',
//                    'is_process'            => 'integer',
//                    'is_split'              => 'integer',
//                    'zenon_format_id'       => 'integer',
//                ],
//                'flags'         => [
//                    'id'                    => 1,
//                    'csv_file_name'         => 1,
//                    'zenon_data_type_id'    => 1,
//                    'zenon_data_name'       => 1,
//                    'first_column_position' => 1,
//                    'last_column_position'  => 1,
//                    'column_length'         => 1,
//                    'reference_return_date' => 1,
//                    'is_daily'              => 1,
//                    'is_monthly'            => 1,
//                    'database_name'         => 1,
//                    'table_name'            => 1,
//                    'is_cumulative'         => 1,
//                    'is_account_convert'    => 1,
//                    'is_process'            => 1,
//                    'is_split'              => 1,
//                    'zenon_format_id'       => 1,
//                    'account_column_name'   => 1,
//                    'subject_column_name'   => 1,
//                    'split_foreign_key_1'   => 1,
//                    'split_foreign_key_2'   => 1,
//                    'split_foreign_key_3'   => 1,
//                    'split_foreign_key_4'   => 1,
//                ],
//                'keys'          => ['id'],
//            ],
//        ];
//        $this->param = $params;
//    }
//
//    public function getZenonTable() {
//        $params      = [
//            'object'        => '\App\ZenonTable',
//            'display'       => [
//                'title' => 'MySQL側 全オンテーブル設定',
//                'route' => '/admin/suisin/Suisin/ZenonTable',
//                'h2'    => 'MySQL側 全オンテーブル設定',
//            ],
//            'table_columns' => [
//                ['row' => [['zenon_format_id', 'ID',],]],
//                [
//                    'eloquent' => [
//                        'model' => '\App\ZenonCsv',
//                        'key'   => [
//                            'local_key'   => 'zenon_format_id',
//                            'foreign_key' => 'zenon_format_id',
//                        ],
//                        'row'   => [
//                            ['zenon_data_name', 'データ名', 'class' => 'text-left'],
//                            ['csv_file_name', 'CSVファイル名', 'class' => 'text-left'],
//                        ]
//                    ]
//                ],
//                ['row' => [['column_name', 'カラム名',],]],
//                ['row' => [['column_type', 'カラム型',],]],
//                [
//                    'row' =>
//                    [
//                        ['created_at', '登録日', 'class' => 'small'],
//                        ['updated_at', '更新日', 'class' => 'small'],
//                    ]
//                ],
//            ],
//            'csv'           => [
//                'columns'       => [
//                    'id',
//                    'zenon_format_id',
//                    'column_name',
//                    'column_type',
//                ],
//                'kanji_columns' => [
//                    'No',
//                    'フォーマットID',
//                    'カラム名',
//                    'データ型',
//                ],
//            ],
//            'import'        => [
//                'table_columns' => [
//                    /* display_flag, kanji_name, [format], [class] */
//                    [1, 'id', 'No',],
//                    [1, 'zenon_format_id', 'フォーマットID',],
//                    [1, 'column_name', 'カラム名', 'class' => 'text-left',],
//                    [1, 'column_type', 'データ型', 'class' => 'text-left',],
//                ],
//                'rules'         => [
//                    'id'              => 'required|integer',
//                    'zenon_format_id' => 'required|exists:mysql_suisin.zenon_data_csv_files,zenon_format_id',
//                    'column_name'     => 'required|min:1',
//                    'column_type'     => 'required|min:1',
//                ],
//                'types'         => [
//                    'id'              => 'integer',
//                    'zenon_format_id' => 'integer',
//                ],
//                'flags'         => [
//                    'id'              => 1,
//                    'zenon_format_id' => 1,
//                    'column_name'     => 1,
//                    'column_type'     => 1,
//                ],
//                'keys'          => ['id'],
//            ],
//        ];
//        $this->param = $params;
//    }
//
//    public function getZenonType() {
//        $params      = [
//            'object'        => '\App\ZenonType',
//            'display'       => [
//                'title' => '全オン還元データ種類',
//                'route' => '/admin/suisin/Suisin/ZenonType',
//                'h2'    => '全オン還元データ種類',
//            ],
//            'table_columns' => [
//                ['row' => [['data_type_name', 'カテゴリ名', 'class' => 'text-left']]],
//                [
//                    'row' =>
//                    [
//                        ['created_at', '登録日', 'class' => 'small'],
//                        ['updated_at', '更新日', 'class' => 'small'],
//                    ]
//                ],
//            ],
//            'csv'           => [
//                'columns'       => [
//                    'id',
//                    'data_type_name',
//                ],
//                'kanji_columns' => [
//                    'No',
//                    'カテゴリ名',
//                ],
//            ],
//            'import'        => [
//                'table_columns' => [
//                    /* display_flag, kanji_name, [format], [class] */
//                    [1, 'id', 'No',],
//                    [1, 'data_type_name', 'データ種類名', 'class' => 'text-left',],
//                ],
//                'rules'         => [
//                    'id'             => 'required|integer',
//                    'data_type_name' => 'required|min:1',
//                ],
//                'types'         => [
//                    'id' => 'integer',
//                ],
//                'flags'         => [
//                    'id'             => 1,
//                    'data_type_name' => 1,
//                ],
//                'keys'          => ['id'],
//            ],
//        ];
//        $this->param = $params;
//    }

}
