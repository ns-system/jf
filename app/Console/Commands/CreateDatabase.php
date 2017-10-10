<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateDatabase extends Command
{

//    use \App\Services\Traits\JsonUsable;
    use \App\Console\Commands\Traits\DatabaseNameUsable;

    protected $signature   = 'db:create
        {--name=all : 作成したいDB名を指定。配列指定可[db_name_1,db_name_2,...]。}
        {--dbenv=testing : テスト環境->testing 本番環境->mysql}'
    ;
    protected $description = 'データベースそのものを作成。引数指定で作成するデータベースの指定が可能。設定ファイルはconfig/database.phpを使用。（driver="mysql"のものに限る）';

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
            $statement     = "CREATE DATABASE {$db_name};";
            $res           = $db->exec($statement);
            $formated_name = sprintf("%-{$str_len}s", $db_name);
            if ($res === false)
            {
                echo ("{$formated_name} : すでに存在しているか、SQL文が間違っているようです。（SQL文 ： {$statement}）" . PHP_EOL);
                $this->comment("誤りがありますが、処理は継続します。");
            }
            else
            {
                echo ("{$formated_name} : 正常に削除されました。" . PHP_EOL);
            }
        }
    }

}
