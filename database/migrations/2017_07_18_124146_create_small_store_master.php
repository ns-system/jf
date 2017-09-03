<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmallStoreMaster extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public $tableName = 'small_stores';
    public $connect   = 'mysql_master';

    public function up() {
        if (!Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
                $table->increments("id");
                $table->integer("prefecture_code")->index();
                $table->integer("store_number")->index();
                $table->integer("small_store_number")->index();
                $table->integer("control_store_code")->index();
                $table->string("small_store_name", 255);
                $table->timestamps("");
            });
        }
    }

    /**
     * Reverse the migrations.
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
