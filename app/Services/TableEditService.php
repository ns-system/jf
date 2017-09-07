<?php

namespace App\Services;

//use App\Http\Requests;
use App\Services\CsvService;

class TableEditService
{

    protected $parameter;
    protected $rows      = [];
    protected $rules     = [];
    protected $file_name = '';

    public function setConfigs($config_object_name, $category) {
        $obj             = new $config_object_name;
        $this->parameter = $obj->getter($category);
        return $this;
    }

    public function getModel() {
        $model = new $this->parameter['object'];
        if (!empty($this->parameter['join']))
        {
            foreach ($this->parameter['join'] as $c) {
                $model = $model->leftJoin($c['db'], $c['left'], '=', $c['right']); //TODO:そんなSQL文で大丈夫か？
            }
        }
        return $model;
    }

    public function order($rows) {
        if (!isset($this->parameter['table_orders']))
        {
            return $rows;
        }
        $orders = $this->parameter['table_orders'];
        foreach ($orders as $key => $order) {
//            echo "<p>{$key} => {$order}</p>";
            $rows = $rows->orderBy($key, $order);
        }
        return $rows;
    }

    public function getListConfig() {
        $list_view_page_configs = [
            'title'         => $this->parameter['display']['title'],
            'brand'         => $this->parameter['display']['title'],
            'h2'            => $this->parameter['display']['h2'],
            'import_route'  => $this->parameter['display']['route'] . '/import',
            'export_route'  => $this->parameter['display']['route'] . '/export',
            'table_columns' => $this->parameter['table_columns'],
        ];
        return $list_view_page_configs;
    }

    public function getColumnName() {
        return $this->parameter['csv']['columns'];
    }

    public function getKanjiColumns() {
        return $this->parameter['csv']['kanji_columns'];
    }

    public function getTitle() {
        return $this->parameter['display']['title'];
//        return $this->parameter['csv']['titles'];
    }

    public function makeRowAndValidateRules($csv_file) {
        $column_titles   = $this->getColumnName();
        $obj             = new CsvService();
        $csv_datas       = $obj->convertCsvFileToArray($csv_file, count($column_titles))->getCsvRows();
        $cnt             = $obj->getCsvLineCount();
        $file_name       = $obj->getFileName();
        $this->file_name = $file_name;
        $conf_rule       = $this->parameter['import']['rules'];

        $rows  = [];
        $rules = [];
        foreach ($csv_datas as $key => $line) {
            $row = [];
            foreach ($line as $i => $buf) {
                $title = $column_titles[$i];
                // . の切り落とし
                if (strpos($title, '.') !== false)
                {
                    $title = mb_substr(mb_strstr($title, '.'), 1);
                }
                $row[$title] = $buf;
            }
            foreach ($conf_rule as $k => $r) {
                $rules["{$key}.{$k}"] = $r;
            }

            $rows[$key] = $row;
        }
//        var_dump($rows);
//        var_dump($rules);
//        exit();
        $this->rows  = $rows;
        $this->rules = $rules;

        return $this;
    }

//    public function setRowsAndRules(){}

    public function getImportParameter() {
        $conf = $this->getImportConfig();
        if (empty($conf))
        {
            throw new \Exception("テーブル情報がセットされていません。");
        }
        if (empty($this->rows))
        {
            throw new \Exception("データがセットされていません。");
        }
//        var_dump($this->rows);
//        exit();
        return ['rows' => $this->rows, 'configs' => $conf];
    }

    public function getValidate() {
//        try {
        if (empty($this->rows))
        {
            throw new \Exception("データがセットされていません。");
        }
        $validator = \Validator::make($this->rows, $this->rules);
//        var_dump($validator->fails());
        if ($validator->fails())
        {
            return $validator;
        }
        return true;
//        } catch (\Exception $e) {
//            echo $e->getTraceAsString();
////            echo "true";
//            throw $e;
//        }
    }

    public function getImportConfig() {
        $import_view_page_configs = [
            'title'         => '',
            'brand'         => $this->parameter['display']['title'],
            'form_route'    => $this->parameter['display']['route'] . '/upload',
            'h2'            => "CSVファイル確認<small> － {$this->file_name}</small>",
            'key'           => $this->parameter['import']['keys'],
            'table_columns' => $this->parameter['import']['table_columns'],
        ];
        return $import_view_page_configs;
    }

    public function exportRow() {
        $rows = $this
                ->getModel()
                ->get($this->getColumnName())
        ;
        $rows = $rows->toArray();
        $obj  = new CsvService();
        return $obj->exportCsv($rows, $this->getTitle() . '_' . date('Ymd_His') . '.csv', $this->getKanjiColumns());
    }

