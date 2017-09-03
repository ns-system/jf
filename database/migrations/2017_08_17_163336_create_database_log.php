<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatabaseLog extends Migration
{
    public $tableName = 'database_logs';
    public $connect   = 'mysql_laravel';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments('id');
            $table->string('table_name');
            $table->string('table_name_kanji');
            $table->integer('user_id')->index();
            $table->timestamps('');
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }

}
