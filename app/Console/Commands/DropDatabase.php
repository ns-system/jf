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
    protected $signature   = 'db:drop
        {--name=all : 削除したいDB名を指定。配列指定可[db_name_1,db_name_2,...]。}
        {--dbenv=testing : テスト環境->testing 本番環境->mysql}'
    ;
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
//        $db_env = env('DB_CONNECTION');
        $db_env = $this->option('dbenv');
        $db     = $this->connectDatabase($db_env);

//        if (empty($db_env))
//        {
//            $this->error("エラーが発生したため処理を中断しました。");
//            echo("データベース コネクションが指定されていません。") . PHP_EOL;
//            exit();
//        }
//        elseif ($db_env !== 'testing' && $db_env !== 'mysql')
//        {
//            $this->error("エラーが発生したため処理を中断しました。");
//            echo("dbenvはtestingもしくはmysqlを指定してください。") . PHP_EOL;
//            exit();
//        }
////        $this->confirm("DB環境：'{$db_env}' 処理を開始してよろしいですか？");
//        $db_config   = \Config::get("database.connections.{$db_env}");
//        $user        = $db_config['username'];
//        $password    = $db_config['password'];
////      $connect_buf = "{$db_env}:host={$db_config['host']}; port={$db_config['port']};";
//        $connect_buf = "mysql:host={$db_config['host']}; port={$db_config['port']};";
////      $connect_buf = "mysql:host={$db_config['host']}; dbname=mysql; port={$db_config['port']}; charset={$db_config['charset']};"; // Postgresだと色々面倒くさいことになるので簡略化した
//        try {
//            $db = new \PDO($connect_buf, $user, $password);
//        } catch (\PDOException $e) {
//            echo ($e->getMessage() . PHP_EOL);
//            echo "コネクション確立に失敗しました。（{$connect_buf} ユーザー：{$user}）" . PHP_EOL;
//            $this->error("エラーが発生したため処理を中断しました。");
//            exit();
//        }

        try {
            $database_names = $this->getDatabaseName($this->option('name'));
        } catch (\Exception $e) {
            echo ($e->getMessage() . PHP_EOL);
            $this->error("エラーが発生したため処理を中断しました。");
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
                echo ("データベース名が不正です。（データベース名：{$db_name}）" . PHP_EOL);
                $this->comment("処理を継続します。");
                continue;
            }
            $statement     = "DROP DATABASE {$db_name};";
            $res           = $db->exec($statement);
            $formated_name = sprintf("%-{$str_len}s", $db_name);
            if ($res === false)
            {
                echo ("{$formated_name} : 存在しないか、SQL文が間違っているようです。（SQL文 ： {$statement}）" . PHP_EOL);
                $this->comment("誤りがありますが、処理は継続します。");
            }
            else
            {
                echo ("{$formated_name} : 正常に削除されました。" . PHP_EOL);
            }
        }
    }

}
