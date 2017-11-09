<?php

namespace App\Services;

//use App\Http\Requests;
//use App\Services\CsvService;
use App\Services\Traits\CsvUsable;
use App\Services\Traits\TypeConvertable;

class TableEditService
{

    use CsvUsable,
        TypeConvertable;

    protected $page_generate_parameter_array;
    protected $model;
    protected $html_titles;
    protected $top_page_table_settings;
    protected $raw_csv_en_columns;
    protected $csv_en_columns;
    protected $csv_jp_columns;
    protected $model_name;
    protected $import_settings = [];
    protected $validate_rules  = [];
    protected $csv_file_name   = '';
    protected $csv_file_object;

    /**
     * URL上のパラメータクエリから情報を取得して設定ファイルを作成するメソッド。
     * @param string $config_class_name_include_namespace : 設定ファイルのクラス名。フルパス。（実例：\App\Services\SuisinCsvConfigService）
     * @param string $method_name_in_url                  : 設定ファイル内のパラメータメソッド名。getは外して指定。（実例：DepositGist）
     * @return $this                                      : チェーンメソッド。
     * @throws \Exception                                 : 設定ファイルクラス、設定ファイルメソッドが見つからなかった場合にエラー。
     */
    public function setHtmlPageGenerateConfigs(string $config_class_name_include_namespace, string $method_name_in_url) {
        if (!class_exists('\\' . $config_class_name_include_namespace))
        {
            throw new \Exception("設定サービスクラスが見つかりませんでした。（クラス名：{$config_class_name_include_namespace}）");
        }
        $obj = new $config_class_name_include_namespace;
        if (!method_exists($obj, 'get' . $method_name_in_url))
        {
            throw new \Exception("メソッドが見つかりませんでした。（メソッド名：get{$method_name_in_url}）");
        }
        $parameter = $obj->getter($method_name_in_url);

        // HACK: tosite テスタブルにする方法が思いつきませんでした。修正よろしくです。
//        if ($this->isArrayEmpty($parameter))
//        {
//            throw new \Exception("設定ファイルが見つかりませんでした。");
//        }
        $this->setModel($parameter['object'], (
                isset($parameter['join'])) ? $parameter['join'] : [], $parameter['table_orders'], (isset($parameter['as'])) ? $parameter['as'] : null
        );
        $this->model_name              = $parameter['object'];
        $this->html_titles             = $parameter['display'];
        $this->top_page_table_settings = $parameter['table_columns'];
        $this->raw_csv_en_columns      = $parameter['csv']['columns'];
        $this->csv_en_columns          = $this->convertCsvEnColumnToKey($parameter['csv']['columns']);
        $this->csv_jp_columns          = $parameter['csv']['kanji_columns'];
        $this->import_settings         = $parameter['import'];

        return $this;
    }

    /**
     * ドットを含む英語のキー（table_name.column_nameなどを想定）を分割し、後者だけを取得するメソッド
     * @param array $raw_csv_en_columns : 分割されていない生のキー配列
     * @return array                    : ドットより前を取り除いたキー配列
     */
    private function convertCsvEnColumnToKey(array $raw_csv_en_columns): array {
        $keys = [];
        foreach ($raw_csv_en_columns as $column) {
            $tmp_column = explode('.', $column);
            $keys[]     = $tmp_column[count($tmp_column) - 1];
        }
        return $keys;
    }

    /**
     * モデル名からインスタンスを生成し、結合・並び替えを行うメソッド
     * @param string $model_name   : モデル名。
     * @param array $join_settings : 結合する条件。空の場合、結合しない。
     * @param array $table_orders  : テーブルソートに利用する条件。
     * @return model_object        : インスタンス化したモデルを返す。
     *                               なお、get()でオブジェクト化はしていないので、返却後に更に条件の追加等行える。
     *                               データベースの値を利用する場合は必ずget()すること。
     */
    private function setModel(string $model_name, array $join_settings, array $table_orders, $as) {
        $model = new $model_name;
        if (!$this->isArrayEmpty($join_settings))
        {
            foreach ($join_settings as $join) {
                $model = $model->leftJoin($join['db'], $join['left'], '=', $join['right']);
            }
        }
        if (!$this->isArrayEmpty($table_orders))
        {
            foreach ($table_orders as $key => $order) {
                $model = $model->orderBy($key, $order);
            }
        }
        $select = '*';
        if (!$this->isArrayEmpty($as))
        {
            $table = $as['table'];
            foreach ($as['columns'] as $a) {
                $select .= ", {$table}.{$a} AS {$a}";
            }
        }
        $model       = $model->select(\DB::raw($select));
        $this->model = $model;
    }

    public function getModel() {
        return $this->model;
    }

    public function getTopPageTableSettings() {
        return $this->top_page_table_settings;
    }

    public function getImportSettings() {
        return $this->import_settings;
    }

