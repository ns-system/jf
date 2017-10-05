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
    protected $description = 'データベースそのものを削除します。引数指定で削除するデータベースの指定が可能。設定ファイルはconfig/database.phpを使用。（driver="mysql"のものに限る）';

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
        $db_config   = \Config::get('database.connections.mysql');
        $user        = $db_config['username'];
        $password    = $db_config['password'];
        $connect_buf = "mysql:host={$db_config['host']}; dbname=mysql; port={$db_config['port']}; charset={$db_config['charset']};";
        $db          = new \PDO($connect_buf, $user, $password);

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
            try {
                if (!preg_match('|^[0-9a-z_.,/?-]+$|', $db_name))
                {
                    throw new \Exception("データベース名が不正です。（データベース名：{$db_name}）");
                }
                $statement = "DROP DATABASE IF EXISTS {$db_name};";
                $res       = $db->exec($statement);
                if ($res === false)
                {
                    throw new \Exception("不正なSQL文 - {$statement}");
                }
                $this->info(sprintf("%-{$str_len}s", $db_name) . " : 正常に削除されました。");
            } catch (\Exception $e) {
                $this->error(sprintf("%-{$str_len}s", $db_name) . " : 削除されませんでした。（理由：{$e->getMessage()}）");
            }
        }
    }

}
