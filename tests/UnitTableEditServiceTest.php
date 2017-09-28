<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\Traits\FileUploadTestable;

class UnitTableEditServiceTest extends TestCase
{

    use FileUploadTestable;

    /**
     * A basic test example.
     *
     * @return void
     */
    protected $s;

    public function __construct() {
        $this->s = new \App\Services\TableEditService();
    }

    private function setReflection($function_name) {
        $s = new \ReflectionMethod($this->s, $function_name);
        $s->setAccessible(true);
        return $s;
    }

    /**
     * @tests
     */
    public function 正常系_英語キーの分割ができる() {
        $s     = $this->setReflection('convertCsvEnColumnToKey');
        $res_1 = $s->invoke($this->s, ['db_name.key_1', 'db_name.key_2', 'db_name.key_3', 'db_name.key_4']);
        $this->assertEquals(['key_1', 'key_2', 'key_3', 'key_4'], $res_1);
    }

    /**
     * @tests
     */
    public function 正常系_CSV型変換用の配列が作れる() {
        $types = ['key_1' => 'integer', 'key_4' => 'date',];
        $keys  = ['key_1', 'key_2', 'key_3', 'key_4',];
        $s     = $this->setReflection('makeCsvValueConvertType');
        $res_1 = $s->invoke($this->s, $types, $keys);
        $res_2 = $s->invoke($this->s, null, ['key_1', 'key_2',]);
        $this->assertEquals(['key_1' => 'integer', 'key_2' => 'string', 'key_3' => 'string', 'key_4' => 'date'], $res_1);
        $this->assertEquals(['key_1' => 'string', 'key_2' => 'string',], $res_2);
    }