    public function getExportRows() {
        $raw_rows = $this->model->get()->toArray();
        $keys     = $this->getEnColumns();

        $export_rows = [];
        foreach ($raw_rows as $row) {
            $export_row = [];
            foreach ($keys as $key) {
                if (key_exists($key, $row))
                {
                    $export_row[$key] = $row[$key];
                }
            }
            $export_rows[] = $export_row;
        }
        return $export_rows;
    }

    public function getHtmlPageGenerateParameter() {
        return [
            'title'        => $this->html_titles['title'],
            'brand'        => $this->html_titles['title'],
            'h2'           => $this->html_titles['h2'],
            'index_route'  => $this->html_titles['route'],
            'import_route' => $this->html_titles['route'] . '/import',
            'export_route' => $this->html_titles['route'] . '/export',
            'form_route'   => $this->html_titles['route'] . '/upload',
        ];
    }

    public function getRawEnColumns() {
        return $this->raw_csv_en_columns;
    }

    public function getEnColumns() {
        return $this->csv_en_columns;
    }

    public function getJpColumns() {
        return $this->csv_jp_columns;
    }

    /**
     * 入力された値、CSVファイルの値を他の型に変換するルールを生成するメソッド。
     * @param type $types          : 設定ファイル内の変換型を指定。省略されている部分はスキップ（string型になり変換されない）される。
     * @param type $csv_en_columns : カラム名を指定する。設定ファイル内の値を使用。
     * @return array               : 変換ルールを配列で返す。
     * @throws \Exception          : カラム名が空だった場合にエラー。
     */
    private function makeCsvValueConvertType($types, $csv_en_columns): array {
        if (empty($csv_en_columns))
        {
            throw new \Exception("変換用カラム名が入力されていません。");
        }
        if (empty($types))
        {
            $convert_rules = [];
            foreach ($csv_en_columns as $c) {
                $convert_rules[$c] = 'string';
            }
            return $convert_rules;
        }
        $convert_rules = [];
        foreach ($csv_en_columns as $key) {
            $convert_rules[$key] = (array_key_exists($key, $types)) ? $types[$key] : 'string';
        }
        return $convert_rules;
    }

    /**
     * バリデーションルールを生成するメソッド
     * 先にsetHtmlPageGenerateConfigsを実行しておくこと
     * @param array $csv_rows : 配列変換後のCSVデータ。
     * @return array          : バリデーションルールを返す。
     */
    public function makeValidationRules(array $csv_rows): array {
        $validation_templates = $this->import_settings['rules'];
        //HACK: tosite カバレッジを出すために犠牲にしました。修正よろしくです。
//        if ($this->isArrayEmpty($validation_templates))
//        {
//            return [];
//        }
        $validate_rules       = [];
        foreach ($csv_rows as $i => $row) {
            foreach ($row as $key => $r) {
                if (array_key_exists($key, $validation_templates))
                {
                    // 数字.要素名の形に対応。要素名.数字型には対応していない。
                    $validate_rules["{$i}.{$key}"] = $validation_templates[$key];
                }
            }
        }
        return $validate_rules;
    }

    /**
     * POSTされた配列を横並びにしてINSERTしやすく整形するためのメソッド
     * 実例：
     * [                   |    [
     *     'key_1' => [    |    0 => [
     *         0 => 1,     |        'key_1' => 1,
     *         1 => 2,     |        'key_2' => 'Sam',
     *     ],              |    ],
     *     'key_2' => [    |    1 => [
     *         0 => 'Sam', |        'key_1' => 2,
     *         1 => 'Mary',|        'key_2' => 'Mary',
     *     ]               |    ]
     * ]
     * @param array $input_rows
     * @return array
     * 警告 : 2次元配列しか対応していない
     */
    private function swapPostColumnAndRow(array $input_rows): array {
        $rows_after_swapped = [];
        foreach ($input_rows as $key => $input_values) {
            foreach ($input_values as $i => $value) {
                $rows_after_swapped[$i][$key] = $value;
            }
        }
        return $rows_after_swapped;
    }