    public function updatePost($input) {
        $types = $this->parameter['import']['types'];
        $flags = $this->parameter['import']['flags'];
        $obj   = new CsvService();
        $rows  = $this
                ->swapColumnRow($input)
                ->setUpdateFlags($flags)
                ->setPrimaryKey($this->parameter['import']['keys'])
                ->getRows()
        ;
        $rows  = $obj->convertTypes($types, $rows);

        $insert_and_update_counts = $this->reflectToDataBase($this->parameter['object'], $rows);
//        var_dump($rows);
//        exit();
//        $insert_and_update_counts = $obj
//                ->swapColumnRow($input)
//                ->convertTypes($types)
//                ->setUpdateFlags($flags)
//                ->setPrimaryKey($this->parameter['import']['keys'])
//                ->reflectToDb($this->parameter['object'])
//                ->getCount()
//        ;
        return $insert_and_update_counts;
    }

    public function getIndexRoute() {
        return $this->parameter['display']['route'];
    }

    private function swapColumnRow($input, $add_columns = []) {
        $tmp_rows = [];
        $i        = 0;
        foreach ($input as $key => $row) {
            if ($key === '_token')
            {
                continue;
            }
//            echo $key;
//            var_dump($row);
//            $tmp_row = [];
            foreach ($row as $i => $column) {
                $tmp_rows[$i][$key] = $column;
                if (count($add_columns) !== 0)
                {
                    foreach ($add_columns as $add_key => $buf) {
                        $tmp_rows[$i][$add_key] = $buf;
                    }
                }
            }
        }
        $this->rows = $tmp_rows;
        return $this;
    }

    private function setUpdateFlags($update_flags) {
//        if (count($rows) === 0 && $this->rows !== [null])
//        {
        $rows     = $this->rows;
//        }
        $tmp_rows = [];
        foreach ($rows as $i => $row) {
            foreach ($row as $key => $column) {
                if (!array_key_exists($key, $update_flags))
                {
                    $tmp_rows[$i][$key] = [0, $column];
                    continue;
                }
                $tmp_rows[$i][$key] = [$update_flags[$key], $column];
            }
        }
        $this->rows = $tmp_rows;
        return $this;
    }

    private function setKeys($primary_key, $row) {
        if (!is_array($primary_key))
        {
            return [$primary_key => $row[$primary_key][1]];
        }
        $conditions = [];
        foreach ($primary_key as $pk) {
            $conditions[$pk] = $row[$pk][1];
        }
//        var_dump($conditions);
//        exit();
        return $conditions;
    }

    public function setPrimaryKey($primary_key) {
//        if (count($rows) === 0 && $this->rows !== [null])
//        {
        $rows       = $this->rows;
//        }
        $tmp_rows   = [
            'key'  => $primary_key,
            'rows' => $rows,
        ];
        $this->rows = $tmp_rows;
        return $this;
    }

    public function getRows() {
        return $this->rows;
    }

    private function reflectToDataBase($model, $rows, $is_insert_execute = true) {
//        if (count($rows) === 0 && $this->rows !== [null])
//        {
//            $rows = $this->rows;
//        }
        $primary_key = $rows['key'];
        $insert_cnt  = 0;
        $update_cnt  = 0;

        foreach ($rows['rows'] as $row) {
            $tmp        = new $model;
            $conditions = $this->setKeys($primary_key, $row);
            $is_exist   = $tmp->where($conditions)->exists();
            $table      = $tmp->firstOrNew($conditions);

            $is_insert = false;
            $is_update = false;
            foreach ($row as $key => $column) {
                if (!$is_exist)
                {
                    // データベースにデータが存在しなかった場合
                    if ($is_insert_execute === true)
                    {
                        $table[$key] = $column[1];
                        $is_insert   = true;
                    }
                }
                else
                {
                    // データベースにデータが存在した場合
                    if ($column[0] === 1)
                    {
                        $table[$key] = $column[1];
//                    echo $key."->".$column[1].' , ';
                    }
                    $is_update = true;
                }
            }
//            echo "<br>";
            if ($is_insert === true)
            {
                $insert_cnt++;
            }
            elseif ($is_update === true)
            {
                $update_cnt++;
            }

            $table->save();
        }
        return [
            'insert_count' => $insert_cnt,
            'update_count' => $update_cnt,
        ];

//        $this->insert_cnt = $insert_cnt;
//        $this->update_cnt = $update_cnt;
//        return $this;
    }

}
