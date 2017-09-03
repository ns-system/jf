<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlStoreMaster extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public $tableName = 'control_stores';
    public $connect   = 'mysql_master';

    public function up() {
        if (!Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
                $table->increments("id");
                $table->integer("control_store_code")->index();
                $table->string("control_store_name", 255);
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