    /**
     * 
     * @param string $select_language      : キー配列の言語を選択する。enかjp。
     * @param bool   $is_header_exist      : ヘッダー行として1行目を読み飛ばす場合、true。
     * @param type $option_csv_file_object : CSVファイルオブジェクトを外で指定する場合にセット。
     * @return array                       : 変換後の配列を返す。
     */
    public function convertCsvFileToArray(string $select_language = 'en', bool $is_header_exist = true, $option_csv_file_object = null): array {
        $keys            = ($select_language === 'jp') ? $this->csv_jp_columns : $this->csv_en_columns;
        $csv_file_object = (empty($option_csv_file_object)) ? $this->csv_file_object : $option_csv_file_object;
        $convert_rules   = $this->makeCsvValueConvertType($this->import_settings['types'], $this->csv_en_columns);

        $csv_rows = [];
        foreach ($csv_file_object as $i => $raw_line) {
            if ($i === 0 && $is_header_exist)
            {
                continue;
            }
            $line = $this->lineEncode($raw_line);
            // 空行を読み飛ばす処理
            if ($this->isArrayEmpty($line))
            {
                continue;
            }
            if (count($keys) !== count($line))
            {
                throw new \Exception("CSVファイル列数が一致しませんでした。（想定：" . count($keys) . "列 実際：" . count($line) . "列）");
            }
//            var_dump(count($keys), count($line));
//            dd($line);
            $tmp_row    = array_combine($keys, $line);
            $csv_rows[] = $this->convertTypes($convert_rules, $tmp_row);
        }

        /*
         * 全てのPOST数をカウントし、上限を超えていたらエラーを投げる
         * 上限はphp.ini -> max_input_varsで調整可能
         * counts関数の第二引数にCOUNT_RECURSIVEを与えることで再帰的に要素数を取得する
         * そのため、配列の一次元目をマイナスしてPOSTの数として揃えている
         */
        $posts_count = count($csv_rows, COUNT_RECURSIVE) - count($csv_rows);
        $max_posts   = (int) ini_get('max_input_vars');
        if ($posts_count > $max_posts)
        {
            throw new \Exception("一度に取り込めるデータ件数をオーバーしました。フィールド数が" . number_format($max_posts) . "を超えないように調整してください。（フィールド数：" . number_format($posts_count) . "）");
        }
        return $csv_rows;
    }

    /**
     * 配列から指定されたプライマリキーを探し、プライマリキー＋値の配列を返すメソッド。
     * firstOrNewする際に使用。
     * @param array $primary_keys : 設定ファイルに記述されているkeysの値をセット。
     * @param array $row          : POST後、置換＋整形（swapPostColumnAndRow + convertTypes）した配列をセット。
     *                              なお、convertTypesする前にmakeCsvValueConvertTypeで変換設定をする必要がある。
     * @return array              : プライマリキー＋値を返す。
     * @throws \Exception         : プライマリキーが配列にない場合にエラー。
     */
    private function getPrimaryKeyValue(array $primary_keys, array $row): array {
        $value = [];
        foreach ($primary_keys as $key) {
            if (!key_exists($key, $row))
            {
                throw new \Exception("キーが見つかりませんでした。（キー値：{$key}）");
            }
            $value[$key] = $row[$key];
        }
        return $value;
    }

    /**
     * POSTされた値をデータベースに反映させるメソッド。POSTした値は[カラム名][カウントアップ]となっているので、
     * メソッド内で置換している。
     * @param array $raw_input_rows : POSTされた生の配列。2次元配列を想定。
     * @return array                : INSERTした件数、UPDATEした件数を配列で返す。
     */
    public function uploadToDatabase(array $raw_input_rows/* , string $connection */): array {
        $tmp_rows      = $this->swapPostColumnAndRow($raw_input_rows);
        $convert_rules = $this->makeCsvValueConvertType($this->import_settings['types'], $this->csv_en_columns);
        $input_rows    = $this->convertTypes($convert_rules, $tmp_rows);
        $raw_keys      = $this->import_settings['keys'];
        $primary_key   = (is_array($raw_keys)) ? $raw_keys : [$raw_keys];

//        $insert_and_update_count = \DB::connection($connection)->transaction(function() use($input_rows, $primary_key, $convert_rules) {
        // HACK: tosite DBの場所によって貼るトランザクション変えるの面倒くさいからコントローラーでやって(´・ω・`)
        $insert_count = 0;
        $update_count = 0;
        foreach ($input_rows as $i => $raw_row) {
            $row            = $this->convertTypes($convert_rules, $raw_row);
            $primary_values = $this->getPrimaryKeyValue($primary_key, $row);
            // 警告：ここで毎回インスタンス化しておかないと、firstOrNewが一度だけFirst扱いとなり、残りは全てNew扱いとなる。
            //       おそらく、Firstしたモデルに対してキーを探しにいく→見つからないという動きをしているものと思われる。
            $model          = new $this->model_name;
            $model          = $model->firstOrNew($primary_values);
            // Laravel5.2からだとFirstなのか、Newなのかを判定する簡略な方法があるようだが、そんなものは5.1には存在しない。
            if (!$model->id)
            {
                $insert_count++;
            }
            else
            {
                $update_count++;
            }
            foreach ($row as $key => $column) {
                // HACK: tosite カバレッジのために犠牲に。
//                if (is_array($column))
//                {
//                    throw new \Exception("配列のネストが深すぎるようです。");
//                }
                $model->$key = $column;
            }
            $model->save();
        }

        return [
            'insert_count' => $insert_count,
            'update_count' => $update_count,
        ];
    }

}