    /**
     * @tests
     */
    public function 異常系_CSV型変換用の配列が作れない() {
        $s = $this->setReflection('makeCsvValueConvertType');
        try {
            $res_1 = $s->invoke($this->s, null, null);
            $this->fail('例外発生なし');
        } catch (\Exception $ex) {
            $this->assertEquals('変換用カラム名が入力されていません。', $ex->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系_ページ生成用の設定情報を取得する() {
        $res_1       = $this->s->setHtmlPageGenerateConfigs('App\Services\SuisinCsvConfigService', 'DepositGist')
                ->getHtmlPageGenerateParameter()
        ;
        $expection_1 = [
            "title"        => "摘要コード",
            "brand"        => "摘要コード",
            "h2"           => "全オン・ビジネスネット摘要リスト",
            'index_route'  => '/admin/suisin/config/Suisin/DepositGist',
            'import_route' => '/admin/suisin/config/Suisin/DepositGist/import',
            'export_route' => '/admin/suisin/config/Suisin/DepositGist/export',
            'form_route'   => '/admin/suisin/config/Suisin/DepositGist/upload',
        ];
        $this->assertEquals($expection_1, $res_1);
    }

    /**
     * @tests
     */
    public function 正常系_CSVカラム名を取得する() {
        $obj         = $this->s->setHtmlPageGenerateConfigs('App\Services\SuisinCsvConfigService', 'DepositCategory');
        $res_1       = $obj->getRawEnColumns();
        $res_2       = $obj->getJpColumns();
        $res_3       = $obj->getEnColumns();
        $expection_1 = ['deposit_category_codes.subject_code', 'subject_codes.subject_name', 'deposit_category_codes.category_code', 'deposit_category_codes.category_name',];
        $expection_2 = ['科目コード', '科目名', '貯金種類コード', '貯金種類名',];
        $expection_3 = ['subject_code', 'subject_name', 'category_code', 'category_name',];
//        var_dump($res_1);
        $this->assertEquals($expection_1, $res_1);
        $this->assertEquals($expection_2, $res_2);
        $this->assertEquals($expection_3, $res_3);
    }

    /**
     * @tests
     */
    public function 異常系_設定クラスが見つからない() {
        try {
            $this->s->setHtmlPageGenerateConfigs('NotExistConfigService', 'NotExistMethod');
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("設定サービスクラスが見つかりませんでした。（クラス名：NotExistConfigService）", $e->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 異常系_設定メソッドが見つからない() {
        try {
            $this->s->setHtmlPageGenerateConfigs('App\Services\SuisinCsvConfigService', 'NotExistMethod');
            $this->fail('例外発生なし');
        } catch (\Exception $e) {
            $this->assertEquals("メソッドが見つかりませんでした。（メソッド名：getNotExistMethod）", $e->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系_CSVファイルを配列に変換できる() {
        $request_csv_file = $this->createUploadFile(storage_path() . '/tests', 'test_gist_code.csv', 'text/csv');
        $csv_object       = $this->s->setHtmlPageGenerateConfigs('App\Services\SuisinCsvConfigService', 'DepositGist')
                ->setCsvFileObject($request_csv_file)
                ->getCsvFileObject()
        ;

        $res_1 = $this->s->convertCsvFileToArray('en', true, $csv_object);
        $res_2 = $this->s->convertCsvFileToArray('jp', true, $csv_object);

        $en_keys = ['gist_code', 'display_gist', 'zenon_gist', 'keizai_gist_kanji', 'keizai_gist_half_kana', 'keizai_gist_full_kana',];
        $jp_keys = ["摘要コード", "表示摘要名", "全オン摘要", "ビジネスネット 漢字摘要", "ビジネスネット カナ摘要", "ビジネスネット ｶﾅ摘要",];

        $rows = [
            ["1", "その他自振１", "その他自振１", "", "", "",],
            ["2", "その他自振２", "その他自振２", "", "", "",]
        ];

        $expection_1 = [];
        $expection_2 = [];
        foreach ($rows as $r) {
            $expection_1[] = array_combine($en_keys, $r);
            $expection_2[] = array_combine($jp_keys, $r);
        }
        $this->assertEquals($expection_1, $res_1);
        $this->assertEquals($expection_2, $res_2);
//        var_dump($res_1);
    }

    /**
     * @tests
     */
    public function 正常系_バリデーションルールが生成できる() {
        $request_csv_file = $this->createUploadFile(storage_path() . '/tests', 'test_gist_code.csv', 'text/csv');
        $csv_object       = $this->s->setHtmlPageGenerateConfigs('App\Services\SuisinCsvConfigService', 'DepositGist')
                ->setCsvFileObject($request_csv_file)
                ->getCsvFileObject()
        ;
        $rows             = $this->s->convertCsvFileToArray('en', true, $csv_object);
        $res_1            = $this->s->makeValidationRules($rows);
        $expection_1      = ["0.gist_code" => "required|integer", "0.display_gist" => "required|min:1", "1.gist_code" => "required|integer", "1.display_gist" => "required|min:1",];
        $this->assertEquals($expection_1, $res_1);
    }

    /**
     * @tests
     */
    public function 正常系_モデルが取得できる() {
        \DB::connection('mysql_master')->beginTransaction();

        $param       = ['id' => null, 'gist_code' => 999, 'display_gist' => 'test_1', 'zenon_gist' => 'gist_2', 'keizai_gist_kanji' => '摘要3', 'keizai_gist_half_kana' => 'ﾃｷﾖｳ4', 'keizai_gist_full_kana' => 'テキヨウ5', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),];
        \App\Models\Deposit\Gist::insert($param);
        $expection_1 = \App\Models\Deposit\Gist::where('gist_code', '=', 999)->first()->toArray();
        $model       = $this->s->setHtmlPageGenerateConfigs('App\Services\SuisinCsvConfigService', 'DepositGist')->getModel();
        $res_1       = $model->where('gist_code', '=', 999)->first()->toArray();
        $this->assertEquals($expection_1, $res_1);

        \DB::connection('mysql_master')->rollback();
    }

    /**
     * @tests
     */
    public function 正常系_配列の入れ替え成功() {
        $post     = [
            'key_1' => [0 => 1, 1 => 2,],
            'key_2' => [0 => 'sam', 1 => 'mary',],
        ];
        $expect_1 = [
            0 => ['key_1' => 1, 'key_2' => 'sam',],
            1 => ['key_1' => 2, 'key_2' => 'mary',],
        ];
        $s        = $this->setReflection('swapPostColumnAndRow');
        $result_1 = $s->invoke($this->s, $post);
//        $result_1 = $this->s->swapPostColumnAndRow($post);
        $this->assertEquals($expect_1, $result_1);
    }

    /**
     * @tests
     */
    public function 正常系_プライマリキー値が取得できる() {
        $s            = $this->setReflection('getPrimaryKeyValue');
        $primary_keys = ['key_1', 'key_3',];
        $rows         = ['key_1' => 1, 'key_2' => 2, 'key_3' => 3, 'key_4' => 4];

        $res_1 = $s->invoke($this->s, $primary_keys, $rows);
        $this->assertEquals(['key_1' => 1, 'key_3' => 3], $res_1);
    }

    /**
     * @tests
     */
    public function 異常系_プライマリキー値が取得できない() {
        $s            = $this->setReflection('getPrimaryKeyValue');
        $primary_keys = ['key_1', 'key_99',];
        $rows         = ['key_1' => 1, 'key_2' => 2, 'key_3' => 3, 'key_4' => 4];

        try {
            $s->invoke($this->s, $primary_keys, $rows);
            $this->fail('予期しないエラーです。');
        } catch (\Exception $exc) {
            $this->assertEquals("キーが見つかりませんでした。（キー値：key_99）", $exc->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系_トップページ構成用の設定ファイルが取得できる() {
        $s            = $this->setReflection('getPrimaryKeyValue');
        $primary_keys = ['key_1', 'key_99',];
        $rows         = ['key_1' => 1, 'key_2' => 2, 'key_3' => 3, 'key_4' => 4];

        try {
            $s->invoke($this->s, $primary_keys, $rows);
            $this->fail('予期しないエラーです。');
        } catch (\Exception $exc) {
            $this->assertEquals("キーが見つかりませんでした。（キー値：key_99）", $exc->getMessage());
        }
    }

    /**
     * @tests
     */
    public function 正常系_ページ設定ファイルの取得に成功する() {
        $res_1 = $this->s->setHtmlPageGenerateConfigs('App\Services\SuisinCsvConfigService', 'DepositGist', 'DepositGist')->getTopPageTableSettings();
        $res_2 = $this->s->getImportSettings();

        $expect_1 = [
            ['row' => [['gist_code', '摘要コード', 'format' => '%03d']]],
            ['row' => [['display_gist', '表示摘要', 'class' => 'text-left']]],
            ['row' => [['zenon_gist', '全オン摘要', 'class' => 'text-left']]],
            ['row' => [['keizai_gist_kanji', 'ビジネスネット 漢字摘要', 'class' => 'text-left']]],
            ['row' => [['keizai_gist_full_kana', 'ビジネスネット カナ摘要', 'class' => 'text-left']]],
            ['row' => [['keizai_gist_half_kana', 'ビジネスネット ｶﾅ摘要', 'class' => 'text-left']]],
            ['row' => [['created_at', '登録日', 'class' => 'small'], ['updated_at', '更新日', 'class' => 'small'],]],
        ];

        $expect_2 = [
            'table_columns' => [
                [1, 'gist_code', '摘要コード', 'format' => '%03d'],
                [1, 'display_gist', '表示摘要', 'class' => 'text-left'],
                [1, 'zenon_gist', '全オン摘要', 'class' => 'text-left'],
                [1, 'keizai_gist_kanji', '漢字摘要', 'class' => 'text-left'],
                [1, 'keizai_gist_full_kana', 'カナ摘要', 'class' => 'text-left'],
                [1, 'keizai_gist_half_kana', 'ｶﾅ摘要', 'class' => 'text-left'],
            ],
            'rules'         => ['gist_code' => 'required|integer', 'display_gist' => 'required|min:1',],
            'types'         => ['gist_code' => 'integer',],
            'flags'         => ['display_gist' => 1, 'zenon_gist' => 1, 'keizai_gist_kanji' => 1, 'keizai_gist_half_kana' => 1, 'keizai_gist_full_kana' => 1,],
            'keys'          => ['gist_code'],
        ];

        $this->assertEquals($expect_1, $res_1);
        $this->assertEquals($expect_2, $res_2);
    }

    /**
     * @tests
     */
    public function 正常系_出力用の配列が取得できる() {
        $except_1 = ["gist_code" => 1, "display_gist" => "その他自振１", "zenon_gist" => "その他自振１", "keizai_gist_kanji" => "", "keizai_gist_half_kana" => "", "keizai_gist_full_kana" => "",];
        try {
            \DB::connection('mysql_master')->beginTransaction();
            \DB::connection('mysql_master')->table('deposit_gist_codes')->insert($except_1);
            $res_1 = $this->s->setHtmlPageGenerateConfigs('App\Services\SuisinCsvConfigService', 'DepositGist', 'DepositGist')->getExportRows();
            \DB::connection()->rollback();
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
            throw new \Exception('予期しないエラーです。');
        }
        $this->assertEquals($except_1, $res_1[0]);
    }

    /**
     * @tests
     */
    public function 正常系_データベースに反映できる() {
        $before_insert = ["gist_code" => 1, "display_gist" => "その他自振１", "zenon_gist" => "その他自振１", "keizai_gist_kanji" => "", "keizai_gist_half_kana" => "", "keizai_gist_full_kana" => "",];
        $input_rows    = [
            "gist_code"             => [1, 99999,],
            "display_gist"          => ["その他自振１", "テストデータ1",],
            "zenon_gist"            => ["その他自振１", "テストデータ2",],
            "keizai_gist_kanji"     => ["", "テストデータ3",],
            "keizai_gist_half_kana" => ["", "ﾃｽﾄﾃﾞｰﾀ4",],
            "keizai_gist_full_kana" => ["", "テストデータ5",],
        ];
        try {
            \DB::connection('mysql_master')->beginTransaction();
            \DB::connection('mysql_master')->table('deposit_gist_codes')->insert($before_insert);
            $res_1 = $this->s->setHtmlPageGenerateConfigs('App\Services\SuisinCsvConfigService', 'DepositGist', 'DepositGist')->uploadToDatabase($input_rows);
            \DB::connection()->rollback();
        } catch (\Exception $exc) {
            echo $exc->getMessage();
            echo $exc->getTraceAsString();
            throw new \Exception('予期しないエラーです。');
        }
//        dd($counts);
        $this->assertEquals(['insert_count' => 1, 'update_count' => 1], $res_1);
    }

}
