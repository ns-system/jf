<?php

//dd(env('APP_ENV'));

$port = 3306;
if (env('APP_ENV') === 'testing')
{
    $host      = '127.0.0.1';
    $user_name = 'homestead';
    $password  = 'secret';
    $ip        = gethostbyname(gethostname());

    if (php_sapi_name() === 'cli' && $ip !== $host)
    {
        $port = 33060; /* ubuntu = 3306, local_test(vagrant) = 33060 */
    }
}
else
{
    $host      = '192.1.10.222';
    $user_name = 'homestead';
    $password  = 'secret';
//    $port      = 3306;
}
//dd($port);
//var_dump($port);

return [
    /*
      |--------------------------------------------------------------------------
      | PDO取得スタイル
      |--------------------------------------------------------------------------
      |
      | デフォルトでデータベースの結果はPHP stdClassオブジェクトのインスタンスが
      | リターンされます。しかし、ご希望であればレコードを単純な配列の形式でも
      | 取得できます。ここで取得するスタイルを調整します。
      |
     */

    'fetch'       => PDO::FETCH_CLASS,
    /*
      |--------------------------------------------------------------------------
      | デフォルトデータベース接続名
      |--------------------------------------------------------------------------
      |
      | ここでは全てのデータベース動作で用いられるデフォルトデータベース接続を
      | 指定することができます。もちろん、データベースライブラリーを使用することで
      | 多くの接続を一度に使うことができます。
      |
     */
    'default'     => env('DB_CONNECTION', 'mysql'),
    /*
      |--------------------------------------------------------------------------
      | データベース接続
      |--------------------------------------------------------------------------
      |
      | ここではアプリケーションで用いる各データベース接続を設定します。
      | もちろん、以下はLaravelでサポートされているデータベースシステムの
      | サンプル設定で、簡単に開発ができることを示すため設置してあります。
      |
      |
      | Laravelで動作する全てのデータベースはPHP PDO機能上で動作します。
      | ですから開発を始める前に選択したデータベースのドライバーが開発機に
      | インストールされていることを確認してください。
      |
     */
    'connections' => [
        'sqlite'         => [
            'driver'   => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix'   => '',
        ],
        'mysql'          => [
            'driver'    => 'mysql',
            'host'      => $host/* env('DB_HOST', 'localhost') */,
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'port'      => 3306/* $port */,
        ],
        'mysql_zenon'    => [
            'driver'    => 'mysql',
            'host'      => $host,
            'database'  => 'zenon_db',
            'username'  => $user_name,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'port'      => $port,
        ],
        'mysql_master'   => [
            'driver'    => 'mysql',
            'host'      => $host,
            'database'  => 'master_db',
            'username'  => $user_name,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'port'      => $port,
        ],
        'mysql_laravel'  => [
            'driver'    => 'mysql',
            'host'      => $host,
            'database'  => 'laravel_db',
            'username'  => $user_name,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'port'      => $port,
        ],
        'mysql_suisin'   => [
            'driver'    => 'mysql',
            'host'      => $host,
            'database'  => 'suisin_db',
            'username'  => $user_name,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'port'      => $port,
        ],
        'mysql_sinren'   => [
            'driver'    => 'mysql',
            'host'      => $host,
            'database'  => 'sinren_db',
            'username'  => $user_name,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'port'      => $port,
        ],
        'mysql_roster'   => [
            'driver'    => 'mysql',
            'host'      => $host,
            'database'  => 'roster_db',
            'username'  => $user_name,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'port'      => $port,
        ],
        'mysql_nikocale' => [
            'driver'    => 'mysql',
            'host'      => $host,
            'database'  => 'nikocale_db',
            'username'  => $user_name,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'port'      => $port,
        ],
        'pgsql'          => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ],
        'sqlsrv'         => [
            'driver'   => 'sqlsrv',
            'host'     => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
        ],
        'testing'        => [
            'driver'    => 'mysql',
            'host'      => $host,
            'database'  => 'nikocale_db',
            'username'  => $user_name,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'port'      => $port,
            'options'   => [
                PDO::ATTR_PERSISTENT => true,
            ],
        ],
    ],
    /*
      |--------------------------------------------------------------------------
      | マイグレーションリポジトリテーブル
      |--------------------------------------------------------------------------
      |
      | こで指定したテーブルに、アプリケーションで実行済みの全マイグレーション
      | 情報が保存されます。この情報を使用することで、ディスク上の
      | どのマイグレーションが未実行なのかを判断することができます。
      |
     */
    'migrations'  => 'migrations',
    /*
      |--------------------------------------------------------------------------
      | Redisデータベース
      |--------------------------------------------------------------------------
      |
      | Redisはオープンソースで、早く、進歩的なキー／値保存システムであり
      | APCやMemecachedのような典型的なキー／値システムよりも、豊富なコマンドが
      | 用意されています。Laravelはこれを使用しやすくします。
      |
     */
    'redis'       => [
        'cluster' => false,
        'default' => [
            'host'     => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', 6379),
            'database' => 0,
        ],
    ],
];
