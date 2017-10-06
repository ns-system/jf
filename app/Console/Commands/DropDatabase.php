<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DropDatabase extends Command
{

    use \App\Console\Commands\Traits\DatabaseNameUsable;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'db:drop {--name=all : 削除したいデータベース名を指定（省略時は全て）。配列[db_name_1,db_name_2...]として指定することも可能。}';
    protected $description = 'データベースそのものを削除。引数指定で削除するデータベースの指定が可能。設定ファイルはconfig/database.phpを使用。（driver="mysql"のものに限る）';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        // Make Database Connection
        $env_db = env('DB_CONNECTION');
        if (empty($env_db))
        {
            $this->error("データベース コネクションが指定されていません。");
            exit();
        }
        $db_config   = \Config::get("database.connections.{$env_db}");
        $user        = $db_config['username'];
        $password    = $db_config['password'];
        $connect_buf = "{$env_db}:host={$db_config['host']}; port={$db_config['port']};";
//      $connect_buf = "mysql:host={$db_config['host']}; dbname=mysql; port={$db_config['port']}; charset={$db_config['charset']};"; // Postgresだと色々面倒くさいことになるので簡略化した
        try {
            $db = new \PDO($connect_buf, $user, $password);
        } catch (\PDOException $e) {
            $this->error("コネクション確立に失敗しました。（命令：{$connect_buf} ユーザー：{$user}");
            exit();
        }

        try {
            $database_names = $this->getDatabaseName($this->option('name'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            exit();
        }
        $str_len = $this->getNameLen($database_names);

        foreach ($database_names as $db_name) {
            if (empty($db_name))
            {
                continue;
            }
            if (!preg_match('|^[0-9a-z_.,/?-]+$|', $db_name))
            {
                $this->error("データベース名が不正です。（データベース名：{$db_name}）");
                continue;
            }
            $statement     = "DROP DATABASE {$db_name};";
            $res           = $db->exec($statement);
            $formated_name = sprintf("%-{$str_len}s", $db_name);
            if ($res === false)
            {
                $this->warn("{$formated_name} : 存在しないか、SQL文が間違っているようです。（SQL文 ： {$statement}）");
            }
            else
            {
                $this->info("{$formated_name} : 正常に削除されました。");
            }
        }
        \Log::info('[' . date('Y-m-d H:i:s') . '] drop end.');
    }

}
