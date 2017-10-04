<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableColumnConfigs extends Migration
{
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'zenon_table_column_configs';
    public $connect   = 'mysql_suisin';
    //本番ではconnectを変更する
    public function up()
    {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("zenon_format_id")->index();
            $table->string("column_name");
            $table->string("column_type");
            $table->timestamps("");
        });
    }

    /**
     * マイグレーションを戻す
     *
     * @return void
     */
    public function down()
    {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }
}
