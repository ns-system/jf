<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreMastar extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'stores';
    public $connect   = 'mysql_master';

    public function up() {
        if (!Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
                $table->increments("id");
                $table->integer("prefecture_code");
                $table->integer("store_number")->index();
                $table->string("store_name", 255);
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
