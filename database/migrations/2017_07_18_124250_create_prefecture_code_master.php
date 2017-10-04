<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrefectureCodeMaster extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'prefecture_codes';
    public $connect   = 'mysql_master';

    public function up() {
        if (!Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
                $table->increments("id");
                $table->integer("prefecture_code")->unique()->index();
                $table->string("prefecture_name", 255);
                $table->timestamps("");
            });
        }
    }

    /**
     * マイグレーションを戻す
     *
     * @return void
     */
    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
//            Schema::connection($this->connect)->drop($this->tableName);
        }
    }

}
