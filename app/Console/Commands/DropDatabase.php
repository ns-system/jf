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
    protected $signature   = 'db:reset
        {--name=all      : 削除したいDB名を指定。配列指定可[db_name_1,db_name_2,...]。}
        {--dbenv=testing : テスト環境->testing 本番環境->mysql}
        {--hide=false    : 成功/失敗メッセージ出力不要時->true。}
    ';
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
        $db_env          = $this->option('dbenv');
        $is_hide_message = ($this->option('hide') === 'true' && $db_env === 'testing') ? true : false;
        $db              = $this->connectDatabase($db_env);

        if ($db_env === 'mysql')
        {
            $this->confirm("テスト環境以外が指定されたようです。削除してもよろしいですか？");
        }

        try {
            $database_names = $this->getDatabaseName($this->option('name'));
            $str_len        = $this->getNameLen($database_names);
        } catch (\Exception $e) {
            $this->echoMessage(false, $e->getMessage());
            $this->error("エラーが発生したため処理を中断しました。");
            exit();
        }

        foreach ($database_names as $db_name) {
            if (empty($db_name))
            {
                continue;
            }
            if (!preg_match('|^[0-9a-z_.,/?-]+$|', $db_name))
            {
                $this->echoMessage($is_hide_message, "データベース名が不正です。（データベース名：{$db_name}）");
//                if (!$is_hide_message)
//                {
//                    $this->comment("処理を継続します。");
//                }
                continue;
            }
            $statement     = "DROP DATABASE {$db_name};";
            $res           = $db->exec($statement);
            $formated_name = sprintf("%-{$str_len}s", $db_name);
            if ($res === false)
            {
                $this->echoMessage($is_hide_message, "{$formated_name} : すでに存在しないか、SQL文が間違っているようです。（SQL文 ： {$statement}）");
//                if (!$is_hide_message)
//                {
//                    $this->comment("処理を継続します。");
//                }
            }
            else
            {
                $this->echoMessage($is_hide_message, "{$formated_name} : 正常に削除されました。");
            }
        }
        $this->info("処理は正常に終了しました。");
    }

}
