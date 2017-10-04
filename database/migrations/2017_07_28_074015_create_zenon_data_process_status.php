<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZenonDataProcessStatus extends Migration
{
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'zenon_data_monthly_process_status';
    public $connect   = 'mysql_suisin';
    //本番ではconnect変更する
    public function up()
    {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("monthly_id")->index();
            $table->integer("zenon_data_csv_file_id")->index();
            $table->boolean("is_exist");
            $table->boolean("is_import");
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
