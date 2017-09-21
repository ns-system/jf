<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuisinUsers extends Migration
{
    public $tableName = 'suisin_users';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->string('email')->index()->unique();
            $table->integer('prefecture_code')->index;
            $table->integer('store_number')->index;
            $table->integer('control_store_number')->index;
            $table->integer('division')->index();
            $table->timestamps("");
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }

}
