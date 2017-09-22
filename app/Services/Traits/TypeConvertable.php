<?php

namespace App\Services\Traits;

trait TypeConvertable
{

    use DateUsable;

    public function convertType($type, $column) {

        switch ($type) {
            case 'integer':
            case 'bigInteger':
                if (!is_numeric($column))
                {
                    throw new \Exception("値が数字型ではありません。（引数：{$column}）");
                }
                return (int) $column;

            case 'float':
            case 'double':
                if (!is_numeric($column))
                {
                    throw new \Exception("値が数字型ではありません。（引数：{$column}）");
                }
                return (double) $column;

            case 'date':
                if (!$this->isDate($column))
                {
                    throw new \Exception("値が日付型ではありません。（引数：{$column}）");
                }
                $obj   = $this->setDate($column);
                return (empty($obj)) ? null : $obj->format('Y-m-d');
            case 'time':
                $times = $this->setTime($column);
                $time  = "{$times['hour']}:{$times['min']}:{$times['sec']}";
                return $time;
            case 'dateTime':
                if (!$this->isDate($column))
                {
                    throw new \Exception("値が日付時刻型ではありません。（引数：{$column}）");
                }
                $obj = $this->setDate($column);
                return (empty($obj)) ? null : $obj->format('Y-m-d H:i:s');
            case 'boolean':
                return ($column === 'false') ? false : (bool) $column;
            default:
                return $column;
        }
    }

    public function convertTypes($types, $row) {
        $tmp_rows = [];

        foreach ($row as $key => $column) {
//            if (!array_key_exists($key, $types))
//            {
//                $tmp_rows[$key] = $column;
//                continue;
//            }
            $tmp_rows[$key] = $this->convertType($types[$key], $column);
        }
        return $tmp_rows;
    }

    public function splitSpace($buf) {
        $return = preg_replace("/^[\s　]*(.*?)[\s　]*$/u", "$1", $buf);
        return $return;
    }

}
