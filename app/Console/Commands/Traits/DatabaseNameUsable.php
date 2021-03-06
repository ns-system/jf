<?php

namespace App\Console\Commands\Traits;

Trait DatabaseNameUsable
{

    public function echoMessage(bool $is_hide_message, string $message) {
        if (!$is_hide_message)
        {
            echo($message) . PHP_EOL;
        }
    }

    public function connectDatabase(string $db_env) {
        if (empty($db_env))
        {
            $this->error("エラーが発生したため処理を中断しました。");
            $this->echoMessage(false, "データベース コネクションが指定されていません。");
            exit();
        }
        elseif ($db_env !== 'testing' && $db_env !== 'mysql')
        {
            $this->error("エラーが発生したため処理を中断しました。");
            $this->echoMessage(false, "dbenvはtestingもしくはmysqlを指定してください。");
            exit();
        }
        $db_config   = \Config::get("database.connections.{$db_env}");
        $user        = $db_config['username'];
        $password    = $db_config['password'];
//      $connect_buf = "{$db_env}:host={$db_config['host']}; port={$db_config['port']};";
        $connect_buf = "mysql:host={$db_config['host']}; port={$db_config['port']};";
//        dd($connect_buf);
//      $connect_buf = "mysql:host={$db_config['host']}; dbname=mysql; port={$db_config['port']}; charset={$db_config['charset']};"; // Postgresだと色々面倒くさいことになるので簡略化した
        try {
            $pdo = new \PDO($connect_buf, $user, $password);
        } catch (\PDOException $e) {
            echo ($e->getMessage() . PHP_EOL);
            $this->echoMessage(false, "コネクション確立に失敗しました。（環境：{$db_env} コネクション：{$connect_buf} ユーザー：{$user}）");
            $this->error("エラーが発生したため処理を中断しました。");
            exit();
        }
        return $pdo;
    }

    public function getDatabaseName(string $option_option) {
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
                if (isset($db['database']) && !in_array($db['database'], $db_names))
                {
//                    echo "a";
                    $db_names[] = $db['database'];
                }else{
//                    echo "b";
                }
//                var_dump($db_names);
//                dd($db_names);
            }
//            dd($db_names);
            return $db_names;
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

    public function getNameLen(array $database_names) {
        $str_len = 0;
        foreach ($database_names as $name) {
            $str_len = ($str_len > strlen($name)) ? $str_len : strlen($name);
        }
        return $str_len;
    }

}
