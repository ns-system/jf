<?php

// 各環境ごとにコピーしてこの設定ファイルを編集して使う
$host      = '127.0.0.1';
$user_name = 'homestead';
$password  = 'secret';
$charset   = 'utf8mb4';
$collation = 'utf8mb4_bin';
$port      = 3306;

// $ip   = gethostbyname(gethostname());
// $sapi = php_sapi_name();
// Laravelサーバーからアクセスした場合に設定を変える場合
// 実例：データベースの作成のみ管理者にさせたい場合などに特権ユーザーのID・パスワードを記述する
//       が、うまく動いてない
// 注意：exampleには実際の環境で利用しているパスワードをここに記述しないこと
//if ($ip === '127.0.0.1' && $sapi === 'cli')
//{
//    $user_name = 'homestead';
//    $password  = 'password';
//}
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
            'charset'   => $charset,
            'collation' => $collation,
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
            'charset'   => $charset,
            'collation' => $collation,
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
            'charset'   => $charset,
            'collation' => $collation,
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
            'charset'   => $charset,
            'collation' => $collation,
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
            'charset'   => $charset,
            'collation' => $collation,
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
            'charset'   => $charset,
            'collation' => $collation,
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
            'charset'   => $charset,
            'collation' => $collation,
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
            'charset'   => $charset,
            'collation' => $collation,
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
            'host'      => '127.0.0.1',
            'database'  => 'laravel_db',
            'username'  => 'homestead',
            'password'  => 'secret',
            'charset'   => $charset,
            'collation' => $collation,
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
