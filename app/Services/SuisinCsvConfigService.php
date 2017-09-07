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
            'object'        => '\App\DepositGist',
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
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
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
            'join'          => [
                ['db' => 'master_data_db.subject_codes', 'left' => 'deposit_category_codes.subject_code', 'right' => 'subject_codes.subject_code',],
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
            'object'        => '\App\DepositBankbookType',
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

//    public function getDepositBankbookCode() {
//        $params      = [
//            'object'        => '\App\DepositBankbookCode',
//            'display'       => [
//                'title' => '通証区分',
//                'route' => $this->route.'/DepositBankbookCode',
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
//                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
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
            'object'        => '\App\Industry',
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
            'object'        => '\App\Qualification',
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
            'object'        => '\App\Personality',
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
            'object'        => '\App\Prefecture',
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
            'object'        => '\App\Store',
            'join'          => [
                ['db' => 'master_data_db.prefecture_codes', 'left' => 'stores.prefecture_code', 'right' => 'prefecture_codes.prefecture_code',],
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
            'object'        => '\App\SmallStore',
            'join'          => [
                ['db' => 'master_data_db.prefecture_codes', 'left' => 'small_stores.prefecture_code', 'right' => 'prefecture_codes.prefecture_code',],
                ['db' => 'master_data_db.stores', 'left' => 'small_stores.store_number', 'right' => 'stores.store_number',],
                ['db' => 'master_data_db.control_stores', 'left' => 'small_stores.control_store_code', 'right' => 'control_stores.control_store_code',],
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
            'object'        => '\App\Area',
            'join'          => [
                ['db' => 'master_data_db.prefecture_codes', 'left' => 'area_codes.prefecture_code', 'right' => 'prefecture_codes.prefecture_code',],
                ['db' => 'master_data_db.stores', 'left' => 'area_codes.store_number', 'right' => 'stores.store_number',],
                ['db' => 'master_data_db.small_stores', 'left' => 'area_codes.small_store_number', 'right' => 'small_stores.small_store_number',],
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
                ['db' => 'master_data_db.prefecture_codes', 'left' => 'control_stores.prefecture_code', 'right' => 'prefecture_codes.prefecture_code',],
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
//                ['eloquent' =>
//                    [
//                        'model' => '\App\ConsignorGroup',
//                        'key'   => [
//                            'local_key'   => 'id',
//                            'foreign_key' => 'consignor_group_id',
//                        ],
//                        'row'   => [['group_name', 'グループ名', 'class' => 'text-left']],
//                    ]
//                ],
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
                    /* if 1 is entered, it acts as an input form, input form name, kanji_name, [format], [class] */
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

}
