<?php

namespace App\Console\Commands\Traits;

Trait DatabaseNameUsable
{

    public function getDatabaseName(string $option_option): array {
        $option = str_replace('[', '', str_replace(']', '', $option_option));
        if (!preg_match('|^[0-9a-z_.,/?-]+$|', $option))
        {
            throw new \Exception("引数に誤りがあったようです。（引数：{$option}）");
        }

        // Case : All
        if ($option === 'all')
        {
            $db_config = \Config::get('database.connections');
            $db_names  = [];
            foreach ($db_config as $db) {
                if (!empty($db['database']) && $db['driver'] !== 'mysql')
                {
                    continue;
                }
                if (!in_array($db['database'], $db_names))
                {
                    $db_names[] = $db['database'];
                }
            }
            return $db_names;
//            dd($db_names);
        }
        // Case : String
        $tmp_option_name = explode(',', $option);
        if (!is_array($tmp_option_name))
        {
            return [$tmp_option_name];
        }

        // Case : array
        $database_names = [];
        foreach ($tmp_option_name as $name) {
            $database_names[] = $name;
        }
        return $database_names;
    }

    public function getNameLen(array $database_names): int {
        $str_len = 0;
        foreach ($database_names as $name) {
            $str_len = ($str_len > strlen($name)) ? $str_len : strlen($name);
        }
        return $str_len;
    }

}
