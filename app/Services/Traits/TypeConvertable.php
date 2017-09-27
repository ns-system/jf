<?php

namespace App\Services\Traits;

trait TypeConvertable
{

    use DateUsable;

    public function convertType($type, $column, $is_ceil = false) {

        switch ($type) {
            case 'integer':
            case 'bigInteger':
                if ((!empty($column) && $column !== ' '))
                {
                    if (!is_numeric($column))
                    {
                        throw new \Exception("値が数字型ではありません。（引数：'{$column}'）");
                    }
                }
                return (int) $column;

            case 'float':
            case 'double':
                if ((!empty($column) && $column !== ' '))
                {
                    if (!is_numeric($column))
                    {
                        throw new \Exception("値が数字型ではありません。（引数：'{$column}'）");
                    }
                }
                if (!$is_ceil)
                {
                    return (double) $column;
                }
                return round((double) $column, 0);

            case 'date':
                if (!$this->isDate($column))
                {
                    throw new \Exception("値が日付型ではありません。（引数：'{$column}'）");
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
                    throw new \Exception("値が日付時刻型ではありません。（引数：'{$column}'）");
                }
                $obj = $this->setDate($column);
                return (empty($obj)) ? null : $obj->format('Y-m-d H:i:s');
            case 'boolean':
                return ($column === 'false') ? false : (bool) $column;
            default:
                return $this->splitSpace($column);
        }
    }

    public function convertTypes($types, $rows, $is_ceil = false) {
        $tmp_rows = [];
//        var_dump($types);
//        var_dump($rows);

        foreach ($rows as $key => $column) {
            if (!array_key_exists($key, $types))
            {
                $tmp_rows[$key] = $column;
                continue;
            }
//            var_dump($key);
            $tmp_rows[$key] = $this->convertType($types[$key], $column, $is_ceil);
        }
//        var_dump($tmp_rows);
        return $tmp_rows;
    }

    public function splitSpace($buf) {
        $return = preg_replace("/^[\s　]*(.*?)[\s　]*$/u", "$1", $buf);
        return $return;
    }

}